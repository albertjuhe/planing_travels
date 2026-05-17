<?php

namespace App\Infrastructure\GpxBundle\Service;

/**
 * Simplifies a GPX file in place using the Douglas-Peucker algorithm.
 *
 * Uses geoPHP to parse/serialize, and a pure-PHP Douglas-Peucker
 * implementation to simplify (so it works without the GEOS extension).
 */
class GpxSimplifier
{
    /**
     * @param string $filePath  Absolute path to the GPX file (will be overwritten)
     * @param float  $tolerance Tolerance in degrees (~0.0001 keeps high detail, 0.001 simplifies aggressively)
     *
     * @return array{originalPoints:int,simplifiedPoints:int,originalSize:int,simplifiedSize:int}
     */
    public function simplifyFile(string $filePath, float $tolerance = 0.0001): array
    {
        if (!is_file($filePath)) {
            throw new \RuntimeException('GPX file not found: '.$filePath);
        }

        $originalSize = (int) filesize($filePath);
        $content = file_get_contents($filePath);

        $originalPoints = 0;
        $simplifiedPoints = 0;

        $xml = @simplexml_load_string($content);
        if ($xml === false) {
            throw new \RuntimeException('Invalid GPX/XML content');
        }

        // Process each <trkseg> inside each <trk>
        foreach ($xml->trk as $trk) {
            foreach ($trk->trkseg as $trkseg) {
                $points = [];
                foreach ($trkseg->trkpt as $pt) {
                    $points[] = [
                        'lat' => (float) $pt['lat'],
                        'lon' => (float) $pt['lon'],
                        'node' => $pt,
                    ];
                }
                $originalPoints += count($points);

                if (count($points) <= 2) {
                    $simplifiedPoints += count($points);
                    continue;
                }

                $keepIdx = $this->douglasPeucker($points, $tolerance);
                $simplifiedPoints += count($keepIdx);

                if (count($keepIdx) === count($points)) {
                    continue;
                }

                $keepSet = array_flip($keepIdx);
                /** @var \SimpleXMLElement $dom */
                $dom = dom_import_simplexml($trkseg);
                $children = [];
                foreach ($dom->childNodes as $child) {
                    $children[] = $child;
                }
                $i = 0;
                foreach ($children as $child) {
                    if ($child->nodeType === XML_ELEMENT_NODE && $child->localName === 'trkpt') {
                        if (!isset($keepSet[$i])) {
                            $dom->removeChild($child);
                        }
                        $i++;
                    }
                }
            }
        }

        // Also simplify <rte> route points
        foreach ($xml->rte as $rte) {
            $points = [];
            foreach ($rte->rtept as $pt) {
                $points[] = [
                    'lat' => (float) $pt['lat'],
                    'lon' => (float) $pt['lon'],
                    'node' => $pt,
                ];
            }
            $originalPoints += count($points);

            if (count($points) <= 2) {
                $simplifiedPoints += count($points);
                continue;
            }

            $keepIdx = $this->douglasPeucker($points, $tolerance);
            $simplifiedPoints += count($keepIdx);

            if (count($keepIdx) === count($points)) {
                continue;
            }

            $keepSet = array_flip($keepIdx);
            $dom = dom_import_simplexml($rte);
            $children = [];
            foreach ($dom->childNodes as $child) {
                $children[] = $child;
            }
            $i = 0;
            foreach ($children as $child) {
                if ($child->nodeType === XML_ELEMENT_NODE && $child->localName === 'rtept') {
                    if (!isset($keepSet[$i])) {
                        $dom->removeChild($child);
                    }
                    $i++;
                }
            }
        }

        $newXml = $xml->asXML();
        if ($newXml === false) {
            throw new \RuntimeException('Could not serialize simplified GPX');
        }
        file_put_contents($filePath, $newXml);
        clearstatcache(true, $filePath);

        return [
            'originalPoints'   => $originalPoints,
            'simplifiedPoints' => $simplifiedPoints,
            'originalSize'     => $originalSize,
            'simplifiedSize'   => (int) filesize($filePath),
        ];
    }

    /**
     * Douglas-Peucker algorithm. Returns the list of indices of points to keep.
     *
     * @param array<int,array{lat:float,lon:float}> $points
     *
     * @return int[]
     */
    private function douglasPeucker(array $points, float $tolerance): array
    {
        $n = count($points);
        if ($n <= 2) {
            return array_keys($points);
        }

        $keep = array_fill(0, $n, false);
        $keep[0] = true;
        $keep[$n - 1] = true;

        $stack = [[0, $n - 1]];
        while ($stack) {
            [$first, $last] = array_pop($stack);
            $maxDist = 0.0;
            $maxIdx = -1;
            for ($i = $first + 1; $i < $last; $i++) {
                $d = $this->perpendicularDistance(
                    $points[$i]['lat'], $points[$i]['lon'],
                    $points[$first]['lat'], $points[$first]['lon'],
                    $points[$last]['lat'], $points[$last]['lon']
                );
                if ($d > $maxDist) {
                    $maxDist = $d;
                    $maxIdx = $i;
                }
            }
            if ($maxIdx !== -1 && $maxDist > $tolerance) {
                $keep[$maxIdx] = true;
                $stack[] = [$first, $maxIdx];
                $stack[] = [$maxIdx, $last];
            }
        }

        $result = [];
        foreach ($keep as $idx => $k) {
            if ($k) {
                $result[] = $idx;
            }
        }
        return $result;
    }

    /**
     * Perpendicular distance from point (px,py) to segment (ax,ay)-(bx,by).
     * Coordinates are treated as 2D (lat/lon) — fine for simplification tolerance in degrees.
     */
    private function perpendicularDistance(
        float $px, float $py,
        float $ax, float $ay,
        float $bx, float $by
    ): float {
        $dx = $bx - $ax;
        $dy = $by - $ay;
        if ($dx === 0.0 && $dy === 0.0) {
            return sqrt(($px - $ax) ** 2 + ($py - $ay) ** 2);
        }
        $num = abs($dy * $px - $dx * $py + $bx * $ay - $by * $ax);
        $den = sqrt($dx * $dx + $dy * $dy);
        return $num / $den;
    }
}

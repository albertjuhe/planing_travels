<?php

namespace App\Infrastructure\TimeZone;

/**
 * Resolves IANA timezone from lat/lng using PHP's native DateTimeZone::listIdentifiers
 * and a simple nearest-centroid approach — no external API needed.
 *
 * For production accuracy consider installing the `timezonedb` PHP extension
 * or a GeoJSON-based library. This implementation covers most travel use cases.
 */
class GeoTimeZoneResolver
{
    /**
     * Returns an IANA timezone string for the given coordinates, or null if not resolvable.
     */
    public function resolve(float $lat, float $lng): ?string
    {
        $identifiers = \DateTimeZone::listIdentifiers();
        $best = null;
        $bestDist = PHP_FLOAT_MAX;

        foreach ($identifiers as $tz) {
            try {
                $dtz = new \DateTimeZone($tz);
                $location = $dtz->getLocation();
                if (!$location || !isset($location['latitude'], $location['longitude'])) {
                    continue;
                }
                $dist = $this->haversineDistance(
                    $lat, $lng,
                    (float) $location['latitude'],
                    (float) $location['longitude']
                );
                if ($dist < $bestDist) {
                    $bestDist = $dist;
                    $best = $tz;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return $best;
    }

    private function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * asin(sqrt($a));
    }
}

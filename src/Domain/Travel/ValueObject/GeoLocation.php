<?php

namespace App\Domain\Travel\ValueObject;

class GeoLocation
{
    /** @var float */
    private $lat;

    /** @var float */
    private $lng;

    /** @var float */
    private $lat0;

    /** @var float */
    private $lng0;

    /** @var float */
    private $lat1;

    /** @var float */
    private $lng1;

    /**
     * GeoLocation constructor.
     *
     * @param float $latitud
     * @param float $longitud
     * @param float $latitud0
     * @param float $longitud0
     * @param float $latitud1
     * @param float $longitud1
     */
    public function __construct(
        float $latitud,
        float $longitud,
        float $latitud0 = 0,
        float $longitud0 = 0,
        float $latitud1 = 0,
        float $longitud1 = 0
    ) {
        $this->lat = $latitud;
        $this->lng = $longitud;
        $this->lat0 = $latitud0;
        $this->lng0 = $longitud0;
        $this->lat1 = $latitud1;
        $this->lng1 = $longitud1;
    }

    /**
     * @param GeoLocation $geolocation
     *
     * @return bool
     */
    public function equal(GeoLocation $geolocation)
    {
        return $this->lng === $geolocation->lng() &&
            $this->lat === $geolocation->lat() &&
            $this->lng0 === $geolocation->lng0() &&
            $this->lat0 === $geolocation->lat0() &&
            $this->lng1 === $geolocation->lng1() &&
            $this->lat1 === $geolocation->lat1();
    }

    /**
     * @return float
     */
    public function lat(): float
    {
        return $this->lat;
    }

    /**
     * @return float
     */
    public function lng(): float
    {
        return $this->lng;
    }

    /**
     * @return float
     */
    public function lat0(): float
    {
        return $this->lat0;
    }

    /**
     * @return float
     */
    public function lng0(): float
    {
        return $this->lng0;
    }

    /**
     * @return float
     */
    public function lat1(): float
    {
        return $this->lat1;
    }

    /**
     * @return float
     */
    public function lng1(): float
    {
        return $this->lng1;
    }

    public function toArray(): array
    {
        return [
            'latitud' => $this->lat,
            'longitud' => $this->lng,
            'latitud_0' => $this->lat0,
            'longitud_0' => $this->lng0,
            'latitud_1' => $this->lat1,
            'longitud_0' => $this->lng1,
        ];
    }
}

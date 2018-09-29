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
     * @param float $latitud
     * @param float $longitud
     * @param float $latitud0
     * @param float $longitud0
     * @param float $latitud1
     * @param float $longitud1
     */
    public function __construct(float $latitud,float $longitud,float $latitud0,float $longitud0,float $latitud1,float $longitud1)
    {
        $this->lat = $latitud;
        $this->lng = $longitud;
        $this->lat0= $latitud0;
        $this->lng0 = $longitud0;
        $this->lat1 = $latitud1;
        $this->lng1 = $longitud1;
    }

    /**
     * @param GeoLocation $geolocation
     * @return bool
     */
    public function equal(GeoLocation $geolocation) {
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

    /**
     * @param float $lat
     */
    public function setLat(float $lat): void
    {
        $this->lat = $lat;
    }

    /**
     * @param float $lng
     */
    public function setLng(float $lng): void
    {
        $this->lng = $lng;
    }

    /**
     * @param float $lat0
     */
    public function setLat0(float $lat0): void
    {
        $this->lat0 = $lat0;
    }

    /**
     * @param float $lng0
     */
    public function setLng0(float $lng0): void
    {
        $this->lng0 = $lng0;
    }

    /**
     * @param float $lat1
     */
    public function setLat1(float $lat1): void
    {
        $this->lat1 = $lat1;
    }

    /**
     * @param float $lng1
     */
    public function setLng1(float $lng1): void
    {
        $this->lng1 = $lng1;
    }


}
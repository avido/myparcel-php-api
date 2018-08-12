<?php

namespace Mvdnbrk\MyParcel\Resources;

class PickupLocation extends Address
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $distance;

    /**
     * @var string
     */
    public $location_code;

    /**
     * @var array
     */
    public $opening_hours;

    /**
     * @var string
     */
    public $phone;

    /**
     * @var float
     */
    public $latitude;

    /**
     * @var float
     */
    public $longitude;

    public function distanceForHumans()
    {
        if ($this->distance >= 10000) {
            return round($this->distance / 1000, 0) . ' km';
        }

        if ($this->distance >= 1000) {
            return round($this->distance / 1000, 1) . ' km';
        }

        return "{$this->distance} meter";
    }

    public function toArray()
    {
        return array_merge(
            $this->attributesToArray(),
            ['distance' => $this->distanceForHumans()]
        );
    }
}

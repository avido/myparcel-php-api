<?php

namespace Mvdnbrk\MyParcel\Resources;

class DeliveryMoment extends BaseResource
{
    /** @var \DateTime */
    public $start;

    /** @var \DateTime */
    public $end;

    public function setStartAttribute($value)
    {
        if ($value instanceof \stdClass) {
            $this->start = new \DateTime(
                $value->date,
                new \DateTimeZone($value->timezone ?? null)
            );
        }
    }

    public function setEndAttribute($value)
    {
        if ($value instanceof \stdClass) {
            $this->end = new \DateTime(
                $value->date,
                new \DateTimeZone($value->timezone ?? null)
            );
        }
    }
}

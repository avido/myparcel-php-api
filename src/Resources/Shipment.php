<?php

namespace Mvdnbrk\MyParcel\Resources;

class Shipment extends Parcel
{
    /** @var int */
    public $id;

    /** @var string */
    public $barcode;

    /** @var string */
    public $created;

    /** @var int */
    public $status;

    /** @var Money */
    public $price;

    public function setPriceAttribute($value): void
    {
        if ($value instanceof Money) {
            $this->price = $value;
        } else {
            $this->price = new Money($value);
        }
    }
}

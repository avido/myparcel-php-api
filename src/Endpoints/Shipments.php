<?php

namespace Mvdnbrk\MyParcel\Endpoints;

use Mvdnbrk\MyParcel\Resources\Parcel;
use Mvdnbrk\MyParcel\Resources\Shipment as ShipmentResource;

class Shipments extends BaseEndpoint
{
    /**
     * Create a new shipment for a parcel.
     *
     * @param  \Mvdnbrk\MyParcel\Resources\Parcel  $parcel
     * @return \Mvdnbrk\MyParcel\Resources\Shipment
     */
    public function create(Parcel $parcel)
    {
        $shipment = $this->concept($parcel);

        return $shipment;
    }

    /**
     * Create a new concept shipment for a parcel.
     *
     * @param  \Mvdnbrk\MyParcel\Resources\Parcel  $parcel
     * @return \Mvdnbrk\MyParcel\Resources\Shipment
     */
    public function concept(Parcel $parcel)
    {
        $response = $this->performApiCall(
            'POST',
            'shipments',
            $this->getHttpBody($parcel),
            ['Content-Type' => 'application/vnd.shipment+json; charset=utf-8']
        );

        return new ShipmentResource(array_merge([
            'id' => $response->data->ids[0]->id,
        ], $parcel->toArray()));
    }

    /**
     * Get a shipment by the Id that was assigned by MyParcel upon creation.
     * Delete a shipment.
     *
     * @param  int $id
     * @return bool
     */
    public function delete($id)
    {
        $response = $this->performApiCall(
            'DELETE',
            'shipments/'.$id
        );

        return true;
    }

    /**
     * Get a shipment by id.
     *
     * @param  int $id
     * @return \Mvdnbrk\MyParcel\Resources\Shipment
     */
    public function get($id)
    {
        $response = $this->performApiCall(
            'GET',
            'shipments/'.$id
        );

        return new ShipmentResource(
            collect($response->data->shipments[0])->all()
        );
    }

    /**
     * Get a shipment by your own reference.
     *
     * @param  string $value
     * @return \Mvdnbrk\MyParcel\Resources\Shipment
     */
    public function getByReference($value)
    {
        $response = $this->performApiCall(
            'GET',
            'shipments' . $this->buildQueryString(['reference_identifier' => $value])
        );

        return new ShipmentResource(
            collect($response->data->shipments[0])->all()
        );
    }

    /**
     * Get the http body for the API request.
     *
     * @param  \Mvdnbrk\MyParcel\Resources\Parcel  $parcel
     * @return string
     */
    public function getHttpBody(Parcel $parcel)
    {
        return json_encode([
            'data' => [
                'shipments' => [
                    json_decode($parcel->toJson())
                ]
            ],
        ]);
    }
}

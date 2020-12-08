<?php

namespace Mvdnbrk\MyParcel\Tests\Feature\Endpoints;

use Mvdnbrk\MyParcel\Resources\DeliveryMoment;
use Mvdnbrk\MyParcel\Resources\Parcel;
use Mvdnbrk\MyParcel\Resources\Shipment;
use Mvdnbrk\MyParcel\Tests\TestCase;

/** @group integration */
class TrackTraceTest extends TestCase
{
    private function cleanUp(Shipment $shipment): bool
    {
        return $this->client->shipments->delete($shipment);
    }

    private function validRecipient(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'street' => 'Poststraat',
            'number' => '1',
            'postal_code' => '1234AA',
            'city' => 'Amsterdam',
            'cc' => 'NL',
        ], $overrides);
    }

    /** @test */
    public function get_track_and_trace_information_by_shipment_id()
    {
        $parcel = new Parcel([
            'recipient' => $this->validRecipient(),
        ]);

        $shipment = $this->client->shipments->concept($parcel);

        $tracktrace = $this->client->tracktrace->get($shipment->id);

        $this->assertSame([], $tracktrace->history);

        $this->assertTrue($this->cleanUp($shipment));
    }

    /** @test */
    public function get_track_and_trace_information_by_shipment_object()
    {
        $parcel = new Parcel([
            'recipient' => $this->validRecipient(),
        ]);

        $shipment = $this->client->shipments->concept($parcel);

        $tracktrace = $this->client->tracktrace->get($shipment);

        $this->assertSame([], $tracktrace->history);

        $this->assertTrue($this->cleanUp($shipment));
    }

    /** @test */
    public function get_track_and_trace_information_with_expected_delivery_by_shipment_id()
    {
        $parcel = new Parcel([
            'recipient' => $this->validRecipient(),
        ]);

        $shipment = $this->client->shipments->concept($parcel);

        $tracktrace = $this->client->tracktrace->get($shipment->id, true);
        $this->assertInstanceOf(DeliveryMoment::class, $tracktrace->delivery_moment);
        $this->assertSame([], $tracktrace->history);

        $this->assertTrue($this->cleanUp($shipment));
    }


    /** @test */
    public function getting_track_and_trace_information_with_an_invalid_id_should_throw_an_error()
    {
        $this->expectException(\Mvdnbrk\MyParcel\Exceptions\MyParcelException::class);
        $this->expectExceptionMessage('Error executing API call (3001) : Permission Denied.');

        $this->client->tracktrace->get('9999999999');
    }
}

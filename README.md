# MyParcel API client for PHP

![PHP version][ico-php-version]
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Tests][ico-tests]][link-tests]
[![StyleCI][ico-code-style]][link-code-style]
[![Total Downloads][ico-downloads]][link-downloads]

[MyParcel](https://www.myparcel.nl) makes sending packages easy.

[MyParcel API documentation](https://myparcelnl.github.io/api/)

## Requirements

To use the MyParcel API client, the following things are required:

* Get a free [MyParcel account](https://backoffice.myparcel.nl/registration)
* Generate your [API Key](https://backoffice.myparcel.nl/settings)
* Now you're ready to use the MyParcel API client

## Installation

You can install the package via composer:

``` bash
composer require mvdnbrk/myparcel-php-api
```

## Getting started

Initialize the MyParcel client and set your API key:

``` php
$myparcel = new \Mvdnbrk\MyParcel\Client();

$myparcel->setApiKey('your-api-key');
```

### Create a parcel

``` php
$parcel = new \Mvdnbrk\MyParcel\Resources\Parcel([
    'reference' => 'your own reference for the parcel',
    'recipient' => [
        'first_name' => 'John',
        'last_name' => 'Doe'
        'street' => 'Poststraat',
        'number' => '1',
        'number_suffix' => 'A',
        'postal_code' => '1234AA',
        'city' => 'Amsterdam',
        'cc' => 'NL',
    ]
]);
```

### Create a shipment

``` php
$shipment = $myparcel->shipments->create($parcel);

// Get the `id` of the shipment. You may save this value for later reference.
$shipment->id;
```

You have created your first shipment!

### Create multiple shipments
``` php
$parcelsCollection = new ParcelCollection(); 
$parcelsCollection->add($parcel);
$parcelsCollection->add($anotherParcel);
$shipmentsCollection = $myparcel->shipments->createbatch($parcelsCollection);

// Get the `id` of the shipments. You may save this value for later reference.
foreach ($shipmentsCollection->collection as $shipment) {
    $shipment->id;
}
```

### Retrieve a label

A label can be retrieved by using `$shipment->id`. This will return a label in A6 format as a string.

```php
$myparcel->labels->get($shipment->id);
```

Or you may pass the `Shipment` instance directly to this method:

```php
$myparcel->labels->get($shipment);
```

Or you may pass an `array` with shipment ids or instances to retrieve multiple labels. \
**note:** the labels will be merged to a single pdf.
```php
$myparcel->labels->get([
    $shipment->id, 
    $shipmentInstance
]);
```


The label format is A6 by default, you may change this by calling the `setFormatA4` method:

```php
$myparcel->labels->setFormatA4()->get($shipment);
```


### Setting delivery options for a parcel

You can set delivery options for a parcel by passing in the options directly when you create a parcel:

``` php
$parcel = new \Mvdnbrk\MyParcel\Resources\Parcel([
    ...
    'recipient' => [
        ...
    ],
    'options' => [
        'description' => 'Description on the label',
        'signature' => true,
        ...
    ],
]);
```

Or you may use a method like `signature`, `onlyRecipient`, `returnToSender`, `ageCheck`, `insurance` and `labelDescription`.
You may call any of these methods after constructing the parcel:

``` php
$parcel->labelDescription('Your description.')
       ->ageCheck()
       ->insurance(25000)
       ->onlyRecipient()
       ->returnToSender()
       ->signature();
```
**Note:** The insurance value should be in cents


**Mailbox package**

This package type is only available for shipments in the Netherlands that fit in a standard mailbox.

``` php
$parcel->mailboxpackage();
```

### Send a parcel to a service point

You may send a parcel to a PostNL service point where a customer can pick up the parcel:

``` php
$parcel = new \Mvdnbrk\MyParcel\Resources\Parcel([
    'recipient' => [
        ...
    ],
    'pickup' => [
        'name' => 'Name of the location',
        'street' => 'Poststraat',
        'number' => '1',
        'postal_code' => '1234AA',
        'city' => 'Amsterdam',
        'cc' => 'NL,
    ]
]);
```

### Retrieve service points

```php
$servicepoints = $myparcel->servicePoints->setPostalcode('1234AA')->setHousenumber('1')->get();
```

This will return a collection of `ServicePoint` objects:

```php
$servicepoints->each(function ($item) {
    $item->id;
    $item->name;
    $item->latitude;
    $item->longitude;
    $item->distance;
    $item->distanceForHumans();
    $item->opening_hours;
});
```

### Get a shipment

You can get a shipment by `id` or your own reference.

``` php
$shipment = $myparcel->shipments->get($id);

$shipment = $myparcel->shipments->getByReference('your own reference');

// Get the barcode for the shipment:
$shipment->barcode;

// Get the current status:
$shipment->status;
```

### Track a shipment

You can get detailed track and trace information for a shipment.

``` php
$tracktrace = $myparcel->tracktrace->get($id);

// Links:
$tracktrace->link;
$tracktrace->link_portal;

// Check if the shipment is delivered:
$tracktrace->isDelivered;

// Get current state of the shipment:
$tracktrace->code;
$tracktrace->description;
$tracktrace->datetime;

// Get all traces for the shipment, this will return a collection with
// all traces in descending order, including the current state:
$tracktrace->items;

// Convert all items to an array:
$tracktrace->items->all()
```

## Webhooks subscriptions

### Get current subscriptions
```php
$subscriptions = $myparcel->webhooks->list();
foreach ($subscriptions as $subscription) {
    // subscription id
    $subscription->id;
    // subscription hook
    $subscription->hook;
    // subscription url
    $subscription->url;
    // subscription account id
    $subscription->account_id;
    // subscription shop id
    $subscription->shop_id;
}
```

### Get Subscription
Get subscription by id or Webhook resource
```php
// by id
$subscription = $myparcel->webhooks->get($id);
// by resource
$subscription = $myparcel->webhooks->get($resource);
// subscription id
$subscription->id;
// subscription hook
$subscription->hook;
// subscription url
$subscription->url;
// subscription account id
$subscription->account_id;
// subscription shop id
$subscription->shop_id;
```

### Add subscription
Url must be `https`
```php
$subscription = new \Mvdnbrk\MyParcel\Resources\Webhook([
    'hook' => \Mvdnbrk\MyParcel\Types\HookType::SHIPMENT_STATUS_CHANGED,
    'url' => 'https://www.notify-url.com'
]);
$myparcel->webhooks->add($subscription);
```

### Delete subscription
Delete subscription by id or Webhook resource
```php
$myparcel->webhooks->delete($id);
```

## Usage with Laravel

You may incorporate this package in your Laravel application by using [this package](https://github.com/mvdnbrk/laravel-dhlparcel).

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Mark van den Broek][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-php-version]: https://img.shields.io/packagist/php-v/mvdnbrk/myparcel-php-api?style=flat-square
[ico-version]: https://img.shields.io/packagist/v/mvdnbrk/myparcel-php-api.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-tests]: https://img.shields.io/github/workflow/status/mvdnbrk/myparcel-php-api/tests/main?label=tests&style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/mvdnbrk/myparcel-php-api.svg?style=flat-square
[ico-code-style]: https://styleci.io/repos/72292364/shield?branch=main

[link-packagist]: https://packagist.org/packages/mvdnbrk/myparcel-php-api
[link-tests]: https://github.com/mvdnbrk/myparcel-php-api/actions?query=workflow%3Atests
[link-downloads]: https://packagist.org/packages/mvdnbrk/myparcel-php-api
[link-author]: https://github.com/mvdnbrk
[link-contributors]: ../../contributors
[link-code-style]: https://styleci.io/repos/72292364

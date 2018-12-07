# AmazonGiftCode

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

AmazonGiftCode is Laravel package for Amazon Gift Codes On Demand (AGCOD). Integration for Amazon Incentive API. Read more at [https://developer.amazon.com/amazon-incentives-api](https://developer.amazon.com/amazon-incentives-api) 

This package will give you a simplest APIs to Create/Cancel Amazon Gift Code On Demand.


## Installation

You can install this package via Composer.

``` bash
$ composer require kamerk22/amazongiftcode
```

Set the following Environment Variable in `.env` file.
```bash
GIFT_CARD_ENDPOINT=https://agcod-v2-gamma.amazon.com
GIFT_CARD_KEY=AWS_ACCESS_KEY
GIFT_CARD_SECRET=AWS_SECRET
GIFT_CARD_PARTNER_ID=AWS_PARTNER_ID
```

The package will register itself automatically.
Optionally publish config file of package
 ```bash
$ php artisan vendor:publish --provider="kamerk22\AmazonGiftCode\AmazonGiftCodeServiceProvider" --tag="config"
```
## Usage
To Create Amazon Gift Card
```php
$aws = AmazonGiftCode::make()->buyGiftCard($value);
```
To Cancel Amazon Gift Card
```php
$aws = AmazonGiftCode::make()->cancelGiftCard($creationRequestId, $gcId);
```

## Available Methods

To change client configuration dynamic. If you pass only `$key` or other parameter will takes value from default config.
```php
$aws = AmazonGiftCode::make($key, $secret, $partner, $endpoint, $currency)->buyGiftCard($value);
```

### CreateGiftCard

`getStatus()`

Get the status of perform request. (`status`)

```php
$status = $aws->getStatus();
```


`getId()`

To get unique Amazon Gift Card id. (`gcId`)


```php
$gcId = $aws->getId();
```

`getCreationRequestId()`

Original Creation Request Id. (`creationRequestId`)


```php
$creationRequestId = $aws->getCreationRequestId();
```

`getClaimCode()`

Amazon Gift Card Claim Code to be used. (`gcClaimCode`)


```php
$gcClaimCode = $aws->getClaimCode();
```

`getValue()`

Amount of generated Gift Card. (`amount`)


```php
$amount = $aws->getValue();
```

`getCurrency()`

Currency Code of generated Gift Card. (`currencyCode`)


```php
$currencyCode = $aws->getCurrency();
```

`getRawJson()`

Get the raw JSON response. (original response)


```php
$rawJson = $aws->getRawJson();
```


### CancelGiftCard

`getStatus()`

Get the status of perform request. (`status`)

```php
$status = $aws->getStatus();
```

`getId()`

To get unique Amazon Gift Card id. (`gcId`)


```php
$gcId = $aws->getId();
```

`getCreationRequestId()`

Original Creation Request Id. (`creationRequestId`)


```php
$creationRequestId = $aws->getCreationRequestId();
```


`getRawJson()`

Get the raw JSON response. (original response)


```php
$rawJson = $aws->getRawJson();
```



## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email kashyapk62@gmail.com instead of using the issue tracker.

## Credits

- [Kashyap Merai][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/kamerk22/amazongiftcode.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/kamerk22/amazongiftcode.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/kamerk22/amazongiftcode/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/kamerk22/amazongiftcode
[link-downloads]: https://packagist.org/packages/kamerk22/amazongiftcode
[link-travis]: https://travis-ci.org/kamerk22/amazongiftcode
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/kamerk22
[link-contributors]: ../../contributors]
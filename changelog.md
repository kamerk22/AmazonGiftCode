# Changelog

All notable changes to `AmazonGiftCode` will be documented in this file.

## Version 2.0 - 2026-06-12

### Added
- Support for Laravel 13.
- Test suite (PHPUnit + Orchestra Testbench) covering config, request signing, response parsing and the public API.
- GitHub Actions CI matrix across Laravel 11/12/13 on PHP 8.2 to 8.4.

### Changed
- `AWS` and `AmazonGiftCode` now accept an optional `ClientInterface`, allowing the HTTP layer to be substituted (defaults to the existing cURL client, so behavior is unchanged).

### Fixed
- `CreateResponse` and `CreateBalanceResponse` no longer raise an undefined-key error when Amazon returns a body without the `cardInfo` / `availableFunds` keys (for example error responses).

### Removed
- Scrutinizer CI configuration (replaced by GitHub Actions).
- `sempro/phpunit-pretty-print` dev dependency (incompatible with PHPUnit 10+).

## Version 1.0

### Added
- Everything

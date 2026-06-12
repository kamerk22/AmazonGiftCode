# Changelog

All notable changes to `AmazonGiftCode` will be documented in this file.

## Version 2.0 - 2026-06-12

### Added
- Support for Laravel 13.
- Test suite (PHPUnit + Orchestra Testbench) covering config, request signing, response parsing and the public API.
- GitHub Actions CI matrix across Laravel 12/13 on PHP 8.2 to 8.4.

### Changed
- `AWS` and `AmazonGiftCode` now accept an optional `ClientInterface`, allowing the HTTP layer to be substituted (defaults to the existing cURL client, so behavior is unchanged).

### Fixed
- `CreateResponse` and `CreateBalanceResponse` no longer raise an undefined-key error when Amazon returns a body without the `cardInfo` / `availableFunds` keys (for example error responses).

### Removed
- Scrutinizer CI configuration (replaced by GitHub Actions).
- `sempro/phpunit-pretty-print` dev dependency (incompatible with PHPUnit 10+).
- Laravel 11 from the CI matrix and dev dependencies; it is past its security support window and is blocked by Composer's advisory policy. It remains installable for existing users via the `illuminate/support` constraint.

## Version 1.0

### Added
- Everything

# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.6.0] - 2024-05-27

### Commits

- Updated composer.json ([c3890c6](https://github.com/pronamic/wp-mollie/commit/c3890c68640e2b53ad309cd8de18291db5c89f8d))
- Updated package.json ([c5f77a8](https://github.com/pronamic/wp-mollie/commit/c5f77a8c3a06006e4749dd5247ca0b9dc85809f2))
- Added MyBank. ([cb386f4](https://github.com/pronamic/wp-mollie/commit/cb386f43dc53c18557547a135ed576427f432f96))
- Added BLIK. ([72aa13a](https://github.com/pronamic/wp-mollie/commit/72aa13a2ff98bcdd3c9a0c2722da297a46a5c263))

### Composer

- Changed `pronamic/wp-http` from `^1.1` to `v1.2.3`.
	Release notes: https://github.com/pronamic/wp-http/releases/tag/v1.2.3
- Changed `pronamic/wp-number` from `^1.1` to `v1.3.1`.
	Release notes: https://github.com/pronamic/wp-number/releases/tag/v1.3.1
- Changed `pronamic/wp-money` from `^2.0` to `v2.4.3`.
	Release notes: https://github.com/pronamic/wp-money/releases/tag/v2.4.3

Full set of changes: [`1.5.1...1.6.0`][1.6.0]

[1.6.0]: https://github.com/pronamic/wp-mollie/compare/v1.5.1...v1.6.0

## [1.5.1] - 2024-02-12

### Added

- Added constant for the TWINT method. ([331a5a3](https://github.com/pronamic/wp-mollie/commit/331a5a30f2a0bef7ae6d6333ef404e7b42f4ada7))

Full set of changes: [`1.5.0...1.5.1`][1.5.1]

[1.5.1]: https://github.com/pronamic/wp-mollie/compare/v1.5.0...v1.5.1

## [1.5.0] - 2024-02-07

### Added

- Added support for card token. [2941dee](https://github.com/pronamic/wp-mollie/commit/2941dee85b0c7ad2f510c9c1a34ceca1faa91585)

### Changed

- The HTTP timeout option is increased when connecting to Mollie via WP-Cron, WP-CLI or the Action Scheduler library. [pronamic/wp-pay-core#170](https://github.com/pronamic/wp-pay-core/issues/170)

Full set of changes: [`1.4.0...1.5.0`][1.5.0]

[1.5.0]: https://github.com/pronamic/wp-mollie/compare/v1.4.0...v1.5.0

## [1.4.0] - 2023-10-13

### Changed

- The payment details (`$payment->get_details()`) object is now an instance of the `ObjectAccess` class. ([469c231](https://github.com/pronamic/wp-mollie/commit/469c231726bd6ff8ca4b9730e42db55248b24588))

Full set of changes: [`1.3.0...1.4.0`][1.4.0]

[1.4.0]: https://github.com/pronamic/wp-mollie/compare/v1.3.0...v1.4.0

## [1.3.0] - 2023-09-11

### Commits

- Removed `justinrainbow/json-schema` usage to fix https://github.com/pronamic/pronamic-pay-with-mollie-for-woocommerce/issues/6. ([b4d2e66](https://github.com/pronamic/wp-mollie/commit/b4d2e665cdabb136d91789b8cf67e62b37f59f69))

### Composer

- Removed `justinrainbow/json-schema` `^5.2`.
- Changed `php` from `>=7.4` to `>=8.0`.

Full set of changes: [`1.2.3...1.3.0`][1.3.0]

[1.3.0]: https://github.com/pronamic/wp-mollie/compare/v1.2.3...v1.3.0

## [1.2.3] - 2023-08-23

### Commits

- Fixed some WPCS 3 warnings. ([c05be91](https://github.com/pronamic/wp-mollie/commit/c05be91d7d636c2877a648904b0c2437f11f8cfa))

Full set of changes: [`1.2.2...1.2.3`][1.2.3]

[1.2.3]: https://github.com/pronamic/wp-mollie/compare/v1.2.2...v1.2.3

## [1.2.2] - 2023-07-12

### Commits

- Switch to pronamic/pronamic-cli. ([eb47e96](https://github.com/pronamic/wp-mollie/commit/eb47e9636ec18d6809bbdc8f3e8ff427f521c07a))
- Added constant for the Billie method. ([a2b6e58](https://github.com/pronamic/wp-mollie/commit/a2b6e5846b6aaf19db661959c58860e9b804ec1b))

Full set of changes: [`1.2.1...1.2.2`][1.2.2]

[1.2.2]: https://github.com/pronamic/wp-mollie/compare/v1.2.1...v1.2.2

## [1.2.1] - 2023-04-04

### Fixed

- Fixed PHP warning about missing `package.json` file.

### Commits

- Updated .gitattributes ([f03aba3](https://github.com/pronamic/wp-mollie/commit/f03aba3a6221be094d864fce9c6f9f8914dec2c0))

Full set of changes: [`1.2.0...1.2.1`][1.2.1]

[1.2.1]: https://github.com/pronamic/wp-mollie/compare/v1.2.0...v1.2.1

## [1.2.0] - 2023-03-29
### Changed

- Extended support for refunds.

### Commits

- Set Composer type to WordPress plugin. ([f5ea1bb](https://github.com/pronamic/wp-mollie/commit/f5ea1bbf107755cb1108a9c80d0db7d4d3975349))
- Introduce a `Client->get_payment_reunfds( $payment_id, $parameters )` method. ([88028f6](https://github.com/pronamic/wp-mollie/commit/88028f6090eeb7bf3517a1d7ac52a0048051ce87))
- Don't set type of `metadata` in JSON schema, since it's documented as `mixed`. ([34f1e4f](https://github.com/pronamic/wp-mollie/commit/34f1e4f0d2136f256a0914100ed349753f546c21))
- Added function `Payment->has_refunds()`. ([36b3062](https://github.com/pronamic/wp-mollie/commit/36b3062316c92883d825c629a23afb8e14f6e768))
- Added in3 method constant. ([7966503](https://github.com/pronamic/wp-mollie/commit/796650392facd072663b3cb51ca79dbb7212ebfc))
- Added support for order payment line ID. ([8a3dc0e](https://github.com/pronamic/wp-mollie/commit/8a3dc0e1ab305a799005640d6c5680051b503bb4))
- Added support for `en_GB` locale. ([a735ca4](https://github.com/pronamic/wp-mollie/commit/a735ca471a77834352976284ee8af0f7e6dc1866))

Full set of changes: [`1.1.1...1.2.0`][1.2.0]

[1.2.0]: https://github.com/pronamic/wp-mollie/compare/v1.1.1...v1.2.0

## [1.1.1] - 2023-01-31
### Composer

- Changed `php` from `>=8.0` to `>=7.4`.
Full set of changes: [`1.1.0...1.1.1`][1.1.1]

[1.1.1]: https://github.com/pronamic/wp-mollie/compare/v1.1.0...v1.1.1

## [1.1.0] - 2022-12-22

### Commits

- PHP 8.0 ([ecb8bd1](https://github.com/pronamic/wp-mollie/commit/ecb8bd1e3ae1b04bc848dd741c3547d0192eb57b))
- Coding standards. ([c1f7694](https://github.com/pronamic/wp-mollie/commit/c1f76943b0c46256b12164a8940b872a2ddf8348))

### Composer

- Changed `php` from `>=7.4` to `>=8.0`.
Full set of changes: [`1.0.0...1.1.0`][1.1.0]

[1.1.0]: https://github.com/pronamic/wp-mollie/compare/v1.0.0...v1.1.0

## [1.0.0] - 2022-11-28

### Added

- Initial release, based on https://github.com/pronamic/wp-pronamic-pay-mollie/tree/4.5.0.

[unreleased]: https://github.com/pronamic/wp-mollie/compare/v1.1.0...HEAD
[1.0.0]: https://github.com/pronamic/wp-mollie/releases/tag/v0.0.1

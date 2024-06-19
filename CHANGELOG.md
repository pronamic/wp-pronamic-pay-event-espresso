# Change Log

All notable changes to this project will be documented in this file.

This projects adheres to [Semantic Versioning](http://semver.org/) and [Keep a CHANGELOG](http://keepachangelog.com/).

## [Unreleased][unreleased]
-

## [4.3.1] - 2024-06-19

### Commits

- Updated .gitattributes ([abb558f](https://github.com/pronamic/wp-pronamic-pay-event-espresso/commit/abb558feac90d52300bc428da2e2752b89737b0a))
- Updated .gitattributes ([3778601](https://github.com/pronamic/wp-pronamic-pay-event-espresso/commit/377860153ff92acb523ae8f71136ec61daf518fc))
- Updated .gitattributes ([1278489](https://github.com/pronamic/wp-pronamic-pay-event-espresso/commit/12784896761e093d3dc709fa231596823b1d84ab))

Full set of changes: [`4.3.0...4.3.1`][4.3.1]

[4.3.1]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/v4.3.0...v4.3.1

## [4.3.0] - 2024-06-07

### Changed

- Please note: In the "Pronamic Pay" plugin, all images have been removed from the `wp-content/plugins/pronamic-ideal/images/` folder. Users who use images from this folder for the Event Espresso payment methods button URL settings will need to update their settings. Users can upload images to their WordPress media library themselves. Logos and icons of popular payment methods can be found at https://github.com/pronamic/wp-pay-logos.

### Removed

- Removed the Pronamic gateway deafult icon URL.

### Commits

- No longer use files from plugin directory `pronamic-ideal/images`. ([fa893a6](https://github.com/pronamic/wp-pronamic-pay-event-espresso/commit/fa893a6458f134d18e94d50a44045adfc0eaeabd))
- Make standalone plugin for easier development and testing. ([31e388c](https://github.com/pronamic/wp-pronamic-pay-event-espresso/commit/31e388ce1d44883581912587a0e608ecf8001c0d))

### Composer

- Added `automattic/jetpack-autoloader` `^3.0`.
- Added `composer/installers` `^2.2`.
- Added `woocommerce/action-scheduler` `^3.8`.

Full set of changes: [`4.2.4...4.3.0`][4.3.0]

[4.3.0]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/v4.2.4...v4.3.0

## [4.2.4] - 2024-03-26

### Changed

- Fixed "All output should be run through an escaping function". ([f71ca74](https://github.com/pronamic/wp-pronamic-pay-event-espresso/commit/f71ca74367fca18a149d7b55c1feaafce2b776ac))

### Composer

- Changed `php` from `>=7.4` to `>=8.0`.
- Changed `wp-pay/core` from `^4.6` to `v4.16.0`.
	Release notes: https://github.com/pronamic/wp-pay-core/releases/tag/v4.16.0

Full set of changes: [`4.2.3...4.2.4`][4.2.4]

[4.2.4]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/v4.2.3...v4.2.4

## [4.2.3] - 2023-06-01

### Commits

- Switch from `pronamic/wp-deployer` to `pronamic/pronamic-cli`. ([078fa8c](https://github.com/pronamic/wp-pronamic-pay-event-espresso/commit/078fa8c8e4d34b486828200ebe76dde9a5cb5ed3))
- Updated .gitattributes ([f491f44](https://github.com/pronamic/wp-pronamic-pay-event-espresso/commit/f491f449bfbd25c6c1fc956f38f5a781dbfe4055))

Full set of changes: [`4.2.2...4.2.3`][4.2.3]

[4.2.3]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/v4.2.2...v4.2.3

## [4.2.2] - 2023-03-27

### Commits

- Set Composer type to WordPress plugin. ([3579839](https://github.com/pronamic/wp-pronamic-pay-event-espresso/commit/357983911a07398dc8c5ba12b8d3ea2495d18af2))
- Updated .gitattributes ([c6ed85a](https://github.com/pronamic/wp-pronamic-pay-event-espresso/commit/c6ed85afb657875b3bee17b5ad4b47f8f197899a))
- Requires PHP: 7.4. ([36ea581](https://github.com/pronamic/wp-pronamic-pay-event-espresso/commit/36ea581395b04ff17f16e1d3097c8da0ddd9989b))

Full set of changes: [`4.2.1...4.2.2`][4.2.2]

[4.2.2]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/v4.2.1...v4.2.2

## [4.2.1] - 2023-01-31
### Composer

- Changed `php` from `>=8.0` to `>=7.4`.
Full set of changes: [`4.2.0...4.2.1`][4.2.1]

[4.2.1]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/v4.2.0...v4.2.1

## [4.2.0] - 2022-12-23

### Composer

- Changed `php` from `>=5.6.20` to `>=8.0`.
- Changed `wp-pay/core` from `^4.5` to `v4.6.0`.
	Release notes: https://github.com/pronamic/wp-pay-core/releases/tag/v4.1.2
Full set of changes: [`4.1.2...4.2.0`][4.2.0]

[4.2.0]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/v4.1.2...v4.2.0

## [4.1.2] - 2022-11-07
- Fixed "Expected type 'null|array'. Found 'string'.".

## [4.1.1] - 2022-09-27
- Update to `wp-pay/core` version `^4.4`.

## [4.1.0] - 2022-09-26
- Updated for new payment methods and fields registration.

## [4.0.0] - 2022-01-10
- Updated to https://github.com/pronamic/wp-pay-core/releases/tag/4.0.0.

## [3.0.0] - 2021-08-05
- Updated to `pronamic/wp-pay-core`  version `3.0.0`.
- Updated to `pronamic/wp-money`  version `2.0.0`.
- Changed `TaxedMoney` to `Money`, no tax info.
- Switched to `pronamic/wp-coding-standards`.

## [2.3.2] - 2021-05-11
- Use `$transaction->remaining()` instead of `$transaction->total()` so that incomplete or manual payments are also included.
- Fixed "Non-static method EventEspressoHelper::get_description() should not be called statically" warning/error.

## [2.3.1] - 2021-04-26
- Fixed setting payment method.

## [2.3.0] - 2021-01-14
- Removed payment data class.

## [2.2.1] - 2020-04-03
- Set plugin integration name.

## [2.2.0] - 2020-03-19
- Extension extends from abstract plugin integration.

## [2.1.3] - 2019-12-22
- Improved error handling with exceptions.
- Updated usage of deprecated `addItem()` method.
- Updated output fields to use payment.
- Updated payment status class name.

## [2.1.2] - 2019-08-26
- Updated packages.

## [2.1.1] - 2018-09-28
- Use updated iDEAL gateway class name.
- Use cards icon as default icon for Pronamic payment method too.

## [2.1.0] - 2018-08-28
- Complete rewrite of the library.
- Removed support for Event Espresso version older than 4.6.

## [2.0.0] - 2018-05-14
- Switched to PHP namespaces.

## [1.1.6] - 2017-01-25
- Added filter for payment source description and URL.

## [1.1.5] - 2016-10-20
- Use payment redirect URL.
- Added help text with available tags.
- Added support for custom transaction descriptions.

## [1.1.4] - 2016-04-12
- No longer use camelCase for payment data.
- Set global WordPress gateway config as default config in gateways.

## [1.1.3] - 2016-02-11
- Fix only first payment updates EE transaction.
- Set default payment method to iDEAL if required.
- Added iDEAL gateway and payment method.
- Removed status code from redirect in status_update.

## [1.1.2] - 2015-10-14
- Fix sending multiple notifications.

## [1.1.1] - 2015-04-02
- Updated WordPress pay core library to version 1.2.0.
- No longer parse HTML input fields but use the new get_output_fields() function.
- Added workaround for strange behaviour with 2 config select options.

## [1.1.0] - 2015-03-25
- Added experimental support for Event Espresso 4.6 (or higher).

## [1.0.3] - 2015-03-03
- Changed WordPress pay core library requirement from `~1.0.0` to `>=1.0.0`.

## [1.0.2] - 2015-02-16
- Fixed fatal error on Event Espresso version 4.6 (or higher).

## [1.0.1] - 2015-01-27
- Fixed issue with getting customer name.

## 1.0.0 - 2015-01-20
- First release.

[unreleased]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/4.1.2...HEAD
[4.1.2]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/4.1.1...4.1.2
[4.1.1]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/4.1.0...4.1.1
[4.1.0]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/4.0.0...4.1.0
[4.0.0]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/3.0.0...4.0.0
[3.0.0]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/2.3.2...3.0.0
[2.3.2]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/2.3.1...2.3.2
[2.3.1]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/2.3.0...2.3.1
[2.3.0]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/2.2.1...2.3.0
[2.2.1]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/2.2.0...2.2.1
[2.2.0]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/2.1.3...2.2.0
[2.1.3]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/2.1.2...2.1.3
[2.1.2]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/2.1.1...2.1.2
[2.1.1]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/2.0.0...2.1.0
[2.0.0]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/1.1.6...2.0.0
[1.1.6]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/1.1.5...1.1.6
[1.1.5]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/1.1.4...1.1.5
[1.1.4]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/1.1.3...1.1.4
[1.1.3]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/1.1.2...1.1.3
[1.1.2]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/1.0.3...1.1.0
[1.0.3]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/pronamic/wp-pronamic-pay-event-espresso/compare/1.0.0...1.0.1

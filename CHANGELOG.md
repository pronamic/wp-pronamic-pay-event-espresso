# Change Log

All notable changes to this project will be documented in this file.

This projects adheres to [Semantic Versioning](http://semver.org/) and [Keep a CHANGELOG](http://keepachangelog.com/).

## [Unreleased][unreleased]
-

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
- Fix sending multiple notifcations.

## [1.1.1] - 2015-04-02
- Updated WordPress pay core library to version 1.2.0.
- No longer parse HTML input fields but use the new get_output_fields() function.
- Added workaround for strange behaviour with 2 config select options.

## [1.1.0] - 2015-03-25
- Added experimental support for Event Espresso 4.6 (or higher).

## [1.0.3] - 2015-03-03
- Changed WordPress pay core library requirment from ~1.0.0 to >=1.0.0.

## [1.0.2] - 2015-02-16
- Fixed fatal error on Event Espresso version 4.6 (or higher).

## [1.0.1] - 2015-01-27
- Fixed issue with getting customer name.

## 1.0.0 - 2015-01-20
- First release.

[unreleased]: https://github.com/wp-pay-extensions/event-espresso/compare/1.1.6...HEAD
[1.1.6]: https://github.com/wp-pay-extensions/event-espresso/compare/1.1.5...1.1.6
[1.1.5]: https://github.com/wp-pay-extensions/event-espresso/compare/1.1.4...1.1.5
[1.1.4]: https://github.com/wp-pay-extensions/event-espresso/compare/1.1.3...1.1.4
[1.1.3]: https://github.com/wp-pay-extensions/event-espresso/compare/1.1.2...1.1.3
[1.1.2]: https://github.com/wp-pay-extensions/event-espresso/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/wp-pay-extensions/event-espresso/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/wp-pay-extensions/event-espresso/compare/1.0.3...1.1.0
[1.0.3]: https://github.com/wp-pay-extensions/event-espresso/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/wp-pay-extensions/event-espresso/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/wp-pay-extensions/event-espresso/compare/1.0.0...1.0.1

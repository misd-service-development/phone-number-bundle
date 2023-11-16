# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed
- The default value for the `PhoneNumberType` form type option `country_display_emoji_flag` will change from `false` to `true` on the next major release
- The Doctrine column type length is configurable. Any existing `length` configuration (not taken into account before this release) will now be taken into account.

## [3.9.2] - 2023-06-29

### Changed

- Allow to use string with twig filters

### Fixed

- Remove deprecation notice when using serializer

## [3.9.1] - 2023-01-16

### Fixed

- Remove deprecation notice while using the PhoneNumber validation constraint with Symfony 6.1+ #139

## [3.9.0] - 2022-12-19

### Added

- Add Hungarian translations #129
- Add more french translations #130
- Add new (short) render format for countries in form type #95

### Changed

- The message `This value is not a valid number.` that was not translated inside the bundle is now `This value is not a valid phone number.` (and is translated in many languages #128

## [3.8.0] - 2022-10-24

### Added

- Add portuguese translations in #126

## [3.7.0] - 2022-07-04

### Added

- Maintenance: added return types on methods, avoid deprecation trigger, make the bundle future-proof

### Fixed

- Name of the catalan translation file is now accurate #116
- Remove deprecation notice when using annotations

## [3.6.3] - 2022-06-14

### Added

- Add catalan translation

### Changed 

- Deprecate `PhoneNumber::$errorNames` in favour of `PhoneNumber::ERROR_NAMES`.

### Fixed

- Deprecation notice on Symfony >= 6.1

## [3.6.2] - 2022-02-24

### Fixed

- Fix option format while using the validation constraint as attribute

## [3.6.1] - 2021-12-29

- Added return types

## [3.6.0] - 2021-12-07

- Added support for Symfony ^6.0
- Improve README documentation

## [3.5.0] - 2021-10-23

- Added support PHP8 attribute
- Drop support for PHP < 7.4
- Drop support for Symfony < 4.4

## [3.4.2] - 2021-06-28

### Added
- New twig template for Bootstrap 5
- Add `symfony/intl` as a required dependency

## [3.4.1] - 2021-04-12

### Added
- New twig template for Bootstrap 4

## [3.4.0] - 2021-04-06

### Added
- New twig filter `phone_number_format_out_of_country_calling_number`

## [3.3.3] - 2021-02-04

### Added
- Can now define a property path for the region on `PhoneNumber` constraint
- New option to specify default region for serialization
- New option to specify the format of the serialization

## [3.3.2] - 2021-01-22

### Changed
- Revert "Improve `PhoneNumber` constraint"

## [3.3.1] - 2021-01-22

### Changed
- Improve `PhoneNumber` constraint

## [3.3.0] - 2021-01-05

### Added
- Add PHP 8 support

### Changed
- Improve `PhoneNumberNormalizer`

## [3.2.1] - 2020-10-28

### Fixed
- Fix PhoneNumberValidator

## [3.2.0] - 2020-10-23

- Improve bundle configuration
- Can now define default region from config

## [3.1.1] - 2020-06-19

- Use replace keyword instead of conflict in composer.json

## [3.1.0] - 2020-04-27

### Added
- Add support of null values in phone number deserializer
- Add the ability to pass options down to country and number fields
- Refactor `PhoneNumber` constraints & validator to accept more than 1 type.
- Clarify LICENSE & add copyright back
- Update README
- Revamped folder

## [3.0.0] - 2019-11-26

### Removed
- Remove symfony/templating
- Remove deprecated DIC paremeters & aliases

## [2.0.2] - 2019-11-25

### Added
- Add Czech translations
- Add Turkish translations
- Add Ukrainian translations

## [2.0.1] - 2019-11-25

### Fixed
- Fix PhoneNumberBundle class

## [2.0.0] - 2019-11-25

### Added
- Add services only if relevant (optional) dependencies are available

### Changed
- Rename `tel_widget` to `phone_number_widget`

### Removed
- Drop support for Symfony < 3.4
- Drop support for PHP < 7.2
- Drop support for JMS Serializer
- Drop support for PHP templates


## [1.3.1] - 2018-01-17

### Changed
- Undo minor breaking change by reinstating and deprecating `se` code for Swedish translations.
- Validator uses `buildViolation` instead of `addViolation` for Symfony >= 2.5.

## [1.3.0] - 2018-01-15

### Added
- Symfony 4 support.
- Add `phone_number_of_type` Twig test and `isType` test to `PhoneNumberHelper`.
- Swedish translation improvements. Rename country code `se`->`sv`.
- Add `country_placeholder` option.
- Regard `"0"` as an invalid phone number.

### Changed
- Deprecate `PhoneNumberFormatHelper` in favour of `PhoneNumberHelper` and `PhoneNumberFormatExtension` in favour of `PhoneNumberHelperExtension`.

## [1.2.0] - 2016-01-17

### Added
- Add Symfony Serializer support.
- Confirm libphonenumber 8.0 compatibility.
- Avoid `choices_as_values` deprecation notice in Symfony 3.1.

### Changed
- Deprecate `phone_number_format` Twig function in favour of a filter.

## [1.1.3] - 2016-09-07

- Add basic Danish, Swedish and Finnish translations.

## [1.1.2] - 2016-03-31

### Added
- Allow the country choice form widget to not be required.
- Add difference between form type in Symfony 2 and 3 to the documentation.

## [1.1.1] - 2016-03-12

### Fixed
- Correct the block prefix for PhoneNumberType in Symfony 3.

## [1.1.0] - 2016-01-25

### Added
- Add translations for the validation constraint (BC break).
- Add validation of the phone number type.
- Throw an exception if Doctrine can't convert a database value to/from a `PhoneNumber`.
- Add country choice form widget.
- Add `libphonenumber.phone_number_offline_geocoder` service.
- Add `libphonenumber.short_number_info` service.
- Add `libphonenumber.phone_number_to_carrier_mapper` service.
- Add `libphonenumber.phone_number_to_time_zones_mapper` service.
- Deprecate `.class` parameters.

## [1.0.6] - 2015-01-22

- Confirm Symfony 3.0 compatibility.

## [1.0.5] - 2015-04-15

### Added
- Cater for Symfony's deprecation notices.
- Throw a `ConversionException` in the Doctrine type when the value is not a `PhoneNumber`.

## [1.0.4] - 2014-11-03

### Added
- Confirm libphonenumber 7.0 compatibility.

## [1.0.3] - 2014-10-20

### Added
- Handle international numbers correctly when using the national format and a default region.
- Throw a `TransformationFailedException` when required in the form data transformer. 

## [1.0.2] - 2014-02-27

### Added
- Confirm libphonenumber 6.0 compatibility.

## [1.0.1] - 2014-01-30

### Changed
- Changed libphonenumber port to giggsey/libphonenumber-for-php.

## [1.0.0] - 2013-10-10

### Added
- Initial release.

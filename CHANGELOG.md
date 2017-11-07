Changelog
=========

1.2.0
-----

17 December 2016.

* Add Symfony Serializer support.
* Confirm libphonenumber 8.0 compatibility.
* Deprecate `phone_number_format` Twig function in favour of a filter.
* Avoid `choices_as_values` deprecation notice in Symfony 3.1.

1.1.3
-----

7 September 2016.

* Add basic Danish, Swedish and Finnish translations.

1.1.2
-----

31 March 2016.

* Allow the country choice form widget to not be required.
* Add difference between form type in Symfony 2 and 3 to the documentation.

1.1.1
-----

12 March 2016.

* Correct the block prefix for PhoneNumberType in Symfony 3.

1.1.0
-----

25 January 2016.

* Add translations for the validation constraint (BC break).
* Add validation of the phone number type.
* Throw an exception if Doctrine can't convert a database value to/from a `PhoneNumber`.
* Add country choice form widget.
* Add `libphonenumber.phone_number_offline_geocoder` service.
* Add `libphonenumber.short_number_info` service.
* Add `libphonenumber.phone_number_to_carrier_mapper` service.
* Add `libphonenumber.phone_number_to_time_zones_mapper` service.
* Deprecate `.class` parameters.

1.0.6
-----

22 December 2015.

* Confirm Symfony 3.0 compatibility.

1.0.5
-----

15 April 2015.

* Cater for Symfony's deprecation notices.
* Throw a `ConversionException` in the Doctrine type when the value is not a `PhoneNumber`.

1.0.4
-----

3 November 2014.

* Confirm libphonenumber 7.0 compatibility.

1.0.3
-----

20 October 2014.

* Handle international numbers correctly when using the national format and a default region.
* Throw a `TransformationFailedException` when required in the form data transformer. 

1.0.2
-----

27 February 2014.

* Confirm libphonenumber 6.0 compatibility.

1.0.1
-----

30 January 2014.

* Changed libphonenumber port to giggsey/libphonenumber-for-php.

1.0.0
-----

10 October 2013.

* Initial release.

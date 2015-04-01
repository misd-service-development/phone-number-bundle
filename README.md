PhoneNumberBundle
=================

[![Build Status](https://travis-ci.org/misd-service-development/phone-number-bundle.png?branch=master)](https://travis-ci.org/misd-service-development/phone-number-bundle)

This bundle integrates [Google's libphonenumber](https://github.com/googlei18n/libphonenumber) into your Symfony2 application through the [giggsey/libphonenumber-for-php](https://github.com/giggsey/libphonenumber-for-php) port.

Authors
-------

* Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>

Installation
------------

 1. Add the PhoneNumberBundle to your dependencies:

        // composer.json

        {
           // ...
           "require": {
               // ...
               "misd/phone-number-bundle": "~1.0"
           }
        }

 2. Use Composer to download and install the PhoneNumberBundle:

        $ php composer.phar update misd/phone-number-bundle

 3. Register the bundle in your application:

        // app/AppKernel.php

        public function registerBundles()
        {
            $bundles = array(
                // ...
                new Misd\PhoneNumberBundle\MisdPhoneNumberBundle()
            );
        }

Usage
-----

### Services

The following services are available:

| Service                                               | ID                                                 | libphonenumber version |
| ----------------------------------------------------- | -------------------------------------------------- | ---------------------- |
| `libphonenumber\PhoneNumberUtil`                      | `libphonenumber.phone_number_util`                 |                        |
| `libphonenumber\geocoding\PhoneNumberOfflineGeocoder` | `libphonenumber.phone_number_offline_geocoder`     | >=5.8.8                |
| `libphonenumber\ShortNumberInfo`                      | `libphonenumber.short_number_info`                 | >=5.8                  |
| `libphonenumber\PhoneNumberToCarrierMapper`           | `libphonenumber.phone_number_to_carrier_mapper`    | >=5.8.8                |
| `libphonenumber\PhoneNumberToTimeZonesMapper`         | `libphonenumber.phone_number_to_time_zones_mapper` | >=5.8.8                |

So to parse a string into a `libphonenumber\PhoneNumber` object:

    $phoneNumber = $container->get('libphonenumber.phone_number_util')->parse($string, PhoneNumberUtil::UNKNOWN_REGION);

### Doctrine mapping

*Requires `doctrine/doctrine-bundle`.*

To persist `libphonenumber\PhoneNumber` objects, add the `Misd\PhoneNumberBundle\Doctrine\DBAL\Types\PhoneNumberType` mapping to your application's config:

    // app/config.yml

    doctrine:
        dbal:
            types:
                phone_number: Misd\PhoneNumberBundle\Doctrine\DBAL\Types\PhoneNumberType

You can then use the `phone_number` mapping:

    /**
     * @ORM\Column(type="phone_number")
     */
    private $phoneNumber;

This creates a `varchar(35)` column with a Doctrine mapping comment.

Note that if you're putting the `phone_number` type on an already-existing schema the current values must be converted to the `libphonenumber\PhoneNumberFormat::E164` format.

### Formatting `libphonenumber\PhoneNumber` objects

#### Twig

The `phone_number_format` function takes two arguments: a `libphonenumber\PhoneNumber` object and an optional `libphonenumber\PhoneNumberFormat` constant name.

For example, to format an object called `myPhoneNumber` in the `libphonenumber\PhoneNumberFormat::NATIONAL` format:

    {{ phone_number_format(myPhoneNumber, 'NATIONAL') }}

By default phone numbers are formatted in the `libphonenumber\PhoneNumberFormat::INTERNATIONAL` format.

#### PHP template

The `format()` method in the `phone_number_format` helper takes two arguments: a `libphonenumber\PhoneNumber` object and an optional `libphonenumber\PhoneNumberFormat` constant name or value.

For example, to format `$myPhoneNumber` in the `libphonenumber\PhoneNumberFormat::NATIONAL` format, either use:

    <?php echo $view['phone_number_format']->format($myPhoneNumber, 'NATIONAL') ?>

or:

    <?php echo $view['phone_number_format']->format($myPhoneNumber, \libphonenumber\PhoneNumberFormat::NATIONAL) ?>

By default phone numbers are formatted in the `libphonenumber\PhoneNumberFormat::INTERNATIONAL` format.

### Serializing `libphonenumber\PhoneNumber` objects

*Requires `jms/serializer-bundle`.*

Instances of `libphonenumber\PhoneNumber` are automatically serialized in the E.164 format.

Phone numbers can be deserialized from an international format by setting the type to `libphonenumber\PhoneNumber`. For example:

    use JMS\Serializer\Annotation\Type;

    /**
     * @Type("libphonenumber\PhoneNumber")
     */
    private $phoneNumber;

### Using `libphonenumber\PhoneNumber` objects in forms

You can use the `tel` form type to create phone number fields. For example:

    use libphonenumber\PhoneNumberFormat;
    use Symfony\Component\Form\FormBuilderInterface;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('phone_number', 'tel', array('default_region' => 'GB', 'format' => PhoneNumberFormat::NATIONAL));
    }

By default the `default_region` and `format` options are `PhoneNumberUtil::UNKNOWN_REGION` and `PhoneNumberFormat::INTERNATIONAL` respectively.

### Validating phone numbers

You can use the `Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber` constraint to make sure that either a `libphonenumber\PhoneNumber` object or a plain string is a valid phone number. For example:

    use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;

    /**
     * @AssertPhoneNumber
     */
    private $phoneNumber;

You can set the default region through the `defaultRegion` property:

    use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;

    /**
     * @AssertPhoneNumber(defaultRegion="GB")
     */
    private $phoneNumber;

By default any valid phone number will be accepted. You can restrict the type through the `type` property, recognised values are `mobile` and `fixed_line`. (Note the libphonenumber cannot always distinguish between mobile and fixed-line numbers (eg in the USA), in which case it will be accepted.)

    /**
     * @AssertPhoneNumber(type="mobile")
     */
    private $mobilePhoneNumber;

### Translations

The bundle contains translations for the form field and validation constraints.

In cases where a language uses multiple terms for mobile phones, the generic language locale will use the term 'mobile', while country-specific locales will use the relevant term. So in English, for example, `en` uses 'mobile', `en_US` uses 'cell' and `en_SG` uses 'handphone'.

If your language doesn't yet have translations, feel free to open a pull request to add them in!

PhoneNumberBundle
=================

This package has been abandoned in favour of [odolbeau/phone-number-bundle](https://github.com/odolbeau/phone-number-bundle).

[![Build Status](https://img.shields.io/travis/misd-service-development/phone-number-bundle.svg?style=flat-square)](https://travis-ci.org/misd-service-development/phone-number-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/misd/phone-number-bundle.svg?style=flat-square)](https://packagist.org/packages/misd/phone-number-bundle)
[![Downloads per month](https://img.shields.io/packagist/dm/misd/phone-number-bundle.svg?style=flat-square)](https://packagist.org/packages/misd/phone-number-bundle)
[![Latest stable version](https://img.shields.io/packagist/v/misd/phone-number-bundle.svg?style=flat-square)](https://packagist.org/packages/misd/phone-number-bundle)
[![License](http://img.shields.io/packagist/l/misd/phone-number-bundle.svg?style=flat-square)](https://packagist.org/packages/misd/phone-number-bundle)

This bundle integrates [Google's libphonenumber](https://github.com/googlei18n/libphonenumber) into your Symfony2-Symfony4 application through the [giggsey/libphonenumber-for-php](https://github.com/giggsey/libphonenumber-for-php) port.

Installation
------------

 1. Use Composer to download the PhoneNumberBundle:

```bash
        $ composer require misd/phone-number-bundle
```

 2. Register the bundle in your application:

```php
        // app/AppKernel.php

        public function registerBundles()
        {
            $bundles = array(
                // ...
                new Misd\PhoneNumberBundle\MisdPhoneNumberBundle()
            );
        }
```

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

```php
    $phoneNumber = $container->get('libphonenumber.phone_number_util')->parse($string, PhoneNumberUtil::UNKNOWN_REGION);
```

### Doctrine mapping

*Requires `doctrine/doctrine-bundle`.*

To persist `libphonenumber\PhoneNumber` objects, add the `Misd\PhoneNumberBundle\Doctrine\DBAL\Types\PhoneNumberType` mapping to your application's config:

```yml
    // app/config.yml

    doctrine:
        dbal:
            types:
                phone_number: Misd\PhoneNumberBundle\Doctrine\DBAL\Types\PhoneNumberType
```

You can then use the `phone_number` mapping:

```php
    /**
     * @ORM\Column(type="phone_number")
     */
    private $phoneNumber;
```

This creates a `varchar(35)` column with a Doctrine mapping comment.

Note that if you're putting the `phone_number` type on an already-existing schema the current values must be converted to the `libphonenumber\PhoneNumberFormat::E164` format.

### Templating

#### Twig

##### phone_number_format
The `phone_number_format` filter can be used to format a phone number object. A `libphonenumber\PhoneNumberFormat` constant can be passed as argument to specify in which format the number should be printed.

For example, to format an object called `myPhoneNumber` in the `libphonenumber\PhoneNumberFormat::NATIONAL` format:

```php
    {{ myPhoneNumber|phone_number_format('NATIONAL') }}
```

By default phone numbers are formatted in the `libphonenumber\PhoneNumberFormat::INTERNATIONAL` format.

###### phone_number_of_type

The `phone_number_of_type` test can be used to check a phone number against a type: A `libphonenumber\PhoneNumberType` constant name must be passed to specify to which type a number has to match.

For example, to check if an object called `myPhoneNumber` is a `libphonenumber\PhoneNumberType::MOBILE` type:

```php
    {% if myPhoneNumber is phone_number_of_type('MOBILE') }} %} ... {% endif %}
```

#### PHP template

##### format()

The `format()` method in the `phone_number_helper` takes two arguments: a `libphonenumber\PhoneNumber` object and an optional `libphonenumber\PhoneNumberFormat` constant name or value.

For example, to format `$myPhoneNumber` in the `libphonenumber\PhoneNumberFormat::NATIONAL` format, either use:

```php
    <?php echo $view['phone_number_helper']->format($myPhoneNumber, 'NATIONAL') ?>
```

or:

```php
    <?php echo $view['phone_number_helper']->format($myPhoneNumber, \libphonenumber\PhoneNumberFormat::NATIONAL) ?>
```

By default phone numbers are formatted in the `libphonenumber\PhoneNumberFormat::INTERNATIONAL` format.

###### isType()

The `isType()` method in the `phone_number_helper` takes two arguments: a `libphonenumber\PhoneNumber` object and an optional `libphonenumber\PhoneNumberType` constant name or value.

For example, to check if $myPhoneNumber` is a `libphonenumber\PhoneNumberType::MOBILE` type:

```php
    <?php if $view['phone_number_helper']->isType($myPhoneNumber, 'MOBILE'): ?>
    ...
    <?php endif; ?>
```

or:

```php
    <?php if $view['phone_number_helper']->isType($myPhoneNumber, \libphonenumber\PhoneNumberType::MOBILE): ?>
    ...
    <?php endif; ?>
```

### Serializing `libphonenumber\PhoneNumber` objects

*Requires `jms/serializer-bundle`.*

Instances of `libphonenumber\PhoneNumber` are automatically serialized in the E.164 format.

Phone numbers can be deserialized from an international format by setting the type to `libphonenumber\PhoneNumber`. For example:

```php
    use JMS\Serializer\Annotation\Type;

    /**
     * @Type("libphonenumber\PhoneNumber")
     */
    private $phoneNumber;
```

### Using `libphonenumber\PhoneNumber` objects in forms

You can use the `PhoneNumberType` (`phone_number` for Symfony 2.7) form type to create phone number fields. There are two widgets available.

#### Single text field

A single text field allows the user to type in the complete phone number. When an international prefix is not entered, the number is assumed to be part of the set `default_region`. For example:

```php
    use libphonenumber\PhoneNumberFormat;
    use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
    use Symfony\Component\Form\FormBuilderInterface;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('phone_number', PhoneNumberType::class, array('default_region' => 'GB', 'format' => PhoneNumberFormat::NATIONAL));
    }
```

By default the `default_region` and `format` options are `PhoneNumberUtil::UNKNOWN_REGION` and `PhoneNumberFormat::INTERNATIONAL` respectively.

#### Country choice fields

The phone number can be split into a country choice and phone number text fields. This allows the user to choose the relevant country (from a customisable list) and type in the phone number without international dialling.

```php
    use libphonenumber\PhoneNumberFormat;
    use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
    use Symfony\Component\Form\FormBuilderInterface;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('phone_number', PhoneNumberType::class, array('widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE, 'country_choices' => array('GB', 'JE', 'FR', 'US'), 'preferred_country_choices' => array('GB', 'JE')));
    }
```

This produces the preferred choices of 'Jersey' and 'United Kingdom', and regular choices of 'France' and 'United States'.

By default the `country_choices` is empty, which means all countries are included, as is `preferred_country_choices`.
The option `country_placeholder` can be specified to create a placeholder option on above the whole list.

### Validating phone numbers

You can use the `Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber` constraint to make sure that either a `libphonenumber\PhoneNumber` object or a plain string is a valid phone number. For example:

```php
    use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;

    /**
     * @AssertPhoneNumber
     */
    private $phoneNumber;
```

You can set the default region through the `defaultRegion` property:

```php
    use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;

    /**
     * @AssertPhoneNumber(defaultRegion="GB")
     */
    private $phoneNumber;
```

By default any valid phone number will be accepted. You can restrict the type through the `type` property, recognised values:

- `Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber::ANY` (default)
- `Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber::FIXED_LINE`
- `Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber::MOBILE`
- `Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber::PAGER`
- `Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber::PERSONAL_NUMBER`
- `Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber::PREMIUM_RATE`
- `Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber::SHARED_COST`
- `Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber::TOLL_FREE`
- `Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber::UAN`
- `Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber::VOIP`
- `Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber::VOICEMAIL`

(Note that libphonenumber cannot always distinguish between mobile and fixed-line numbers (eg in the USA), in which case it will be accepted.)

```php
    /**
     * @AssertPhoneNumber(type="mobile")
     */
    private $mobilePhoneNumber;
```

### Translations

The bundle contains translations for the form field and validation constraints.

In cases where a language uses multiple terms for mobile phones, the generic language locale will use the term 'mobile', while country-specific locales will use the relevant term. So in English, for example, `en` uses 'mobile', `en_US` uses 'cell' and `en_SG` uses 'handphone'.

If your language doesn't yet have translations, feel free to open a pull request to add them in!

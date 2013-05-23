PhoneNumberBundle
=================

This bundle integrates [Google's libphonenumber](https://code.google.com/p/libphonenumber/) into your Symfony2 application through the [practo/libphonenumber-for-php](https://github.com/practo/libphonenumber-for-php) port.

Authors
-------

* Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>

Installation
------------

 1. Add PhoneNumberBundle to your dependencies:

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

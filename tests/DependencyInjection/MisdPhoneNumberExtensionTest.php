<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Tests\DependencyInjection;

use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Misd\PhoneNumberBundle\DependencyInjection\MisdPhoneNumberExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\TaggedContainerInterface;

/**
 * Bundle extension test.
 */
class MisdPhoneNumberExtensionTest extends TestCase
{
    /**
     * @var TaggedContainerInterface
     */
    protected $container;

    public function testLoad()
    {
        $extension = new MisdPhoneNumberExtension();
        $this->container = new ContainerBuilder();

        $extension->load([], $this->container);

        $this->assertTrue($this->container->has('libphonenumber\PhoneNumberUtil'));
        if (class_exists('libphonenumber\geocoding\PhoneNumberOfflineGeocoder') && \extension_loaded('intl')) {
            $this->assertTrue($this->container->has('libphonenumber\geocoding\PhoneNumberOfflineGeocoder'));
        }
        if (class_exists('libphonenumber\ShortNumberInfo')) {
            $this->assertTrue($this->container->has('libphonenumber\ShortNumberInfo'));
        }
        if (class_exists('libphonenumber\PhoneNumberToCarrierMapper') && \extension_loaded('intl')) {
            $this->assertTrue($this->container->has('libphonenumber\PhoneNumberToCarrierMapper'));
        }
        if (class_exists('libphonenumber\PhoneNumberToTimeZonesMapper')) {
            $this->assertTrue($this->container->has('libphonenumber\PhoneNumberToTimeZonesMapper'));
        }
        $this->assertTrue($this->container->has('Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberHelper'));
        $this->assertTrue($this->container->has('Misd\PhoneNumberBundle\Form\Type\PhoneNumberType'));

        $services = $this->container->findTaggedServiceIds('form.type');
        $this->assertArrayHasKey('Misd\PhoneNumberBundle\Form\Type\PhoneNumberType', $services);
        $this->assertContains(['alias' => 'phone_number'], $services['Misd\PhoneNumberBundle\Form\Type\PhoneNumberType']);

        $services = $this->container->findTaggedServiceIds('validator.constraint_validator');
        $this->assertArrayHasKey('Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumberValidator', $services);

        $this->assertSame(PhoneNumberUtil::UNKNOWN_REGION, $this->container->getParameter('misd_phone_number.validator.default_region'));
    }

    public function testDisabledServices()
    {
        $extension = new MisdPhoneNumberExtension();
        $this->container = new ContainerBuilder();
        $extension->load([
            'misd_phone_number' => [
                'twig' => false,
                'form' => false,
                'serializer' => false,
                'validator' => false,
            ],
        ], $this->container);

        $this->assertTrue($this->container->has('libphonenumber\PhoneNumberUtil'));

        $this->assertFalse($this->container->has('Misd\PhoneNumberBundle\Twig\Extension\PhoneNumberHelperExtension'));
        $this->assertFalse($this->container->has('Misd\PhoneNumberBundle\Form\Type\PhoneNumberType'));
        $this->assertFalse($this->container->has('Misd\PhoneNumberBundle\Serializer\Normalizer\PhoneNumberNormalizer'));
        $this->assertFalse($this->container->has('Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumberValidator'));
    }

    public function testValidatorParameters()
    {
        $extension = new MisdPhoneNumberExtension();
        $this->container = new ContainerBuilder();
        $extension->load([
            'misd_phone_number' => [
                'validator' => [
                    'default_region' => 'GB',
                    'format' => PhoneNumberFormat::E164,
                ],
            ],
        ], $this->container);

        $this->assertSame('GB', $this->container->getParameter('misd_phone_number.validator.default_region'));
        $this->assertSame(0, $this->container->getParameter('misd_phone_number.validator.format'));
    }

    public function testNormalizerParameters()
    {
        $extension = new MisdPhoneNumberExtension();
        $this->container = new ContainerBuilder();
        $extension->load([
            'misd_phone_number' => [
                'serializer' => [
                    'default_region' => 'FR',
                    'format' => PhoneNumberFormat::INTERNATIONAL,
                ],
            ],
        ], $this->container);

        $this->assertSame('FR', $this->container->getParameter('misd_phone_number.serializer.default_region'));
        $this->assertSame(PhoneNumberFormat::INTERNATIONAL, $this->container->getParameter('misd_phone_number.serializer.format'));
    }

    public function testValidatorDefaultRegionUppercase()
    {
        $extension = new MisdPhoneNumberExtension();
        $this->container = new ContainerBuilder();
        $extension->load([
            'misd_phone_number' => [
                'validator' => [
                    'default_region' => 'gb',
                ],
            ],
        ], $this->container);

        $this->assertSame('GB', $this->container->getParameter('misd_phone_number.validator.default_region'));
    }
}

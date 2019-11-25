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
        if (class_exists('libphonenumber\geocoding\PhoneNumberOfflineGeocoder') && extension_loaded('intl')) {
            $this->assertTrue($this->container->has('libphonenumber\geocoding\PhoneNumberOfflineGeocoder'));
        }
        if (class_exists('libphonenumber\ShortNumberInfo')) {
            $this->assertTrue($this->container->has('libphonenumber\ShortNumberInfo'));
        }
        if (class_exists('libphonenumber\PhoneNumberToCarrierMapper') && extension_loaded('intl')) {
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
    }
}

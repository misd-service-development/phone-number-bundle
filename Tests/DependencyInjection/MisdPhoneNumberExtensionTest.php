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
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\TaggedContainerInterface;

/**
 * Bundle extension test.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
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

        $extension->load(array(), $this->container);

        $this->assertHasService(
          'libphonenumber.phone_number_util',
          'libphonenumber\PhoneNumberUtil'
        );
        $this->assertHasService(
          'misd_phone_number.templating.helper.format',
          'Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberFormatHelper'
        );
        $this->assertServiceHasTag(
          'misd_phone_number.templating.helper.format',
          'templating.helper',
          array('alias' => 'phone_number_format')
        );
        $this->assertHasService(
          'misd_phone_number.form.type',
          'Misd\PhoneNumberBundle\Form\Type\PhoneNumberType'
        );
        $this->assertServiceHasTag(
          'misd_phone_number.form.type',
          'form.type',
          array('alias' => 'tel')
        );
        $this->assertHasService(
          'misd_phone_number.serializer.handler',
          'Misd\PhoneNumberBundle\Serializer\Handler\PhoneNumberHandler'
        );
        $this->assertServiceHasTag(
          'misd_phone_number.serializer.handler',
          'jms_serializer.handler',
          array(
            'type' => 'libphonenumber\PhoneNumber',
            'direction' => 'serialization',
            'format' => 'json',
            'method' => 'serializePhoneNumber',
          )
        );
        $this->assertServiceHasTag(
          'misd_phone_number.serializer.handler',
          'jms_serializer.handler',
          array(
            'type' => 'libphonenumber\PhoneNumber',
            'direction' => 'deserialization',
            'format' => 'json',
            'method' => 'deserializePhoneNumberFromJson',
          )
        );
        $this->assertServiceHasTag(
          'misd_phone_number.serializer.handler',
          'jms_serializer.handler',
          array(
            'type' => 'libphonenumber\PhoneNumber',
            'direction' => 'serialization',
            'format' => 'xml',
            'method' => 'serializePhoneNumber',
          )
        );
        $this->assertServiceHasTag(
          'misd_phone_number.serializer.handler',
          'jms_serializer.handler',
          array(
            'type' => 'libphonenumber\PhoneNumber',
            'direction' => 'deserialization',
            'format' => 'xml',
            'method' => 'deserializePhoneNumberFromXml',
          )
        );
        $this->assertServiceHasTag(
          'misd_phone_number.serializer.handler',
          'jms_serializer.handler',
          array(
            'type' => 'libphonenumber\PhoneNumber',
            'direction' => 'serialization',
            'format' => 'yml',
            'method' => 'serializePhoneNumber',
          )
        );
    }

    protected function assertHasService($id, $instanceOf)
    {
        $this->assertTrue($this->container->has($id));
        $this->assertInstanceOf($instanceOf, $this->container->get($id));
    }

    protected function assertServiceHasTag($id, $tag, $attributes = array())
    {
        $services = $this->container->findTaggedServiceIds($tag);

        $this->assertArrayHasKey($id, $services);
        $this->assertContains($attributes, $services[$id]);
    }
}

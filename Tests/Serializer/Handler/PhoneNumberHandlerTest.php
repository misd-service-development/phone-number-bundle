<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Tests\Serializer\Handler;

use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\VisitorInterface;
use libphonenumber\PhoneNumberUtil;
use Misd\PhoneNumberBundle\Serializer\Handler\PhoneNumberHandler;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

/**
 * Phone number serialization handler test.
 */
class PhoneNumberHandlerTest extends TestCase
{
    public function testSerializePhoneNumber()
    {
        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();

        $handler = new PhoneNumberHandler($phoneNumberUtil);

        $visitor = $this->getMockBuilder(interface_exists(DeserializationVisitorInterface::class) ? DeserializationVisitorInterface::class : VisitorInterface::class)->getMock();

        $test = $this->createMock('libphoneNumber\PhoneNumber');
        $type = [];
        $context = $this->createMock('JMS\Serializer\Context');

        $phoneNumberUtil->expects($this->once())->method('format')->with($test)->will($this->returnValue('foo'));
        $visitor->expects($this->once())->method('visitString')->with('foo', $type, $context)
            ->will($this->returnValue('bar'));

        $this->assertSame('bar', $handler->serializePhoneNumber($visitor, $test, $type, $context));
    }

    public function testDeserializePhoneNumberFromJsonWhenNull()
    {
        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();

        $handler = new PhoneNumberHandler($phoneNumberUtil);

        $visitor = $this->getMockBuilder('JMS\Serializer\VisitorInterface')
            ->disableOriginalConstructor()->getMock();

        $this->assertNull($handler->deserializePhoneNumberFromJson($visitor, null, []));
    }

    public function testDeserializePhoneNumberFromJson()
    {
        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();

        $handler = new PhoneNumberHandler($phoneNumberUtil);

        $visitor = $this->getMockBuilder('JMS\Serializer\VisitorInterface')
            ->disableOriginalConstructor()->getMock();

        $test = '+441234567890';

        $phoneNumberUtil->expects($this->once())->method('parse')->with($test, PhoneNumberUtil::UNKNOWN_REGION)
            ->will($this->returnValue('foo'));

        $this->assertSame('foo', $handler->deserializePhoneNumberFromJson($visitor, $test, []));
    }

    public function testDeserializePhoneNumberFromXmlWhenNil()
    {
        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();

        $handler = new PhoneNumberHandler($phoneNumberUtil);

        $visitor = $this->getMockBuilder('JMS\Serializer\VisitorInterface')
            ->disableOriginalConstructor()->getMock();

        $xml = new SimpleXMLElement('<phone_number nil="true"/>');

        $this->assertNull($handler->deserializePhoneNumberFromXml($visitor, $xml, []));
    }

    public function testDeserializePhoneNumberFromXmlWhenXsiNil()
    {
        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();

        $handler = new PhoneNumberHandler($phoneNumberUtil);

        $visitor = $this->getMockBuilder('JMS\Serializer\VisitorInterface')
            ->disableOriginalConstructor()->getMock();

        $xml = new SimpleXMLElement('<phone_number/>');
        $xml->addAttribute('xsi:nil', 'true', 'http://www.w3.org/2001/XMLSchema-instance');

        $this->assertNull($handler->deserializePhoneNumberFromXml($visitor, $xml, []));
    }

    public function testDeserializePhoneNumberFromXml()
    {
        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();

        $handler = new PhoneNumberHandler($phoneNumberUtil);

        $visitor = $this->getMockBuilder('JMS\Serializer\VisitorInterface')
            ->disableOriginalConstructor()->getMock();

        $test = '+441234567890';

        $xml = new SimpleXMLElement(sprintf('<phone_number>%s</phone_number>', $test));

        $phoneNumberUtil->expects($this->once())->method('parse')->with($test, PhoneNumberUtil::UNKNOWN_REGION)
            ->will($this->returnValue('foo'));

        $this->assertSame('foo', $handler->deserializePhoneNumberFromXml($visitor, $xml, []));
    }
}

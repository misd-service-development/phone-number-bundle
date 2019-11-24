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

use JMS\Serializer\Context;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\VisitorInterface;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
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
        $phoneNumber = new PhoneNumber();

        $phoneNumberUtil = $this->prophesize(PhoneNumberUtil::class);
        $phoneNumberUtil->format($phoneNumber, PhoneNumberFormat::E164)->shouldBeCalledTimes(1)->willReturn('foo');

        $context = $this->prophesize(Context::class);

        $visitor = $this->prophesize(VisitorInterface::class);
        $visitor->visitString('foo', [], $context->reveal())->shouldBeCalledTimes(1)->willReturn('bar');

        $handler = new PhoneNumberHandler($phoneNumberUtil->reveal());

        $this->assertSame('bar', $handler->serializePhoneNumber($visitor->reveal(), $phoneNumber, [], $context->reveal()));
    }

    public function testDeserializePhoneNumberFromJsonWhenNull()
    {
        $phoneNumberUtil = $this->prophesize(PhoneNumberUtil::class);
        $handler = new PhoneNumberHandler($phoneNumberUtil->reveal());

        $visitor = $this->prophesize(JsonDeserializationVisitor::class);

        $this->assertNull($handler->deserializePhoneNumberFromJson($visitor->reveal(), null, []));
    }

    public function testDeserializePhoneNumberFromJson()
    {
        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();

        $handler = new PhoneNumberHandler($phoneNumberUtil);

        $visitor = $this->getMockBuilder('JMS\Serializer\JsonDeserializationVisitor')
            ->disableOriginalConstructor()->getMock();

        $test = '+441234567890';

        $phoneNumberUtil->expects($this->once())->method('parse')->with($test, PhoneNumberUtil::UNKNOWN_REGION)
            ->will($this->returnValue('foo'));

        $this->assertSame('foo', $handler->deserializePhoneNumberFromJson($visitor, $test, array()));
    }

    public function testDeserializePhoneNumberFromXmlWhenNil()
    {
        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();

        $handler = new PhoneNumberHandler($phoneNumberUtil);

        $visitor = $this->getMockBuilder('JMS\Serializer\XmlDeserializationVisitor')
            ->disableOriginalConstructor()->getMock();

        $xml = new SimpleXMLElement('<phone_number nil="true"/>');

        $this->assertNull($handler->deserializePhoneNumberFromXml($visitor, $xml, array()));
    }

    public function testDeserializePhoneNumberFromXmlWhenXsiNil()
    {
        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();

        $handler = new PhoneNumberHandler($phoneNumberUtil);

        $visitor = $this->getMockBuilder('JMS\Serializer\XmlDeserializationVisitor')
            ->disableOriginalConstructor()->getMock();

        $xml = new SimpleXMLElement('<phone_number/>');
        $xml->addAttribute('xsi:nil', 'true', 'http://www.w3.org/2001/XMLSchema-instance');

        $this->assertNull($handler->deserializePhoneNumberFromXml($visitor, $xml, array()));
    }

    public function testDeserializePhoneNumberFromXml()
    {
        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();

        $handler = new PhoneNumberHandler($phoneNumberUtil);

        $visitor = $this->getMockBuilder('JMS\Serializer\XmlDeserializationVisitor')
            ->disableOriginalConstructor()->getMock();

        $test = '+441234567890';

        $xml = new SimpleXMLElement(sprintf('<phone_number>%s</phone_number>', $test));

        $phoneNumberUtil->expects($this->once())->method('parse')->with($test, PhoneNumberUtil::UNKNOWN_REGION)
            ->will($this->returnValue('foo'));

        $this->assertSame('foo', $handler->deserializePhoneNumberFromXml($visitor, $xml, array()));
    }
}

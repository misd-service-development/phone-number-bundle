<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Tests\Templating\Helper;

use libphonenumber\PhoneNumberFormat;
use Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberFormatHelper;
use Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberHelper;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Phone number templating helper test.
 */
class PhoneNumberHelperTest extends TestCase
{
    public function testDeprecatedConstructor()
    {
        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();

        $helper = new PhoneNumberHelper($phoneNumberUtil);

        $this->assertInstanceOf('Symfony\Component\Templating\Helper\HelperInterface', $helper);
    }

    public function testConstructor()
    {
        $phoneNumberFormatter = $this->getMockBuilder('Misd\PhoneNumberBundle\Formatter\PhoneNumberFormatter')
            ->disableOriginalConstructor()->getMock();

        $helper = new PhoneNumberHelper($phoneNumberFormatter);

        $this->assertInstanceOf('Symfony\Component\Templating\Helper\HelperInterface', $helper);
    }

    public function testCharset()
    {
        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();

        $helper = new PhoneNumberHelper($phoneNumberUtil);

        $helper->setCharset('test');

        $this->assertSame('test', $helper->getCharset());
    }

    public function testName()
    {
        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();

        $helper = new PhoneNumberHelper($phoneNumberUtil);

        $this->assertTrue(is_string($helper->getName()));
    }

    public function testFormat()
    {
        $phoneNumber = $this->getMock('libphonenumber\PhoneNumber');

        $phoneNumberFormatter = $this->getMockBuilder('Misd\PhoneNumberBundle\Formatter\PhoneNumberFormatter')
            ->disableOriginalConstructor()->getMock();
        $phoneNumberFormatter->expects($this->once())->method('format')->with($phoneNumber, PhoneNumberFormat::NATIONAL)->willReturn('foo');

        $helper = new PhoneNumberHelper($phoneNumberFormatter);

        $result = $helper->format($phoneNumber, PhoneNumberFormat::NATIONAL);

        $this->assertSame('foo', $result);
    }

    public function testIsType()
    {
        $phoneNumber = $this->getMock('libphonenumber\PhoneNumber');

        $phoneNumberFormatter = $this->getMockBuilder('Misd\PhoneNumberBundle\Formatter\PhoneNumberFormatter')
            ->disableOriginalConstructor()->getMock();
        $phoneNumberFormatter->expects($this->once())->method('isType')->with($phoneNumber, PhoneNumberFormat::NATIONAL)->willReturn(true);

        $helper = new PhoneNumberHelper($phoneNumberFormatter);

        $result = $helper->isType($phoneNumber, PhoneNumberFormat::NATIONAL);

        $this->assertTrue($result);
    }

    public function testDeprecatedClassName() {
        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();

        $helper = new PhoneNumberFormatHelper($phoneNumberUtil);

        $this->assertInstanceOf('Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberHelper', $helper);
    }
}

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
    public function testConstructor()
    {
        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();

        $helper = new PhoneNumberHelper($phoneNumberUtil);

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

    /**
     * @dataProvider processProvider
     */
    public function testProcess($format, $expectedFormat)
    {
        $phoneNumber = $this->getMock('libphonenumber\PhoneNumber');

        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();
        $phoneNumberUtil->expects($this->once())->method('format')->with($phoneNumber, $expectedFormat);

        $helper = new PhoneNumberHelper($phoneNumberUtil);

        $helper->format($phoneNumber, $format);
    }

    /**
     * 0 => Format
     * 1 => Expected format
     */
    public function processProvider()
    {
        return array(
            array(PhoneNumberFormat::NATIONAL, PhoneNumberFormat::NATIONAL),
            array('NATIONAL', PhoneNumberFormat::NATIONAL),
        );
    }

    /**
     * @expectedException \Misd\PhoneNumberBundle\Exception\InvalidArgumentException
     */
    public function testProcessInvalidArgumentException()
    {
        $phoneNumber = $this->getMock('libphonenumber\PhoneNumber');

        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();

        $helper = new PhoneNumberHelper($phoneNumberUtil);

        $helper->format($phoneNumber, 'foo');
    }

    public function testDeprecatedClassName() {
        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();

        $helper = new PhoneNumberFormatHelper($phoneNumberUtil);

        $this->assertInstanceOf('Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberHelper', $helper);
    }
}

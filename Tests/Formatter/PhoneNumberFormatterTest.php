<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Tests\Formatter;

use libphonenumber\PhoneNumberFormat;
use Misd\PhoneNumberBundle\Formatter\PhoneNumberFormatter;
use Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberFormatHelper;
use Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberHelper;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Phone number formatter test.
 */
class PhoneNumberFormatterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\libphonenumber\PhoneNumberUtil
     */
    private $phoneNumberUtil;

    /**
     * @var \Misd\PhoneNumberBundle\Formatter\PhoneNumberFormatter
     */
    private $formatter;

    protected function setUp()
    {
        $this->phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();

        $this->formatter = new PhoneNumberFormatter($this->phoneNumberUtil);
    }

    /**
     * @dataProvider formatProvider
     */
    public function testFormat($format, $expectedFormat)
    {
        $phoneNumber = $this->getMock('libphonenumber\PhoneNumber');

        $this->phoneNumberUtil->expects($this->once())->method('format')->with($phoneNumber, $expectedFormat);

        $this->formatter->format($phoneNumber, $format);
    }

    /**
     * 0 => Format
     * 1 => Expected format
     */
    public function formatProvider()
    {
        return array(
            array(PhoneNumberFormat::NATIONAL, PhoneNumberFormat::NATIONAL),
            array('NATIONAL', PhoneNumberFormat::NATIONAL),
        );
    }

    /**
     * @expectedException \Misd\PhoneNumberBundle\Exception\InvalidArgumentException
     */
    public function testFormatInvalidArgumentException()
    {
        $phoneNumber = $this->getMock('libphonenumber\PhoneNumber');

        $this->formatter->format($phoneNumber, 'foo');
    }

    /**
     * @dataProvider isTypeProvider
     */
    public function testIsType($type, $isNationalFormat)
    {
        $phoneNumber = $this->getMock('libphonenumber\PhoneNumber');

        $this->phoneNumberUtil->expects($this->once())->method('getNumberType')->with($phoneNumber)->willReturn(PhoneNumberFormat::NATIONAL);

        $this->assertSame($isNationalFormat, $this->formatter->isType($phoneNumber, $type));
    }

    /**
     * 0 => Format
     * 1 => Expected format
     */
    public function isTypeProvider()
    {
        return array(
            array(PhoneNumberFormat::NATIONAL, true),
            array(PhoneNumberFormat::INTERNATIONAL, false),
        );
    }

    /**
     * @expectedException \Misd\PhoneNumberBundle\Exception\InvalidArgumentException
     */
    public function testIsTypeInvalidArgumentException()
    {
        $phoneNumber = $this->getMock('libphonenumber\PhoneNumber');

        $this->formatter->isType($phoneNumber, 'foo');
    }
}

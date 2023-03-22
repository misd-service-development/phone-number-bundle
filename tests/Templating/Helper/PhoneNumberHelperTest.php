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

use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Misd\PhoneNumberBundle\Exception\InvalidArgumentException;
use Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Phone number templating helper test.
 */
class PhoneNumberHelperTest extends TestCase
{
    use ProphecyTrait;

    protected $phoneNumberUtil;
    protected $helper;

    protected function setUp(): void
    {
        $this->phoneNumberUtil = $this->prophesize(PhoneNumberUtil::class);
        $this->helper = new PhoneNumberHelper($this->phoneNumberUtil->reveal());
    }

    /**
     * @dataProvider processProvider
     */
    public function testProcess($format, $expectedFormat)
    {
        $phoneNumber = $this->prophesize(PhoneNumber::class);
        $this->phoneNumberUtil
            ->format($phoneNumber->reveal(), $expectedFormat)
            ->shouldBeCalledTimes(1)
            ->willReturn('+33600000000');

        $this->helper->format($phoneNumber->reveal(), $format);
    }

    /**
     * 0 => Format
     * 1 => Expected format.
     */
    public function processProvider()
    {
        yield [PhoneNumberFormat::NATIONAL, PhoneNumberFormat::NATIONAL];
        yield ['NATIONAL', PhoneNumberFormat::NATIONAL];
    }

    public function testProcessInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);

        $phoneNumber = $this->prophesize(PhoneNumber::class);

        $this->helper->format($phoneNumber->reveal(), 'foo');
    }

    /**
     * @dataProvider formatOutOfCountryCallingNumberProvider
     */
    public function testFormatOutOfCountryCallingNumber($phoneNumber, $defaultRegion, $regionCode, $expectedResult)
    {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();
        $helper = new PhoneNumberHelper($phoneNumberUtil);

        $phoneNumber = $phoneNumberUtil->parse($phoneNumber, $defaultRegion);

        $this->assertSame($expectedResult, $helper->formatOutOfCountryCallingNumber($phoneNumber, $regionCode));
    }

    public function testFormatAcceptString()
    {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();
        $helper = new PhoneNumberHelper($phoneNumberUtil);
        $result = $helper->format('+37122222222');
        $this->assertEquals('+371 22 222 222', $result);
    }

    public function testFormatAcceptNotAllowValue()
    {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();
        $helper = new PhoneNumberHelper($phoneNumberUtil);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The phone number supplied is not PhoneNumber or string.');
        $helper->format(0037122222222);
    }

    public function formatOutOfCountryCallingNumberAcceptString()
    {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();
        $helper = new PhoneNumberHelper($phoneNumberUtil);
        $result = $helper->formatOutOfCountryCallingNumber('+37122222222', 'LV');
        $this->assertEquals('+371 22 222 222', $result);
    }

    /**
     * 0 => The phone number.
     * 1 => Phone number default region.
     * 2 => Country calling from.
     * 3 => Expected format.
     */
    public function formatOutOfCountryCallingNumberProvider()
    {
        yield ['1-800-854-3680', 'US', 'US', '1 (800) 854-3680'];
        yield ['1-800-854-3680', 'US', 'NL', '00 1 800-854-3680'];
        yield ['1-800-854-3680', 'US', null, '+1 800-854-3680'];
    }
}

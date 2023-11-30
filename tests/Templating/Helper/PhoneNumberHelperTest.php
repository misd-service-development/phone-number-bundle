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
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Phone number templating helper test.
 */
class PhoneNumberHelperTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<PhoneNumberUtil>
     */
    protected ObjectProphecy $phoneNumberUtil;
    protected PhoneNumberHelper $helper;

    protected function setUp(): void
    {
        $this->phoneNumberUtil = $this->prophesize(PhoneNumberUtil::class);
        $this->helper = new PhoneNumberHelper($this->phoneNumberUtil->reveal());
    }

    /**
     * @dataProvider processProvider
     */
    public function testProcess(int|string $format, int $expectedFormat): void
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
     *
     * @return iterable<array{string|int, int}>
     */
    public function processProvider(): iterable
    {
        yield [PhoneNumberFormat::NATIONAL, PhoneNumberFormat::NATIONAL];
        yield ['NATIONAL', PhoneNumberFormat::NATIONAL];
    }

    public function testProcessInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $phoneNumber = $this->prophesize(PhoneNumber::class);

        $this->helper->format($phoneNumber->reveal(), 'foo');
    }

    /**
     * @dataProvider formatOutOfCountryCallingNumberProvider
     */
    public function testFormatOutOfCountryCallingNumber(string $phoneNumber, string $defaultRegion, ?string $regionCode, string $expectedResult): void
    {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();
        $helper = new PhoneNumberHelper($phoneNumberUtil);

        $phoneNumber = $phoneNumberUtil->parse($phoneNumber, $defaultRegion);

        $this->assertSame($expectedResult, $helper->formatOutOfCountryCallingNumber($phoneNumber, $regionCode));
    }

    /**
     * 0 => The phone number.
     * 1 => Phone number default region.
     * 2 => Country calling from.
     * 3 => Expected format.
     *
     * @return iterable<array{string, string, ?string, string}>
     */
    public function formatOutOfCountryCallingNumberProvider()
    {
        yield ['1-800-854-3680', 'US', 'US', '1 (800) 854-3680'];
        yield ['1-800-854-3680', 'US', 'NL', '00 1 800-854-3680'];
        yield ['1-800-854-3680', 'US', null, '+1 800-854-3680'];
    }
}

<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Tests\Serializer\Normalizer;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Misd\PhoneNumberBundle\Serializer\Normalizer\PhoneNumberNormalizer;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Serializer;

/**
 * Phone number serialization test.
 */
class PhoneNumberNormalizerTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists(Serializer::class)) {
            $this->markTestSkipped('The Symfony Serializer is not available.');
        }
    }

    public function testSupportNormalization()
    {
        $normalizer = new PhoneNumberNormalizer($this->prophesize(PhoneNumberUtil::class)->reveal());

        $this->assertTrue($normalizer->supportsNormalization(new PhoneNumber()));
        $this->assertFalse($normalizer->supportsNormalization(new \stdClass()));
    }

    public function testNormalize()
    {
        $phoneNumber = new PhoneNumber();
        $phoneNumber->setRawInput('+33193166989');

        $phoneNumberUtil = $this->prophesize(PhoneNumberUtil::class);
        $phoneNumberUtil->format($phoneNumber, PhoneNumberFormat::E164)->shouldBeCalledTimes(1)->willReturn('+33193166989');

        $normalizer = new PhoneNumberNormalizer($phoneNumberUtil->reveal());

        $this->assertEquals('+33193166989', $normalizer->normalize($phoneNumber));
    }

    public function testSupportDenormalization()
    {
        $normalizer = new PhoneNumberNormalizer($this->prophesize(PhoneNumberUtil::class)->reveal());

        $this->assertTrue($normalizer->supportsDenormalization('+33193166989', 'libphonenumber\PhoneNumber'));
        $this->assertFalse($normalizer->supportsDenormalization(new PhoneNumber(), 'libphonenumber\PhoneNumber'));
        $this->assertFalse($normalizer->supportsDenormalization('+33193166989', 'stdClass'));
    }

    public function testDenormalize()
    {
        $phoneNumber = new PhoneNumber();
        $phoneNumber->setRawInput('+33193166989');

        $phoneNumberUtil = $this->prophesize(PhoneNumberUtil::class);
        $phoneNumberUtil->parse('+33193166989', PhoneNumberUtil::UNKNOWN_REGION)->shouldBeCalledTimes(1)->willReturn($phoneNumber);

        $normalizer = new PhoneNumberNormalizer($phoneNumberUtil->reveal());

        $this->assertSame($phoneNumber, $normalizer->denormalize('+33193166989', 'libphonenumber\PhoneNumber'));
    }

    public function testItDenormalizeNullToNull()
    {
        $phoneNumberUtil = $this->prophesize(PhoneNumberUtil::class);
        $phoneNumberUtil->parse(Argument::cetera())->shouldNotBeCalled();

        $normalizer = new PhoneNumberNormalizer($phoneNumberUtil->reveal());

        $this->assertNull($normalizer->denormalize(null, 'libphonenumber\PhoneNumber'));
    }

    public function testInvalidDateThrowException()
    {
        $this->expectException(UnexpectedValueException::class);

        $phoneNumberUtil = $this->prophesize(PhoneNumberUtil::class);
        $phoneNumberUtil
            ->parse('invalid phone number', PhoneNumberUtil::UNKNOWN_REGION)
            ->shouldBeCalledTimes(1)
            ->willThrow(new NumberParseException(NumberParseException::INVALID_COUNTRY_CODE, ''))
        ;

        $normalizer = new PhoneNumberNormalizer($phoneNumberUtil->reveal());
        $normalizer->denormalize('invalid phone number', 'libphonenumber\PhoneNumber');
    }
}

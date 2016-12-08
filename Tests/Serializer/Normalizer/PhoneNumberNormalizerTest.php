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

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Misd\PhoneNumberBundle\Serializer\Normalizer\PhoneNumberNormalizer;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Phone number serialization test.
 */
class PhoneNumberNormalizerTest extends TestCase
{
    protected function setUp()
    {
        if (!class_exists('Symfony\Component\Serializer\Serializer')) {
            $this->markTestSkipped('The Symfony Serializer is not available.');
        }
    }

    public function testSupportNormalization()
    {
        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();

        $normalizer = new PhoneNumberNormalizer($phoneNumberUtil);

        $this->assertTrue($normalizer->supportsNormalization(new PhoneNumber()));
        $this->assertFalse($normalizer->supportsNormalization(new \stdClass()));
    }

    public function testNormalize()
    {
        $phoneNumber = new PhoneNumber();
        $phoneNumber->setRawInput('+33193166989');

        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();
        $phoneNumberUtil->expects($this->once())->method('format')->with($phoneNumber, PhoneNumberFormat::E164)->will($this->returnValue('+33193166989'));

        $normalizer = new PhoneNumberNormalizer($phoneNumberUtil);

        $this->assertEquals('+33193166989', $normalizer->normalize($phoneNumber));
    }

    public function testSupportDenormalization()
    {
        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();

        $normalizer = new PhoneNumberNormalizer($phoneNumberUtil);

        $this->assertTrue($normalizer->supportsDenormalization('+33193166989', 'libphonenumber\PhoneNumber'));
        $this->assertFalse($normalizer->supportsDenormalization('+33193166989', 'stdClass'));
    }

    public function testDenormalize()
    {
        $phoneNumber = new PhoneNumber();
        $phoneNumber->setRawInput('+33193166989');

        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();

        $phoneNumberUtil->expects($this->once())->method('parse')
            ->with('+33193166989', PhoneNumberUtil::UNKNOWN_REGION)
            ->will($this->returnValue($phoneNumber));

        $normalizer = new PhoneNumberNormalizer($phoneNumberUtil);

        $this->assertSame($phoneNumber, $normalizer->denormalize('+33193166989', 'libphonenumber\PhoneNumber'));
    }

    /**
     * @expectedException \Symfony\Component\Serializer\Exception\UnexpectedValueException
     */
    public function testInvalidDateThrowException()
    {
        $phoneNumberUtil = $this->getMockBuilder('libphonenumber\PhoneNumberUtil')
            ->disableOriginalConstructor()->getMock();

        $phoneNumberUtil->expects($this->once())->method('parse')
            ->with('invalid phone number', PhoneNumberUtil::UNKNOWN_REGION)
            ->willThrowException(new NumberParseException(NumberParseException::INVALID_COUNTRY_CODE, ""));

        $normalizer = new PhoneNumberNormalizer($phoneNumberUtil);
        $normalizer->denormalize('invalid phone number', 'libphonenumber\PhoneNumber');
    }
}

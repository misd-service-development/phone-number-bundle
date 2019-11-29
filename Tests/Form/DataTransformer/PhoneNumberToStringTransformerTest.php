<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Tests\Form\DataTransformer;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Misd\PhoneNumberBundle\Form\DataTransformer\PhoneNumberToStringTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Phone number to string transformer test.
 */
class PhoneNumberToStringTransformerTest extends TestCase
{
    const TRANSFORMATION_FAILED = 'transformation_failed';

    /**
     * @var PhoneNumberUtil
     */
    protected $phoneNumberUtil;

    protected function setUp(): void
    {
        $this->phoneNumberUtil = PhoneNumberUtil::getInstance();
    }

    public function testConstructor()
    {
        $transformer = new PhoneNumberToStringTransformer();

        $this->assertInstanceOf('Symfony\Component\Form\DataTransformerInterface', $transformer);
    }

    /**
     * @dataProvider transformProvider
     */
    public function testTransform($defaultRegion, $format, $actual, $expected)
    {
        $transformer = new PhoneNumberToStringTransformer($defaultRegion, $format);

        $phoneNumberUtil = PhoneNumberUtil::getInstance();
        try {
            $phoneNumber = $phoneNumberUtil->parse($actual, $defaultRegion);
        } catch (NumberParseException $e) {
            $phoneNumber = $actual;
        }

        try {
            $transformed = $transformer->transform($phoneNumber);
        } catch (TransformationFailedException $e) {
            $transformed = self::TRANSFORMATION_FAILED;
        }

        $this->assertSame($expected, $transformed);
    }

    /**
     * 0 => Default region
     * 1 => Format
     * 2 => Actual value
     * 3 => Expected result.
     */
    public function transformProvider()
    {
        return [
            [PhoneNumberUtil::UNKNOWN_REGION, PhoneNumberFormat::INTERNATIONAL, null, ''],
            [PhoneNumberUtil::UNKNOWN_REGION, PhoneNumberFormat::NATIONAL, 'foo', self::TRANSFORMATION_FAILED],
            [PhoneNumberUtil::UNKNOWN_REGION, PhoneNumberFormat::NATIONAL, '0', self::TRANSFORMATION_FAILED],
            [
                PhoneNumberUtil::UNKNOWN_REGION,
                PhoneNumberFormat::INTERNATIONAL,
                '+441234567890',
                '+44 1234 567890',
            ],
            ['GB', PhoneNumberFormat::NATIONAL, '01234567890', '01234 567890'],
        ];
    }

    /**
     * @dataProvider reverseTransformProvider
     */
    public function testReverseTransform($defaultRegion, $actual, $expected)
    {
        $transformer = new PhoneNumberToStringTransformer($defaultRegion);

        try {
            $transformed = $transformer->reverseTransform($actual);
        } catch (TransformationFailedException $e) {
            $transformed = self::TRANSFORMATION_FAILED;
        }

        if ($transformed instanceof PhoneNumber) {
            $transformed = $this->phoneNumberUtil->format($transformed, PhoneNumberFormat::E164);
        }

        $this->assertSame($expected, $transformed);
    }

    /**
     * 0 => Default region
     * 1 => Actual value
     * 2 => Expected result.
     */
    public function reverseTransformProvider()
    {
        return [
            [PhoneNumberUtil::UNKNOWN_REGION, null, null],
            [PhoneNumberUtil::UNKNOWN_REGION, 'foo', self::TRANSFORMATION_FAILED],
            [PhoneNumberUtil::UNKNOWN_REGION, '0', self::TRANSFORMATION_FAILED],
            [PhoneNumberUtil::UNKNOWN_REGION, '+44 1234 567890', '+441234567890'],
            ['GB', '01234 567890', '+441234567890'],
        ];
    }
}

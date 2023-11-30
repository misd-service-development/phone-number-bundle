<?php

declare(strict_types=1);

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
    public const TRANSFORMATION_FAILED = 'transformation_failed';

    private PhoneNumberUtil $phoneNumberUtil;

    protected function setUp(): void
    {
        $this->phoneNumberUtil = PhoneNumberUtil::getInstance();
    }

    public function testConstructor(): void
    {
        $transformer = new PhoneNumberToStringTransformer();

        $this->assertInstanceOf('Symfony\Component\Form\DataTransformerInterface', $transformer);
    }

    /**
     * @dataProvider transformProvider
     */
    public function testTransform(string $defaultRegion, int $format, ?string $actual, string $expected): void
    {
        $transformer = new PhoneNumberToStringTransformer($defaultRegion, $format);

        $phoneNumberUtil = PhoneNumberUtil::getInstance();
        try {
            /* @phpstan-ignore-next-line */
            $phoneNumber = $phoneNumberUtil->parse($actual, $defaultRegion);
        } catch (NumberParseException $e) {
            $phoneNumber = $actual;
        }

        try {
            /* @phpstan-ignore-next-line */
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
     *
     * @return iterable<array{string, int, ?string, string}>
     */
    public function transformProvider(): iterable
    {
        yield [PhoneNumberUtil::UNKNOWN_REGION, PhoneNumberFormat::INTERNATIONAL, null, ''];
        yield [PhoneNumberUtil::UNKNOWN_REGION, PhoneNumberFormat::NATIONAL, 'foo', self::TRANSFORMATION_FAILED];
        yield [PhoneNumberUtil::UNKNOWN_REGION, PhoneNumberFormat::NATIONAL, '0', self::TRANSFORMATION_FAILED];
        yield [
            PhoneNumberUtil::UNKNOWN_REGION,
            PhoneNumberFormat::INTERNATIONAL,
            '+441234567890',
            '+44 1234 567890',
        ];
        yield ['GB', PhoneNumberFormat::NATIONAL, '01234567890', '01234 567890'];
    }

    /**
     * @dataProvider reverseTransformProvider
     */
    public function testReverseTransform(string $defaultRegion, ?string $actual, ?string $expected): void
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
     *
     * @return iterable<array{string, ?string, ?string}>
     */
    public function reverseTransformProvider(): iterable
    {
        yield [PhoneNumberUtil::UNKNOWN_REGION, null, null];
        yield [PhoneNumberUtil::UNKNOWN_REGION, 'foo', self::TRANSFORMATION_FAILED];
        yield [PhoneNumberUtil::UNKNOWN_REGION, '0', self::TRANSFORMATION_FAILED];
        yield [PhoneNumberUtil::UNKNOWN_REGION, '+44 1234 567890', '+441234567890'];
        yield ['GB', '01234 567890', '+441234567890'];
    }
}

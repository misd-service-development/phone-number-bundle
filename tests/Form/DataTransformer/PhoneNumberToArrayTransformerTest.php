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
use Misd\PhoneNumberBundle\Form\DataTransformer\PhoneNumberToArrayTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Phone number to array transformer test.
 */
class PhoneNumberToArrayTransformerTest extends TestCase
{
    public const TRANSFORMATION_FAILED = 'transformation_failed';

    private PhoneNumberUtil $phoneNumberUtil;

    protected function setUp(): void
    {
        $this->phoneNumberUtil = PhoneNumberUtil::getInstance();
    }

    public function testConstructor(): void
    {
        $transformer = new PhoneNumberToArrayTransformer([]);

        $this->assertInstanceOf('Symfony\Component\Form\DataTransformerInterface', $transformer);
    }

    /**
     * @dataProvider transformProvider
     *
     * @param string[]                                      $countryChoices
     * @param array{country: string, number: string}|null   $actual
     * @param array{country: string, number: string}|string $expected
     */
    public function testTransform(array $countryChoices, ?array $actual, array|string $expected): void
    {
        $transformer = new PhoneNumberToArrayTransformer($countryChoices);

        $phoneNumber = null;
        if (\is_array($actual)) {
            try {
                $phoneNumber = $this->phoneNumberUtil->parse($actual['number'], $actual['country']);
            } catch (NumberParseException $e) {
                $phoneNumber = $actual['number'];
            }
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
     * 0 => Country choices
     * 1 => Actual value
     * 2 => Expected result.
     *
     * @return iterable<array{string[], array{country: string, number: string}|null, array{country: string, number: string}|string}>
     */
    public function transformProvider(): iterable
    {
        yield [
            ['GB'],
            null,
            ['country' => '', 'number' => ''],
        ];
        yield [
            ['GB'],
            ['country' => 'GB', 'number' => '01234567890'],
            ['country' => 'GB', 'number' => '01234 567890'],
        ];
        // Wrong country code, but matching country exists.
        yield [
            ['GB', 'JE'],
            ['country' => 'JE', 'number' => '01234567890'],
            ['country' => 'GB', 'number' => '01234 567890'],
        ];
        // Wrong country code, but matching country exists.
        yield [
            ['GB', 'JE'],
            ['country' => 'JE', 'number' => '+441234567890'],
            ['country' => 'GB', 'number' => '01234 567890'],
        ];
        // Country code not in list.
        yield [
            ['US'],
            ['country' => 'GB', 'number' => '01234567890'],
            self::TRANSFORMATION_FAILED,
        ];
        yield [
            ['US'],
            ['country' => 'GB', 'number' => 'foo'],
            self::TRANSFORMATION_FAILED,
        ];
    }

    /**
     * @dataProvider reverseTransformProvider
     *
     * @param string[] $countryChoices
     */
    public function testReverseTransform(array $countryChoices, mixed $actual, ?string $expected): void
    {
        $transformer = new PhoneNumberToArrayTransformer($countryChoices);

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
     * 0 => Country choices
     * 1 => Actual value
     * 2 => Expected result.
     *
     * @return iterable<array{string[], mixed, string|null}>
     */
    public function reverseTransformProvider(): iterable
    {
        yield [
            ['GB'],
            null,
            null,
        ];
        yield [
            ['GB'],
            'foo',
            self::TRANSFORMATION_FAILED,
        ];
        yield [
            ['GB'],
            ['country' => '', 'number' => ''],
            null,
        ];
        yield [
            ['GB'],
            ['country' => 'GB', 'number' => ''],
            null,
        ];
        yield [
            ['GB'],
            ['country' => '', 'number' => 'foo'],
            self::TRANSFORMATION_FAILED,
        ];
        yield [
            ['GB'],
            ['country' => 'GB', 'number' => '01234 567890'],
            '+441234567890',
        ];
        yield [
            ['GB'],
            ['country' => 'GB', 'number' => '+44 1234 567890'],
            '+441234567890',
        ];
        // Country code not in list.
        yield [
            ['US'],
            ['country' => 'GB', 'number' => '+44 1234 567890'],
            self::TRANSFORMATION_FAILED,
        ];
    }
}

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
use Misd\PhoneNumberBundle\Form\DataTransformer\PhoneNumberToArrayTransformer;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Phone number to array transformer test.
 */
class PhoneNumberToArrayTransformerTest extends TestCase
{
    const TRANSFORMATION_FAILED = 'transformation_failed';

    /**
     * @var PhoneNumberUtil
     */
    protected $phoneNumberUtil;

    protected function setUp()
    {
        $this->phoneNumberUtil = PhoneNumberUtil::getInstance();
    }

    public function testConstructor()
    {
        $transformer = new PhoneNumberToArrayTransformer(array());

        $this->assertInstanceOf('Symfony\Component\Form\DataTransformerInterface', $transformer);
    }

    /**
     * @dataProvider transformProvider
     */
    public function testTransform(array $countryChoices, $actual, $expected)
    {
        $transformer = new PhoneNumberToArrayTransformer($countryChoices);

        if (is_array($actual)) {
            try {
                $phoneNumber = $this->phoneNumberUtil->parse($actual['number'], $actual['country']);
            } catch (NumberParseException $e) {
                $phoneNumber = $actual['number'];
            }
        } else {
            $phoneNumber = $actual['number'];
        }

        try {
            $transformed = $transformer->transform($phoneNumber);
        } catch (TransformationFailedException $e) {
            $transformed = self::TRANSFORMATION_FAILED;
        }

        $this->assertSame($expected, $transformed);
    }

    /**
     * 0 => Country choices
     * 1 => Actual value
     * 2 => Expected result
     */
    public function transformProvider()
    {
        return array(
            array(
                array('GB'),
                null,
                array('country' => '', 'number' => ''),
            ),
            array(
                array('GB'),
                array('country' => 'GB', 'number' => '01234567890'),
                array('country' => 'GB', 'number' => '01234 567890'),
            ),
            array(// Wrong country code, but matching country exists.
                array('GB', 'JE'),
                array('country' => 'JE', 'number' => '01234567890'),
                array('country' => 'GB', 'number' => '01234 567890'),
            ),
            array(// Wrong country code, but matching country exists.
                array('GB', 'JE'),
                array('country' => 'JE', 'number' => '+441234567890'),
                array('country' => 'GB', 'number' => '01234 567890'),
            ),
            array(// Country code not in list.
                array('US'),
                array('country' => 'GB', 'number' => '01234567890'),
                self::TRANSFORMATION_FAILED,
            ),
            array(
                array('US'),
                array('country' => 'GB', 'number' => 'foo'),
                self::TRANSFORMATION_FAILED,
            ),
        );
    }

    /**
     * @dataProvider reverseTransformProvider
     */
    public function testReverseTransform(array $countryChoices, $actual, $expected)
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
     * 2 => Expected result
     */
    public function reverseTransformProvider()
    {
        return array(
            array(
                array('GB'),
                null,
                null,
            ),
            array(
                array('GB'),
                'foo',
                self::TRANSFORMATION_FAILED,
            ),
            array(
                array('GB'),
                array('country' => '', 'number' => ''),
                null,
            ),
            array(
                array('GB'),
                array('country' => 'GB', 'number' => ''),
                null,
            ),
            array(
                array('GB'),
                array('country' => '', 'number' => 'foo'),
                self::TRANSFORMATION_FAILED,
            ),
            array(
                array('GB'),
                array('country' => 'GB', 'number' => '01234 567890'),
                '+441234567890',
            ),
            array(
                array('GB'),
                array('country' => 'GB', 'number' => '+44 1234 567890'),
                '+441234567890',
            ),
            array(// Country code not in list.
                array('US'),
                array('country' => 'GB', 'number' => '+44 1234 567890'),
                self::TRANSFORMATION_FAILED,
            ),
        );
    }
}

<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Tests\Validator\Constraints;

use libphonenumber\PhoneNumber as PhoneNumberObject;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumberValidator;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Phone number validator test.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class PhoneNumberValidatorTest extends TestCase
{
    public function testInstanceOf()
    {
        $validator = new PhoneNumberValidator();

        $this->assertInstanceOf('Symfony\Component\Validator\ConstraintValidatorInterface', $validator);
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $violates, $type = null, $defaultRegion = null)
    {
        $validator = new PhoneNumberValidator();
        if (class_exists('Symfony\Component\Validator\Context\ExecutionContext')) {
            $executionContextClass = 'Symfony\Component\Validator\Context\ExecutionContext';
        } else {
            $executionContextClass = 'Symfony\Component\Validator\ExecutionContext';
        }
        $context = $this->getMockBuilder($executionContextClass)
          ->disableOriginalConstructor()->getMock();
        $validator->initialize($context);

        $constraint = new PhoneNumber();
        if (null !== $type) {
            $constraint->type = $type;
        }
        if (null !== $defaultRegion) {
            $constraint->defaultRegion = $defaultRegion;
        }

        if (true === $violates) {
            if ($value instanceof PhoneNumberObject) {
                $constraintValue = PhoneNumberUtil::getInstance()->format($value, PhoneNumberFormat::INTERNATIONAL);
            } else {
                $constraintValue = (string) $value;
            }

            $context->expects($this->once())->method('addViolation')
                ->with(
                    $constraint->getMessage(),
                    array('{{ type }}' => $constraint->type, '{{ value }}' => $constraintValue)
                );
        } else {
            $context->expects($this->never())->method('addViolation');
        }

        $validator->validate($value, $constraint);
    }

    /**
     * 0 => Value
     * 1 => Violates?
     * 2 => Type (optional)
     * 3 => Default region (optional)
     */
    public function validateProvider()
    {
        return array(
            array(null, false),
            array('', false),
            array(PhoneNumberUtil::getInstance()->parse('+441234567890', PhoneNumberUtil::UNKNOWN_REGION), false),
            array(PhoneNumberUtil::getInstance()->parse('+441234567890', PhoneNumberUtil::UNKNOWN_REGION), false, 'fixed_line'),
            array(PhoneNumberUtil::getInstance()->parse('+441234567890', PhoneNumberUtil::UNKNOWN_REGION), true, 'mobile'),
            array(PhoneNumberUtil::getInstance()->parse('+44123456789', PhoneNumberUtil::UNKNOWN_REGION), true),
            array('+441234567890', false),
            array('+441234567890', false, 'fixed_line'),
            array('+441234567890', true, 'mobile'),
            array('+44123456789', true),
            array('+44123456789', true, 'mobile'),
            array('+12015555555', false),
            array('+12015555555', false, 'fixed_line'),
            array('+12015555555', false, 'mobile'),
            array('2015555555', false, null, 'US'),
            array('2015555555', false, 'fixed_line', 'US'),
            array('2015555555', false, 'mobile', 'US'),
            array('01234 567890', false, null, 'GB'),
            array('foo', true),
        );
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testValidateThrowsUnexpectedTypeException()
    {
        $validator = new PhoneNumberValidator();
        $constraint = new PhoneNumber();

        $validator->validate($this, $constraint);
    }
}

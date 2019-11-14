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
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Phone number validator test.
 */
class PhoneNumberValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Validator\Context\ExecutionContextInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumberValidator
     */
    protected $validator;

    protected function setUp()
    {
        if (class_exists('Symfony\Component\Validator\Context\ExecutionContext')) {
            $executionContextClass = 'Symfony\Component\Validator\Context\ExecutionContext';
        } else {
            $executionContextClass = 'Symfony\Component\Validator\ExecutionContext';
        }

        $this->context = $this->getMockBuilder($executionContextClass)
            ->disableOriginalConstructor()
            ->getMock();

        $this->validator = new PhoneNumberValidator();
        $this->validator->initialize($this->context);
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $violates, $type = null, $defaultRegion = null)
    {
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

            if ($this->context instanceof ExecutionContextInterface) {
                $constraintViolationBuilder = $this->getMockBuilder('Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface')
                    ->getMock();

                $constraintViolationBuilder->expects($this->any())
                    ->method('setParameter')
                    ->willReturnSelf();

                $constraintViolationBuilder->expects($this->any())
                    ->method('setCode')
                    ->willReturnSelf();

                $this->context->expects($this->once())
                    ->method('buildViolation')
                    ->with($constraint->getMessage())
                    ->willReturn($constraintViolationBuilder);
            } else {
                $this->context->expects($this->once())
                    ->method('addViolation')
                    ->with($constraint->getMessage(), array(
                        '{{ type }}' => $constraint->type,
                        '{{ value }}' => $constraintValue
                    ));
            }
        } else {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->expects($this->never())
                    ->method('buildViolation');
            } else {
                $this->context->expects($this->never())
                    ->method('addViolation');
            }
        }

        $this->validator->validate($value, $constraint);
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
            array('+447640123456', false, 'pager'),
            array('+441234567890', true, 'pager'),
            array('+447012345678', false, 'personal_number'),
            array('+441234567890', true, 'personal_number'),
            array('+449012345678', false, 'premium_rate'),
            array('+441234567890', true, 'premium_rate'),
            array('+441234567890', true, 'shared_cost'),
            array('+448001234567', false, 'toll_free'),
            array('+441234567890', true, 'toll_free'),
            array('+445512345678', false, 'uan'),
            array('+441234567890', true, 'uan'),
            array('+445612345678', false, 'voip'),
            array('+441234567890', true, 'voip'),
            array('+41860123456789', false, 'voicemail'),
            array('+441234567890', true, 'voicemail'),
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
    public function testValidateThrowsUnexpectedTypeExceptionOnBadValue()
    {
        $constraint = new PhoneNumber();
        $this->validator->validate($this, $constraint);
    }

    protected function createValidator()
    {
        return new PhoneNumberValidator();
    }
}

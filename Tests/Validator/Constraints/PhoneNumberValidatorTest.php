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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Phone number validator test.
 */
class PhoneNumberValidatorTest extends TestCase
{
    /**
     * @var MockObject|\Symfony\Component\Validator\Context\ExecutionContextInterface
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
     *
     * @param string|null $type
     * @param string|null $defaultRegion
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
                    ->with($constraint->getMessage(), [
                        '{{ type }}' => $constraint->type,
                        '{{ value }}' => $constraintValue,
                    ]);
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
     * 3 => Default region (optional).
     */
    public function validateProvider()
    {
        return [
            [null, false],
            ['', false],
            [PhoneNumberUtil::getInstance()->parse('+441234567890', PhoneNumberUtil::UNKNOWN_REGION), false],
            [PhoneNumberUtil::getInstance()->parse('+441234567890', PhoneNumberUtil::UNKNOWN_REGION), false, 'fixed_line'],
            [PhoneNumberUtil::getInstance()->parse('+441234567890', PhoneNumberUtil::UNKNOWN_REGION), true, 'mobile'],
            [PhoneNumberUtil::getInstance()->parse('+44123456789', PhoneNumberUtil::UNKNOWN_REGION), true],
            ['+441234567890', false],
            ['+441234567890', false, 'fixed_line'],
            ['+441234567890', true, 'mobile'],
            ['+44123456789', true],
            ['+44123456789', true, 'mobile'],
            ['+12015555555', false],
            ['+12015555555', false, 'fixed_line'],
            ['+12015555555', false, 'mobile'],
            ['+447640123456', false, 'pager'],
            ['+441234567890', true, 'pager'],
            ['+447012345678', false, 'personal_number'],
            ['+441234567890', true, 'personal_number'],
            ['+449012345678', false, 'premium_rate'],
            ['+441234567890', true, 'premium_rate'],
            ['+448431234567', false, 'shared_cost'],
            ['+441234567890', true, 'shared_cost'],
            ['+448001234567', false, 'toll_free'],
            ['+441234567890', true, 'toll_free'],
            ['+445512345678', false, 'uan'],
            ['+441234567890', true, 'uan'],
            ['+445612345678', false, 'voip'],
            ['+441234567890', true, 'voip'],
            ['+41860123456789', false, 'voicemail'],
            ['+441234567890', true, 'voicemail'],
            ['2015555555', false, null, 'US'],
            ['2015555555', false, 'fixed_line', 'US'],
            ['2015555555', false, 'mobile', 'US'],
            ['01234 567890', false, null, 'GB'],
            ['foo', true],
        ];
    }

    public function testValidateThrowsUnexpectedTypeExceptionOnBadValue()
    {
        $this->expectException(UnexpectedTypeException::class);
        $constraint = new PhoneNumber();
        $this->validator->validate($this, $constraint);
    }

    protected function createValidator()
    {
        return new PhoneNumberValidator();
    }
}

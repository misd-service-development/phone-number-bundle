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

use libphonenumber\PhoneNumberUtil;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumberValidator;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * Phone number validator test.
 */
class PhoneNumberValidatorTest extends TestCase
{
    /**
     * @var \Symfony\Component\Validator\Context\ExecutionContextInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumberValidator
     */
    protected $validator;

    protected function setUp(): void
    {
        $this->context = $this->prophesize(ExecutionContextInterface::class);

        $this->validator = new PhoneNumberValidator(PhoneNumberUtil::getInstance());
        $this->validator->initialize($this->context->reveal());

        $this->context->getObject()->willReturn(new Foo());
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $violates, $type = null, $defaultRegion = null, $regionPath = null)
    {
        $constraint = new PhoneNumber();

        if (null !== $type) {
            $constraint->type = $type;
        }

        if (null !== $defaultRegion) {
            $constraint->defaultRegion = $defaultRegion;
        }

        if (null !== $regionPath) {
            $constraint->regionPath = $regionPath;
        }

        if (true === $violates) {
            $constraintViolationBuilder = $this->prophesize(ConstraintViolationBuilderInterface::class);
            $constraintViolationBuilder->setParameter(Argument::type('string'), Argument::type('string'))->willReturn($constraintViolationBuilder->reveal());
            $constraintViolationBuilder->setCode(Argument::type('string'))->willReturn($constraintViolationBuilder->reveal());
            $constraintViolationBuilder->addViolation()->willReturn($constraintViolationBuilder->reveal());

            $this->context->buildViolation($constraint->getMessage())->shouldBeCalledTimes(1)->willReturn($constraintViolationBuilder->reveal());
        } else {
            $this->context->buildViolation()->shouldNotBeCalled();
        }

        $this->validator->validate($value, $constraint);
    }

    /**
     * @requires PHP 8
     */
    public function testValidateFromAttribute()
    {
        $classMetadata = new ClassMetadata(PhoneNumberDummy::class);
        (new AnnotationLoader())->loadClassMetadata($classMetadata);

        [$constraint1] = $classMetadata->properties['phoneNumber1']->constraints;
        [$constraint2] = $classMetadata->properties['phoneNumber2']->constraints;

        $this->validator->validate('+33606060606', $constraint1);
        $this->validator->validate('+441234567890', $constraint2);

        $this->expectNotToPerformAssertions();
    }

    /**
     * 0 => Value
     * 1 => Violates?
     * 2 => Type (optional)
     * 3 => Default region (optional).
     * 4 => Region Path (optional).
     */
    public function validateProvider()
    {
        return [
            [null, false],
            ['', false],
            [PhoneNumberUtil::getInstance()->parse('+441234567890', PhoneNumberUtil::UNKNOWN_REGION), false],
            [PhoneNumberUtil::getInstance()->parse('+441234567890', PhoneNumberUtil::UNKNOWN_REGION), false, 'fixed_line'],
            [PhoneNumberUtil::getInstance()->parse('+441234567890', PhoneNumberUtil::UNKNOWN_REGION), true, 'mobile'],
            [PhoneNumberUtil::getInstance()->parse('+441234567890', PhoneNumberUtil::UNKNOWN_REGION), false, ['fixed_line', 'mobile']],
            [PhoneNumberUtil::getInstance()->parse('+44123456789', PhoneNumberUtil::UNKNOWN_REGION), true],
            ['+441234567890', false],
            ['+441234567890', false, 'fixed_line'],
            ['+441234567890', true, 'mobile'],
            ['+441234567890', false, ['mobile', 'fixed_line']],
            ['+441234567890', true, ['mobile', 'voip']],
            ['+44123456789', true],
            ['+44123456789', true, 'mobile'],
            ['+12015555555', false],
            ['+12015555555', false, 'fixed_line'],
            ['+12015555555', false, 'mobile'],
            ['+12015555555', false, ['mobile', 'fixed_line']],
            ['+12015555555', true, ['pager', 'voip', 'uan']],
            ['+447640123456', false, 'pager'],
            ['+441234567890', true, 'pager'],
            ['+447012345678', false, 'personal_number'],
            ['+441234567890', true, 'personal_number'],
            ['+449012345678', false, 'premium_rate'],
            ['+441234567890', true, 'premium_rate'],
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
            ['+441234567890', true, 'mobile', null, 'regionPath'],
            ['+33606060606', false, 'mobile', null, 'regionPath'],
        ];
    }

    public function testValidateThrowsUnexpectedTypeExceptionOnBadValue()
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate($this, new PhoneNumber());
    }

    protected function createValidator()
    {
        return new PhoneNumberValidator(PhoneNumberUtil::getInstance());
    }
}

class Foo
{
    public $regionPath = 'GB';
}

class PhoneNumberDummy
{
    #[PhoneNumber(type: [PhoneNumber::MOBILE], defaultRegion: 'FR')]
    private $phoneNumber1;

    #[PhoneNumber(regionPath: 'regionPath')]
    private $phoneNumber2;

    public $regionPath = 'GB';
}

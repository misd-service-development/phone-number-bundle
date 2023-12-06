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

namespace Misd\PhoneNumberBundle\Tests\Validator\Constraints;

use libphonenumber\PhoneNumber as LibPhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumberValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Validator\Mapping\Loader\AttributeLoader;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * Phone number validator test.
 */
class PhoneNumberValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $context;
    private PhoneNumberValidator $validator;

    protected function setUp(): void
    {
        $this->context = $this->createMock(ExecutionContextInterface::class);

        $this->validator = new PhoneNumberValidator(PhoneNumberUtil::getInstance());
        $this->validator->initialize($this->context);

        $this->context->method('getObject')->willReturn(new Foo());
    }

    /**
     * @dataProvider validateProvider
     *
     * @param string[]|string|null $type
     */
    public function testValidate(
        string|LibPhoneNumber|null $value,
        bool $violates,
        array|string $type = null,
        string $defaultRegion = null,
        string $regionPath = null,
        int $format = null
    ): void {
        $constraint = new PhoneNumber($format, $type, $defaultRegion, $regionPath);

        if (true === $violates) {
            $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
            $constraintViolationBuilder
                ->expects($this->exactly(2))
                ->method('setParameter')
                ->with($this->isType('string'), $this->isType('string'))
                ->willReturn($constraintViolationBuilder);
            $constraintViolationBuilder
                ->expects($this->once())
                ->method('setCode')
                ->with($this->isType('string'))
                ->willReturn($constraintViolationBuilder);

            $this->context
                ->expects($this->once())
                ->method('buildViolation')
                ->with($constraint->getMessage())
                ->willReturn($constraintViolationBuilder);
        } else {
            $this->context->expects($this->never())->method('buildViolation');
        }

        $this->validator->validate($value, $constraint);
    }

    /**
     * @requires PHP 8
     */
    public function testValidateFromAttribute(): void
    {
        $classMetadata = new ClassMetadata(PhoneNumberDummy::class);
        if (class_exists(AnnotationLoader::class)) {
            (new AnnotationLoader())->loadClassMetadata($classMetadata);
        } else {
            (new AttributeLoader())->loadClassMetadata($classMetadata);
        }

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
     *
     * @return iterable<array{string|LibPhoneNumber|null, bool, 2?: string|string[]|null, 3?: ?string, 4?: ?string, 5?: ?int}>
     */
    public function validateProvider(): iterable
    {
        yield [null, false];
        yield ['', false];
        yield [PhoneNumberUtil::getInstance()->parse('+441234567890', PhoneNumberUtil::UNKNOWN_REGION), false];
        yield [PhoneNumberUtil::getInstance()->parse('+441234567890', PhoneNumberUtil::UNKNOWN_REGION), false, 'fixed_line'];
        yield [PhoneNumberUtil::getInstance()->parse('+441234567890', PhoneNumberUtil::UNKNOWN_REGION), true, 'mobile'];
        yield [PhoneNumberUtil::getInstance()->parse('+441234567890', PhoneNumberUtil::UNKNOWN_REGION), false, ['fixed_line', 'mobile']];
        yield [PhoneNumberUtil::getInstance()->parse('+44123456789', PhoneNumberUtil::UNKNOWN_REGION), true];
        yield ['+441234567890', false];
        yield ['+441234567890', false, 'fixed_line'];
        yield ['+441234567890', true, 'mobile'];
        yield ['+441234567890', false, ['mobile', 'fixed_line']];
        yield ['+441234567890', true, ['mobile', 'voip']];
        yield ['+44123456789', true];
        yield ['+44123456789', true, 'mobile'];
        yield ['+12015555555', false];
        yield ['+12015555555', false, 'fixed_line'];
        yield ['+12015555555', false, 'mobile'];
        yield ['+12015555555', false, ['mobile', 'fixed_line']];
        yield ['+12015555555', true, ['pager', 'voip', 'uan']];
        yield ['+447640123456', false, 'pager'];
        yield ['+441234567890', true, 'pager'];
        yield ['+447012345678', false, 'personal_number'];
        yield ['+441234567890', true, 'personal_number'];
        yield ['+449012345678', false, 'premium_rate'];
        yield ['+441234567890', true, 'premium_rate'];
        yield ['+441234567890', true, 'shared_cost'];
        yield ['+448001234567', false, 'toll_free'];
        yield ['+441234567890', true, 'toll_free'];
        yield ['+445512345678', false, 'uan'];
        yield ['+441234567890', true, 'uan'];
        yield ['+445612345678', false, 'voip'];
        yield ['+441234567890', true, 'voip'];
        yield ['+41860123456789', false, 'voicemail'];
        yield ['+441234567890', true, 'voicemail'];
        yield ['2015555555', false, null, 'US'];
        yield ['2015555555', false, 'fixed_line', 'US'];
        yield ['2015555555', false, 'mobile', 'US'];
        yield ['01234 567890', false, null, 'GB'];
        yield ['foo', true];
        yield ['+441234567890', true, 'mobile', null, 'regionPath'];
        yield ['+33606060606', false, 'mobile', null, 'regionPath'];
        yield ['+33606060606', false, 'mobile', null, null, PhoneNumberFormat::E164];
        yield ['2015555555', true, null, null, null, PhoneNumberFormat::E164];
    }

    public function testValidateThrowsUnexpectedTypeExceptionOnBadValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate($this, new PhoneNumber());
    }

    protected function createValidator(): PhoneNumberValidator
    {
        return new PhoneNumberValidator(PhoneNumberUtil::getInstance());
    }
}

class Foo
{
    public string $regionPath = 'GB';
}

class PhoneNumberDummy
{
    #[PhoneNumber(type: [PhoneNumber::MOBILE], defaultRegion: 'FR')]
    /* @phpstan-ignore-next-line */
    private PhoneNumber $phoneNumber1;

    #[PhoneNumber(regionPath: 'regionPath')]
    /* @phpstan-ignore-next-line */
    private PhoneNumber $phoneNumber2;

    public string $regionPath = 'GB';
}

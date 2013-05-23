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
    public function testValidate($value, $violates, $defaultRegion = 'ZZ')
    {
        $validator = new PhoneNumberValidator();
        $context = $this->getMock('Symfony\Component\Validator\ExecutionContextInterface');
        $validator->initialize($context);

        $constraint = new PhoneNumber();
        $constraint->defaultRegion = $defaultRegion;

        if (true === $violates) {
            $context->expects($this->once())->method('addViolation');
        } else {
            $context->expects($this->never())->method('addViolation');
        }

        $validator->validate($value, $constraint);
    }

    /**
     * 0 => Value
     * 1 => Violates?
     * 2 => Default region (optional)
     */
    public function validateProvider()
    {
        return array(
            array(null, false),
            array('', false),
            array(PhoneNumberUtil::getInstance()->parse('+441234567890', 'ZZ'), false),
            array(PhoneNumberUtil::getInstance()->parse('+44123456789', 'ZZ'), true),
            array('+441234567890', false),
            array('01234 567890', false, 'GB'),
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

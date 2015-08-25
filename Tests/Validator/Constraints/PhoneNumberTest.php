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

use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Phone number constraint test.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class PhoneNumberTest extends TestCase
{
    public function testProperties()
    {
        $phoneNumber = new PhoneNumber();

        $this->assertObjectHasAttribute('message', $phoneNumber);
        $this->assertObjectHasAttribute('type', $phoneNumber);
        $this->assertObjectHasAttribute('defaultRegion', $phoneNumber);
    }

    /**
     * @dataProvider messageProvider
     */
    public function testMessage($message = null, $type = null, $expectedMessage)
    {
        $phoneNumber = new PhoneNumber();

        if (null !== $message) {
            $phoneNumber->message = $message;
        }
        if (null !== $type) {
            $phoneNumber->type = $type;
        }

        $this->assertSame($expectedMessage, $phoneNumber->getMessage());
    }

    /**
     * 0 => Message (optional)
     * 1 => Type (optional)
     * 2 => Expected message
     */
    public function messageProvider()
    {
        return array(
            array(null, null, 'invalid_phone_number'),
            array(null, 'fixed_line', 'invalid_fixed_phone_number'),
            array(null, 'mobile', 'invalid_mobile_phone_number'),
            array('foo', null, 'foo'),
            array('foo', 'fixed_line', 'foo'),
            array('foo', 'mobile', 'foo'),
        );
    }
}

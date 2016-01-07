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
            array(null, null, 'This value is not a valid phone number.'),
            array(null, 'fixed_line', 'This value is not a valid fixed-line number.'),
            array(null, 'mobile', 'This value is not a valid mobile number.'),
            array(null, 'pager', 'This value is not a valid pager number.'),
            array(null, 'personal_number', 'This value is not a valid personal number.'),
            array(null, 'premium_rate', 'This value is not a valid premium-rate number.'),
            array(null, 'shared_cost', 'This value is not a valid shared-cost number.'),
            array(null, 'toll_free', 'This value is not a valid toll-free number.'),
            array(null, 'uan', 'This value is not a valid UAN.'),
            array(null, 'voip', 'This value is not a valid VoIP number.'),
            array(null, 'voicemail', 'This value is not a valid voicemail access number.'),
            array('foo', null, 'foo'),
            array('foo', 'fixed_line', 'foo'),
            array('foo', 'mobile', 'foo'),
        );
    }
}

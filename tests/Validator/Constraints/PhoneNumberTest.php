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
use PHPUnit\Framework\TestCase;

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
    public function testMessage($message, $type, $expectedMessage)
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
     * 2 => Expected message.
     */
    public function messageProvider()
    {
        return [
            [null, null, 'This value is not a valid phone number.'],
            [null, 'fixed_line', 'This value is not a valid fixed-line number.'],
            [null, 'mobile', 'This value is not a valid mobile number.'],
            [null, 'pager', 'This value is not a valid pager number.'],
            [null, 'personal_number', 'This value is not a valid personal number.'],
            [null, 'premium_rate', 'This value is not a valid premium-rate number.'],
            [null, 'shared_cost', 'This value is not a valid shared-cost number.'],
            [null, 'toll_free', 'This value is not a valid toll-free number.'],
            [null, 'uan', 'This value is not a valid UAN.'],
            [null, 'voip', 'This value is not a valid VoIP number.'],
            [null, 'voicemail', 'This value is not a valid voicemail access number.'],
            [null, ['fixed_line', 'voip'], 'This value is not a valid number.'],
            [null, ['uan', 'fixed_line'], 'This value is not a valid number.'],
            ['foo', null, 'foo'],
            ['foo', 'fixed_line', 'foo'],
            ['foo', 'mobile', 'foo'],
        ];
    }
}

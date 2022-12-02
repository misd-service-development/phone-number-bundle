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

use libphonenumber\PhoneNumberFormat;
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
        $this->assertObjectHasAttribute('regionPath', $phoneNumber);
    }

    /**
     * @dataProvider messageProvider
     */
    public function testMessage($message, $type, $format, $expectedMessage)
    {
        $phoneNumber = new PhoneNumber($format, $type, null, null, $message);
        $this->assertSame($expectedMessage, $phoneNumber->getMessage());
        $this->assertSame($format, $phoneNumber->format);
    }

    /**
     * 0 => Message (optional)
     * 1 => Type (optional)
     * 2 => Format (optional)
     * 3 => Expected message.
     */
    public function messageProvider()
    {
        return [
            [null, null, null, 'This value is not a valid phone number.'],
            [null, 'fixed_line', null, 'This value is not a valid fixed-line number.'],
            [null, 'mobile', null, 'This value is not a valid mobile number.'],
            [null, 'pager', null, 'This value is not a valid pager number.'],
            [null, 'personal_number', null, 'This value is not a valid personal number.'],
            [null, 'premium_rate', null, 'This value is not a valid premium-rate number.'],
            [null, 'shared_cost', null, 'This value is not a valid shared-cost number.'],
            [null, 'toll_free', null, 'This value is not a valid toll-free number.'],
            [null, 'uan', null, 'This value is not a valid UAN.'],
            [null, 'voip', null, 'This value is not a valid VoIP number.'],
            [null, 'voicemail', null, 'This value is not a valid voicemail access number.'],
            [null, ['fixed_line', 'voip'], null, 'This value is not a valid phone number.'],
            [null, ['uan', 'fixed_line'], null, 'This value is not a valid phone number.'],
            ['foo', null, null, 'foo'],
            ['foo', 'fixed_line', null, 'foo'],
            ['foo', 'mobile', null, 'foo'],
            [null, null, PhoneNumberFormat::E164, 'This value is not a valid phone number.'],
        ];
    }
}

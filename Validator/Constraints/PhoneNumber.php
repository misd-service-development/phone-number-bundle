<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Validator\Constraints;

use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Validator\Constraint;

/**
 * Phone number constraint.
 *
 * @Annotation
 */
class PhoneNumber extends Constraint
{
    const ANY             = 'any';
    const FIXED_LINE      = 'fixed_line';
    const MOBILE          = 'mobile';
    const PAGER           = 'pager';
    const PERSONAL_NUMBER = 'personal_number';
    const PREMIUM_RATE    = 'premium_rate';
    const SHARED_COST     = 'shared_cost';
    const TOLL_FREE       = 'toll_free';
    const UAN             = 'uan';
    const VOIP            = 'voip';
    const VOICEMAIL       = 'voicemail';

    const INVALID_PHONE_NUMBER_ERROR = 'ca23f4ca-38f4-4325-9bcc-eb570a4abe7f';

    protected static $errorNames = array(
        self::INVALID_PHONE_NUMBER_ERROR => 'INVALID_PHONE_NUMBER_ERROR',
    );

    public $message = null;
    public $type = self::ANY;
    public $defaultRegion = PhoneNumberUtil::UNKNOWN_REGION;


    /**
     * Returns whether the given type is valid.
     *
     * @param string $type
     *
     * @return bool
     */
    public function isValidType($type)
    {
        return in_array($type, array(
            self::ANY,
            self::FIXED_LINE,
            self::MOBILE,
            self::PAGER,
            self::PERSONAL_NUMBER,
            self::PREMIUM_RATE,
            self::SHARED_COST,
            self::TOLL_FREE,
            self::UAN,
            self::VOIP,
            self::VOICEMAIL,
        ), true);
    }

    /**
     * Returns the first configured type.
     *
     * @return string
     */
    public function getType()
    {
        if (is_string($this->type)) {
            $type = $this->type;
        } elseif (is_array($this->type)) {
            $type = reset($this->type);
        } else {
            $type = null;
        }

        return $this->isValidType($type) ? $type : self::ANY;
    }

    /**
     * Returns the configured types.
     *
     * @return array
     */
    public function getTypes()
    {
        if (is_string($this->type)) {
            $types = array($this->type);
        } elseif (is_array($this->type)) {
            $types = $this->type;
        } else {
            $types =  array();
        }

        $types = array_filter($types, array($this, 'isValidType'));

        return empty($types) ? array(self::ANY) : $types;
    }

    /**
     * Returns the violation message for the first configured type.
     *
     * @return null|string
     */
    public function getMessage()
    {
        // TODO Deal with multiple types

        if (null !== $this->message) {
            return $this->message;
        }

        switch ($this->getType()) {
            case self::FIXED_LINE:
                return 'This value is not a valid fixed-line number.';
            case self::MOBILE:
                return 'This value is not a valid mobile number.';
            case self::PAGER:
                return 'This value is not a valid pager number.';
            case self::PERSONAL_NUMBER:
                return 'This value is not a valid personal number.';
            case self::PREMIUM_RATE:
                return 'This value is not a valid premium-rate number.';
            case self::SHARED_COST:
                return 'This value is not a valid shared-cost number.';
            case self::TOLL_FREE:
                return 'This value is not a valid toll-free number.';
            case self::UAN:
                return 'This value is not a valid UAN.';
            case self::VOIP:
                return 'This value is not a valid VoIP number.';
            case self::VOICEMAIL:
                return 'This value is not a valid voicemail access number.';
        }

        return 'This value is not a valid phone number.';
    }
}

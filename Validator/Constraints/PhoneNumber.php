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
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 *
 * @Annotation
 */
class PhoneNumber extends Constraint
{
    const ANY = 'any';
    const FIXED_LINE = 'fixed_line';
    const MOBILE = 'mobile';

    private $anyMessage = 'This value is not a valid phone number.';
    private $fixedLineMessage = 'This value is not a valid fixed-line number.';
    private $mobileMessage = 'This value is not a valid mobile number.';

    public $message = null;
    public $type = self::ANY;
    public $defaultRegion = PhoneNumberUtil::UNKNOWN_REGION;

    public function getType()
    {
        switch ($this->type) {
            case self::FIXED_LINE:
            case self::MOBILE:
                return $this->type;
        }

        return self::ANY;
    }

    public function getMessage()
    {
        if (null !== $this->message) {
            return $this->message;
        }

        switch ($this->type) {
            case self::FIXED_LINE:
                return $this->fixedLineMessage;
            case self::MOBILE:
                return $this->mobileMessage;
        }

        return $this->anyMessage;
    }
}

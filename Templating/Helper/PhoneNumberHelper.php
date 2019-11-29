<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Templating\Helper;

use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;
use Misd\PhoneNumberBundle\Exception\InvalidArgumentException;

/**
 * Phone number templating helper.
 */
class PhoneNumberHelper
{
    /**
     * Phone number utility.
     *
     * @var PhoneNumberUtil
     */
    protected $phoneNumberUtil;

    /**
     * Constructor.
     *
     * @param PhoneNumberUtil $phoneNumberUtil phone number utility
     */
    public function __construct(PhoneNumberUtil $phoneNumberUtil)
    {
        $this->phoneNumberUtil = $phoneNumberUtil;
    }

    /**
     * Format a phone number.
     *
     * @param PhoneNumber $phoneNumber phone number
     * @param int|string  $format      format, or format constant name
     *
     * @return string formatted phone number
     *
     * @throws InvalidArgumentException if an argument is invalid
     */
    public function format(PhoneNumber $phoneNumber, $format = PhoneNumberFormat::INTERNATIONAL)
    {
        if (true === is_string($format)) {
            $constant = '\libphonenumber\PhoneNumberFormat::'.$format;

            if (false === defined($constant)) {
                throw new InvalidArgumentException('The format must be either a constant value or name in libphonenumber\PhoneNumberFormat');
            }

            $format = constant('\libphonenumber\PhoneNumberFormat::'.$format);
        }

        return $this->phoneNumberUtil->format($phoneNumber, $format);
    }

    /**
     * @param PhoneNumber $phoneNumber phone number
     * @param int|string  $type        phoneNumberType, or PhoneNumberType constant name
     *
     * @return bool
     *
     * @throws InvalidArgumentException if type argument is invalid
     */
    public function isType(PhoneNumber $phoneNumber, $type = PhoneNumberType::UNKNOWN)
    {
        if (true === is_string($type)) {
            $constant = '\libphonenumber\PhoneNumberType::'.$type;

            if (false === defined($constant)) {
                throw new InvalidArgumentException('The format must be either a constant value or name in libphonenumber\PhoneNumberType');
            }

            $type = constant('\libphonenumber\PhoneNumberType::'.$type);
        }

        return $this->phoneNumberUtil->getNumberType($phoneNumber) === $type ? true : false;
    }
}

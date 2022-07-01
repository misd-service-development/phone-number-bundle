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
    public function format(PhoneNumber $phoneNumber, $format = PhoneNumberFormat::INTERNATIONAL): string
    {
        if (true === \is_string($format)) {
            $constant = '\libphonenumber\PhoneNumberFormat::'.$format;

            if (false === \defined($constant)) {
                throw new InvalidArgumentException('The format must be either a constant value or name in libphonenumber\PhoneNumberFormat');
            }

            $format = \constant('\libphonenumber\PhoneNumberFormat::'.$format);
        }

        return $this->phoneNumberUtil->format($phoneNumber, $format);
    }

    /**
     * Formats this phone number for out-of-country dialing purposes.
     *
     * @param PhoneNumber $phoneNumber phone number
     * @param string|null $regionCode  The ISO 3166-1 alpha-2 country code
     */
    public function formatOutOfCountryCallingNumber(PhoneNumber $phoneNumber, $regionCode): string
    {
        return $this->phoneNumberUtil->formatOutOfCountryCallingNumber($phoneNumber, $regionCode);
    }

    /**
     * @param PhoneNumber $phoneNumber phone number
     * @param int|string  $type        phoneNumberType, or PhoneNumberType constant name
     *
     * @throws InvalidArgumentException if type argument is invalid
     */
    public function isType(PhoneNumber $phoneNumber, $type = PhoneNumberType::UNKNOWN): bool
    {
        if (true === \is_string($type)) {
            $constant = '\libphonenumber\PhoneNumberType::'.$type;

            if (false === \defined($constant)) {
                throw new InvalidArgumentException('The format must be either a constant value or name in libphonenumber\PhoneNumberType');
            }

            $type = \constant('\libphonenumber\PhoneNumberType::'.$type);
        }

        return $this->phoneNumberUtil->getNumberType($phoneNumber) === $type;
    }
}

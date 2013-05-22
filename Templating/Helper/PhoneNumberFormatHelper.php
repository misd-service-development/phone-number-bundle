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
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Templating\Helper\HelperInterface;

/**
 * Phone number format templating helper.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class PhoneNumberFormatHelper implements HelperInterface
{
    /**
     * Phone number utility.
     *
     * @var PhoneNumberUtil
     */
    protected $phoneNumberUtil;

    /**
     * Charset.
     *
     * @var string
     */
    protected $charset = 'UTF-8';

    /**
     * Constructor.
     *
     * @param PhoneNumberUtil $phoneNumberUtil Phone number utility.
     */
    public function __construct(PhoneNumberUtil $phoneNumberUtil)
    {
        $this->phoneNumberUtil = $phoneNumberUtil;
    }

    /**
     * {@inheritdoc}
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * {@inheritdoc}
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'phone_number_format';
    }

    /**
     * Format a phone number.
     *
     * @param PhoneNumber $phoneNumber Phone number.
     * @param int         $format      Format.
     *
     * @return string Formatted phone number.
     */
    public function format(PhoneNumber $phoneNumber, $format = PhoneNumberFormat::INTERNATIONAL)
    {
        if (true === is_string($format)) {
            $format = constant('\libphonenumber\PhoneNumberFormat::' . $format);
        }

        return $this->phoneNumberUtil->format($phoneNumber, $format);
    }
}

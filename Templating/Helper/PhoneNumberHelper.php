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
use Misd\PhoneNumberBundle\Formatter\PhoneNumberFormatter;
use Symfony\Component\Templating\Helper\HelperInterface;

/**
 * Phone number templating helper.
 */
class PhoneNumberHelper implements HelperInterface
{
    /**
     * Phone number formatter.
     *
     * @var PhoneNumberFormatter
     */
    protected $formatter;

    /**
     * Charset.
     *
     * @var string
     */
    protected $charset = 'UTF-8';

    /**
     * Constructor.
     *
     * @param PhoneNumberFormatter $formatter Phone number formatter.
     */
    public function __construct($formatter)
    {
        if ($formatter instanceof PhoneNumberUtil) {
            // throw deprecation message
            $formatter = new PhoneNumberFormatter($formatter);
        }
        $this->formatter = $formatter;
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
        return 'phone_number_helper';
    }

    /**
     * Format a phone number.
     *
     * @param PhoneNumber $phoneNumber Phone number.
     * @param int|string  $format      Format, or format constant name.
     *
     * @return string Formatted phone number.
     */
    public function format(PhoneNumber $phoneNumber, $format = PhoneNumberFormat::INTERNATIONAL)
    {
        return $this->formatter->format($phoneNumber, $format);
    }

    /**
     * @param PhoneNumber $phoneNumber Phone number.
     * @param int|string  $type      PhoneNumberType, or PhoneNumberType constant name.
     *
     * @return bool
     */
    public function isType(PhoneNumber $phoneNumber, $type = PhoneNumberType::UNKNOWN)
    {
        return $this->formatter->isType($phoneNumber, $type);
    }
}

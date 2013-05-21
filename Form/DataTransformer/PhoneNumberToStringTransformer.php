<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Form\DataTransformer;

use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Phone number to string transformer.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class PhoneNumberToStringTransformer implements DataTransformerInterface
{
    private $defaultRegion;
    private $format;

    public function __construct($defaultRegion = 'ZZ', $format = PhoneNumberFormat::INTERNATIONAL)
    {
        $this->defaultRegion = $defaultRegion;
        $this->format = $format;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($phoneNumber)
    {
        if (null === $phoneNumber) {
            return '';
        }

        $util = PhoneNumberUtil::getInstance();

        return $util->format($phoneNumber, $this->format);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($string)
    {
        if (!$string) {
            return null;
        }

        $util = PhoneNumberUtil::getInstance();

        return $util->parse($string, $this->defaultRegion);
    }
}

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

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Phone number to string transformer.
 */
class PhoneNumberToStringTransformer implements DataTransformerInterface
{
    /**
     * Default region code.
     *
     * @var string
     */
    private $defaultRegion;

    /**
     * Display format.
     *
     * @var int
     */
    private $format;

    /**
     * Constructor.
     *
     * @param string $defaultRegion default region code
     * @param int    $format        display format
     */
    public function __construct(
        $defaultRegion = PhoneNumberUtil::UNKNOWN_REGION,
        $format = PhoneNumberFormat::INTERNATIONAL
    ) {
        $this->defaultRegion = $defaultRegion;
        $this->format = $format;
    }

    public function transform($value): string
    {
        if (null === $value) {
            return '';
        }

        if (false === $value instanceof PhoneNumber) {
            throw new TransformationFailedException('Expected a \libphonenumber\PhoneNumber.');
        }

        $util = PhoneNumberUtil::getInstance();

        if (PhoneNumberFormat::NATIONAL === $this->format) {
            return $util->formatOutOfCountryCallingNumber($value, $this->defaultRegion);
        }

        return $util->format($value, $this->format);
    }

    public function reverseTransform($value): ?PhoneNumber
    {
        if (!$value && '0' !== $value) {
            return null;
        }

        if (preg_match('/\p{L}/u', $value)) {
            throw new TransformationFailedException('The number can not contain letters.');
        }

        try {
            return PhoneNumberUtil::getInstance()->parse($value, $this->defaultRegion);
        } catch (NumberParseException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}

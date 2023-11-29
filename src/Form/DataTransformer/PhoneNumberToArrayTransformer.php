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
 * Phone number to array transformer.
 */
class PhoneNumberToArrayTransformer implements DataTransformerInterface
{
    /**
     * @var array
     */
    private $countryChoices;

    /**
     * Constructor.
     */
    public function __construct(array $countryChoices)
    {
        $this->countryChoices = $countryChoices;
    }

    public function transform($value): array
    {
        if (null === $value) {
            return ['country' => '', 'number' => ''];
        }

        if (false === $value instanceof PhoneNumber) {
            throw new TransformationFailedException('Expected a \libphonenumber\PhoneNumber.');
        }

        $util = PhoneNumberUtil::getInstance();

        if (false === \in_array($util->getRegionCodeForNumber($value), $this->countryChoices)) {
            throw new TransformationFailedException('Invalid country.');
        }

        return [
            'country' => $util->getRegionCodeForNumber($value),
            'number' => $util->format($value, PhoneNumberFormat::NATIONAL),
        ];
    }

    public function reverseTransform($value): ?PhoneNumber
    {
        if (!$value) {
            return null;
        }

        if (!\is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        if ('' === trim($value['number'] ?? '')) {
            return null;
        }

        $util = PhoneNumberUtil::getInstance();

        if (preg_match('/\p{L}/u', $value['number'])) {
            throw new TransformationFailedException('The number can not contain letters.');
        }

        try {
            $phoneNumber = $util->parse($value['number'], $value['country']);
        } catch (NumberParseException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }

        if (null !== $phoneNumber && false === \in_array($util->getRegionCodeForNumber($phoneNumber), $this->countryChoices)) {
            throw new TransformationFailedException('Invalid country.');
        }

        return $phoneNumber;
    }
}

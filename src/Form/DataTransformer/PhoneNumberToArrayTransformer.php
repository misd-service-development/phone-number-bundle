<?php

declare(strict_types=1);

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
 * @implements DataTransformerInterface<PhoneNumber, array{country: string, number: string}>
 */
class PhoneNumberToArrayTransformer implements DataTransformerInterface
{
    /**
     * @var string[]
     */
    private array $countryChoices;

    /**
     * @param string[] $countryChoices
     */
    public function __construct(array $countryChoices)
    {
        $this->countryChoices = $countryChoices;
    }

    /**
     * @return array{country: string, number: string}
     */
    public function transform(mixed $value): array
    {
        if (null === $value) {
            return ['country' => '', 'number' => ''];
        }

        if (!$value instanceof PhoneNumber) {
            throw new TransformationFailedException('Expected a \libphonenumber\PhoneNumber.');
        }

        $util = PhoneNumberUtil::getInstance();

        if (false === \in_array($util->getRegionCodeForNumber($value), $this->countryChoices)) {
            throw new TransformationFailedException('Invalid country.');
        }

        return [
            'country' => (string) $util->getRegionCodeForNumber($value),
            'number' => $util->format($value, PhoneNumberFormat::NATIONAL),
        ];
    }

    public function reverseTransform(mixed $value): ?PhoneNumber
    {
        if (!$value) {
            return null;
        }

        if (!\is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        /* @phpstan-ignore-next-line */
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

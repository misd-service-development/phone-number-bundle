<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Serializer\Normalizer;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Phone number serialization for Symfony serializer.
 */
class PhoneNumberNormalizer implements NormalizerInterface, DenormalizerInterface
{
    private PhoneNumberUtil $phoneNumberUtil;
    private string $region;
    private int $format;

    /**
     * @param PhoneNumberUtil $phoneNumberUtil phone number utility
     * @param string          $region          region code
     * @param int             $format          display format
     */
    public function __construct(PhoneNumberUtil $phoneNumberUtil, string $region = PhoneNumberUtil::UNKNOWN_REGION, int $format = PhoneNumberFormat::E164)
    {
        $this->phoneNumberUtil = $phoneNumberUtil;
        $this->region = $region;
        $this->format = $format;
    }

    /**
     * @param array<mixed> $context
     *
     * @throws InvalidArgumentException
     */
    public function normalize(mixed $object, string $format = null, array $context = []): string
    {
        return $this->phoneNumberUtil->format($object, $this->format);
    }

    /**
     * @param array<mixed> $context
     */
    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof PhoneNumber;
    }

    /**
     * @param array<mixed> $context
     *
     * @throws UnexpectedValueException
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): ?PhoneNumber
    {
        if (null === $data) {
            return null;
        }

        try {
            return $this->phoneNumberUtil->parse($data, $this->region);
        } catch (NumberParseException $e) {
            throw new UnexpectedValueException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array<mixed> $context
     */
    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return PhoneNumber::class === $type && \is_string($data);
    }

    /**
     * for symfony/serializer >= 6.3.
     */
    public function getSupportedTypes(?string $format): array
    {
        return [PhoneNumber::class => false];
    }
}

<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

/**
 * Phone number Doctrine mapping type.
 */
class PhoneNumberType extends Type
{
    /**
     * Phone number type name.
     */
    public const NAME = 'phone_number';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getVarcharTypeDeclarationSQL(['length' => 35]);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof PhoneNumber) {
            throw new ConversionException('Expected \libphonenumber\PhoneNumber, got '.\gettype($value));
        }

        $util = PhoneNumberUtil::getInstance();

        return $util->format($value, PhoneNumberFormat::E164);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?PhoneNumber
    {
        if (null === $value || $value instanceof PhoneNumber) {
            return $value;
        }

        $util = PhoneNumberUtil::getInstance();

        try {
            return $util->parse($value, PhoneNumberUtil::UNKNOWN_REGION);
        } catch (NumberParseException $e) {
            throw ConversionException::conversionFailed($value, self::NAME);
        }
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}

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
use Doctrine\DBAL\Types\Exception\InvalidType;
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

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        // DBAL < 4
        if (method_exists(AbstractPlatform::class, 'getVarcharTypeDeclarationSQL')) {
            return $platform->getVarcharTypeDeclarationSQL(['length' => $column['length'] ?? 35]);
        }

        // DBAL 4
        return $platform->getStringTypeDeclarationSQL(['length' => $column['length'] ?? 35]);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof PhoneNumber) {
            throw new ConversionException('Expected \libphonenumber\PhoneNumber, got '.\gettype($value));
        }

        return PhoneNumberUtil::getInstance()->format($value, PhoneNumberFormat::E164);
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
            if (method_exists(ConversionException::class, 'conversionFailed')) {
                // DBAL < 4
                throw ConversionException::conversionFailed($value, self::NAME);
            }

            // DBAL 4
            // @phpstan-ignore-next-line
            throw InvalidType::new($value, self::NAME, ['null', 'string']);
        }
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}

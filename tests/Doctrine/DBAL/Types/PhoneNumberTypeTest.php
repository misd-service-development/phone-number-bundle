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

namespace Misd\PhoneNumberBundle\Tests\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Misd\PhoneNumberBundle\Doctrine\DBAL\Types\PhoneNumberType;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Phone number type test.
 */
class PhoneNumberTypeTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<AbstractPlatform>
     */
    private ObjectProphecy $platform;
    private Type $type;
    private PhoneNumberUtil $phoneNumberUtil;

    public static function setUpBeforeClass(): void
    {
        Type::addType('phone_number', PhoneNumberType::class);
    }

    protected function setUp(): void
    {
        $this->platform = $this->prophesize(AbstractPlatform::class);
        if (method_exists(AbstractPlatform::class, 'getVarcharTypeDeclarationSQL')) {
            // DBAL < 4
            $this->platform->getVarcharTypeDeclarationSQL()->willReturn('DUMMYVARCHAR()');
        } else {
            // DBAL 4
            $this->platform->getStringTypeDeclarationSQL()->willReturn('DUMMYVARCHAR()');
        }

        $this->type = Type::getType('phone_number');
        $this->phoneNumberUtil = PhoneNumberUtil::getInstance();
    }

    public function testInstanceOf(): void
    {
        $this->assertInstanceOf(Type::class, $this->type);
    }

    public function testGetName(): void
    {
        $this->assertSame('phone_number', $this->type->getName());
    }

    public function testGetSQLDeclaration(): void
    {
        if (method_exists(AbstractPlatform::class, 'getVarcharTypeDeclarationSQL')) {
            // DBAL < 4
            $this->platform->getVarcharTypeDeclarationSQL(['length' => 35])->willReturn('DUMMYVARCHAR()');
        } else {
            // DBAL 4
            $this->platform->getStringTypeDeclarationSQL(['length' => 35])->willReturn('DUMMYVARCHAR()');
        }
        $this->assertSame('DUMMYVARCHAR()', $this->type->getSQLDeclaration([], $this->platform->reveal()));
    }

    public function testConvertToDatabaseValueWithNull(): void
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform->reveal()));
    }

    public function testConvertToDatabaseValueWithPhoneNumber(): void
    {
        $phoneNumber = $this->phoneNumberUtil->parse('+441234567890', PhoneNumberUtil::UNKNOWN_REGION);

        $this->assertSame('+441234567890', $this->type->convertToDatabaseValue($phoneNumber, $this->platform->reveal()));
    }

    public function testConvertToDatabaseValueFailure(): void
    {
        $this->expectException(ConversionException::class);

        $this->type->convertToDatabaseValue('foo', $this->platform->reveal());
    }

    public function testConvertToPHPValueWithNull(): void
    {
        $this->assertNull($this->type->convertToPHPValue(null, $this->platform->reveal()));
    }

    public function testConvertToPHPValueWithPhoneNumber(): void
    {
        $phoneNumber = $this->type->convertToPHPValue('+441234567890', $this->platform->reveal());

        $this->assertInstanceOf('libphoneNumber\PhoneNumber', $phoneNumber);
        $this->assertSame('+441234567890', $this->phoneNumberUtil->format($phoneNumber, PhoneNumberFormat::E164));
    }

    public function testConvertToPHPValueWithAPhoneNumberInstance(): void
    {
        $expectedPhoneNumber = $this->phoneNumberUtil->parse('+441234567890', PhoneNumberUtil::UNKNOWN_REGION);

        $phoneNumber = $this->type->convertToPHPValue($expectedPhoneNumber, $this->platform->reveal());

        $this->assertEquals($expectedPhoneNumber, $phoneNumber);
    }

    public function testConvertToPHPValueFailure(): void
    {
        $this->expectException(ConversionException::class);

        $this->type->convertToPHPValue('foo', $this->platform->reveal());
    }

    public function testRequiresSQLCommentHint(): void
    {
        $this->assertTrue($this->type->requiresSQLCommentHint($this->platform->reveal()));
    }
}

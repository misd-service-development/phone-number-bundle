<?php

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

/**
 * Phone number type test.
 */
class PhoneNumberTypeTest extends TestCase
{
    /**
     * @var AbstractPlatform
     */
    protected $platform;

    /**
     * @var PhoneNumberType
     */
    protected $type;

    /**
     * @var PhoneNumberUtil
     */
    protected $phoneNumberUtil;

    public static function setUpBeforeClass(): void
    {
        Type::addType('phone_number', 'Misd\PhoneNumberBundle\Doctrine\DBAL\Types\PhoneNumberType');
    }

    protected function setUp(): void
    {
        $this->platform = $this->prophesize(AbstractPlatform::class);
        $this->platform->getVarcharTypeDeclarationSQL()->willReturn('DUMMYVARCHAR()');

        $this->type = Type::getType('phone_number');
        $this->phoneNumberUtil = PhoneNumberUtil::getInstance();
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf('Doctrine\DBAL\Types\Type', $this->type);
    }

    public function testGetName()
    {
        $this->assertSame('phone_number', $this->type->getName());
    }

    public function testGetSQLDeclaration()
    {
        $this->platform->getVarcharTypeDeclarationSQL(['length' => 35])->willReturn('DUMMYVARCHAR()');
        $this->assertSame('DUMMYVARCHAR()', $this->type->getSQLDeclaration([], $this->platform->reveal()));
    }

    public function testConvertToDatabaseValueWithNull()
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform->reveal()));
    }

    public function testConvertToDatabaseValueWithPhoneNumber()
    {
        $phoneNumber = $this->phoneNumberUtil->parse('+441234567890', PhoneNumberUtil::UNKNOWN_REGION);

        $this->assertSame('+441234567890', $this->type->convertToDatabaseValue($phoneNumber, $this->platform->reveal()));
    }

    public function testConvertToDatabaseValueFailure()
    {
        $this->expectException(ConversionException::class);

        $this->type->convertToDatabaseValue('foo', $this->platform->reveal());
    }

    public function testConvertToPHPValueWithNull()
    {
        $this->assertNull($this->type->convertToPHPValue(null, $this->platform->reveal()));
    }

    public function testConvertToPHPValueWithPhoneNumber()
    {
        $phoneNumber = $this->type->convertToPHPValue('+441234567890', $this->platform->reveal());

        $this->assertInstanceOf('libphoneNumber\PhoneNumber', $phoneNumber);
        $this->assertSame('+441234567890', $this->phoneNumberUtil->format($phoneNumber, PhoneNumberFormat::E164));
    }

    public function testConvertToPHPValueWithAPhoneNumberInstance()
    {
        $expectedPhoneNumber = $this->phoneNumberUtil->parse('+441234567890', PhoneNumberUtil::UNKNOWN_REGION);

        $phoneNumber = $this->type->convertToPHPValue($expectedPhoneNumber, $this->platform->reveal());

        $this->assertEquals($expectedPhoneNumber, $phoneNumber);
    }

    public function testConvertToPHPValueFailure()
    {
        $this->expectException(ConversionException::class);

        $this->type->convertToPHPValue('foo', $this->platform->reveal());
    }

    public function testRequiresSQLCommentHint()
    {
        $this->assertTrue($this->type->requiresSQLCommentHint($this->platform->reveal()));
    }
}

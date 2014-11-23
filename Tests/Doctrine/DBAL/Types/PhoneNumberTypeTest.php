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
use Doctrine\DBAL\Types\Type;
use libphonenumber\PhoneNumberUtil;
use Misd\PhoneNumberBundle\Doctrine\DBAL\Types\PhoneNumberType;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Phone number type test.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
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

    public static function setUpBeforeClass()
    {
        Type::addType('phone_number', 'Misd\PhoneNumberBundle\Doctrine\DBAL\Types\PhoneNumberType');
    }

    protected function setUp()
    {
        $this->platform = $this->getMockBuilder('Doctrine\DBAL\Platforms\AbstractPlatform')
            ->setMethods(array('getVarcharTypeDeclarationSQL'))
            ->getMockForAbstractClass();

        $this->platform->expects($this->any())
            ->method('getVarcharTypeDeclarationSQL')
            ->will($this->returnValue('DUMMYVARCHAR()'));

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
        $this->assertSame('DUMMYVARCHAR()', $this->type->getSQLDeclaration(array(), $this->platform));
    }

    public function testConvertToDatabaseValueWithNull()
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    public function testConvertToDatabaseValueWithPhoneNumber()
    {
        $phoneNumber = $this->phoneNumberUtil->parse('+441234567890', PhoneNumberUtil::UNKNOWN_REGION);

        $this->assertSame('+441234567890', $this->type->convertToDatabaseValue($phoneNumber, $this->platform));
    }

    /**
     * @expectedException \Doctrine\DBAL\Types\ConversionException
     */
    public function testConvertToDatabaseValueFailure()
    {
        $this->type->convertToDatabaseValue('foo', $this->platform);
    }

    public function testConvertToPHPValueWithNull()
    {
        $this->assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    public function testConvertToPHPValueWithPhoneNumber()
    {
        $phoneNumber = $this->type->convertToPHPValue('+441234567890', $this->platform);

        $this->assertInstanceOf('libphoneNumber\PhoneNumber', $phoneNumber);
        $this->assertSame('+441234567890', (string) $phoneNumber);
    }

    /**
     * @expectedException \Doctrine\DBAL\Types\ConversionException
     */
    public function testConvertToPHPValueFailure()
    {
        $this->type->convertToPHPValue('foo', $this->platform);
    }

    public function testRequiresSQLCommentHint()
    {
        $this->assertTrue($this->type->requiresSQLCommentHint($this->platform));
    }
}

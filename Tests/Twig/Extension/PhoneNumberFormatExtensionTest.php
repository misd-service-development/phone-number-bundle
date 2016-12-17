<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Tests\Twig\Extension;

use Misd\PhoneNumberBundle\Twig\Extension\PhoneNumberFormatExtension;

/**
 * Phone number format Twig extension test.
 */
class PhoneNumberFormatExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberFormatHelper
     */
    private $helper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Misd\PhoneNumberBundle\Twig\Extension\PhoneNumberFormatExtension
     */
    private $extension;

    protected function setUp()
    {
        $this->helper = $this->getMockBuilder('Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberFormatHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->extension = new PhoneNumberFormatExtension($this->helper);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Twig_Extension', $this->extension);
    }

    public function testGetFunctions()
    {
        $functions = $this->extension->getFunctions();

        $this->assertCount(1, $functions);
        $this->assertInstanceOf('Twig_SimpleFunction', $functions[0]);
        $this->assertSame('phone_number_format', $functions[0]->getName());

        $callable = $functions[0]->getCallable();

        $this->assertSame($this->helper, $callable[0]);
        $this->assertSame('format', $callable[1]);
    }

    public function testGetFilters()
    {
        $filters = $this->extension->getFunctions();

        $this->assertCount(1, $filters);
        $this->assertInstanceOf('Twig_SimpleFunction', $filters[0]);
        $this->assertSame('phone_number_format', $filters[0]->getName());

        $callable = $filters[0]->getCallable();

        $this->assertSame($this->helper, $callable[0]);
        $this->assertSame('format', $callable[1]);
    }

    public function testGetName()
    {
        $this->assertTrue(is_string($this->extension->getName()));
    }
}

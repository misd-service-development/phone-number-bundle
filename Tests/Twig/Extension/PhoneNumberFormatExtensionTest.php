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
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Phone number format Twig extension test.
 */
class PhoneNumberFormatExtensionTest extends TestCase
{
    public function testConstructor()
    {
        $helper = $this->getMockBuilder('Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberFormatHelper')
            ->disableOriginalConstructor()->getMock();

        $extension = new PhoneNumberFormatExtension($helper);

        $this->assertInstanceOf('Twig_Extension', $extension);
    }

    public function testGetFunctions()
    {
        $helper = $this->getMockBuilder('Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberFormatHelper')
            ->disableOriginalConstructor()->getMock();

        $extension = new PhoneNumberFormatExtension($helper);

        $functions = $extension->getFunctions();

        $this->assertCount(1, $functions);
        $this->assertInstanceOf('Twig_SimpleFunction', $functions[0]);
        $this->assertSame('phone_number_format', $functions[0]->getName());

        $callable = $functions[0]->getCallable();

        $this->assertSame($helper, $callable[0]);
        $this->assertSame('format', $callable[1]);
    }

    public function testGetName()
    {
        $helper = $this->getMockBuilder('Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberFormatHelper')
            ->disableOriginalConstructor()->getMock();

        $extension = new PhoneNumberFormatExtension($helper);

        $this->assertTrue(is_string($extension->getName()));
    }
}

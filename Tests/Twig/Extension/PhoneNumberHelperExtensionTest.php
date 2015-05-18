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

use Misd\PhoneNumberBundle\Twig\Extension\PhoneNumberHelperExtension;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Phone number Twig extension test.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class PhoneNumberHelperExtensionTest extends TestCase
{
    public function testConstructor()
    {
        $helper = $this->getMockBuilder('Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberHelper')
            ->disableOriginalConstructor()->getMock();

        $extension = new PhoneNumberHelperExtension($helper);

        $this->assertInstanceOf('Twig_Extension', $extension);
    }

    public function testGetFunctions()
    {
        $helper = $this->getMockBuilder('Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberHelper')
            ->disableOriginalConstructor()->getMock();

        $extension = new PhoneNumberHelperExtension($helper);

        $functions = $extension->getFunctions();

        $this->assertCount(2, $functions);
        $this->assertInstanceOf('Twig_SimpleFunction', $functions[0]);
        $this->assertSame('phone_number_format', $functions[0]->getName());
        $this->assertSame('phone_number_is_mobile', $functions[1]->getName());

        $formatCallable = $functions[0]->getCallable();
        $isMobileCallable = $functions[1]->getCallable();

        $this->assertSame($helper, $formatCallable[0]);
        $this->assertSame('format', $formatCallable[1]);
        $this->assertSame($helper, $isMobileCallable[0]);
        $this->assertSame('isMobile', $isMobileCallable[1]);
    }

    public function testGetName()
    {
        $helper = $this->getMockBuilder('Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberHelper')
            ->disableOriginalConstructor()->getMock();

        $extension = new PhoneNumberHelperExtension($helper);

        $this->assertTrue(is_string($extension->getName()));
    }
}

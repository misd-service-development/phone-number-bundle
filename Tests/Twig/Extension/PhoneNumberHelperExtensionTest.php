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


/**
 * Phone number helper Twig extension test.
 */
class PhoneNumberHelperExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberHelper
     */
    private $helper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Misd\PhoneNumberBundle\Twig\Extension\PhoneNumberHelperExtension
     */
    private $extension;

    protected function setUp()
    {
        $this->helper = $this->getMockBuilder('Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->extension = new PhoneNumberHelperExtension($this->helper);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Twig_Extension', $this->extension);
    }

    public function testGetFunctions()
    {
        $functions = $this->extension->getFunctions();

        $this->assertCount(2, $functions);
        $this->assertInstanceOf('Twig_SimpleFunction', $functions[0]);
        $this->assertSame('phone_number_format', $functions[0]->getName());

        $callable = $functions[0]->getCallable();

        $this->assertSame($this->helper, $callable[0]);
        $this->assertSame('format', $callable[1]);

        $this->assertInstanceOf('Twig_SimpleFunction', $functions[1]);
        $this->assertSame('phone_number_is_type', $functions[1]->getName());

        $callable = $functions[1]->getCallable();

        $this->assertSame($this->helper, $callable[0]);
        $this->assertSame('isType', $callable[1]);
    }

    public function testGetFilters()
    {
        $filters = $this->extension->getFilters();

        $this->assertCount(1, $filters);
        $this->assertInstanceOf('Twig_SimpleFilter', $filters[0]);
        $this->assertSame('phone_number_format', $filters[0]->getName());

        $callable = $filters[0]->getCallable();

        $this->assertSame($this->helper, $callable[0]);
        $this->assertSame('format', $callable[1]);
    }

    public function testGetTests()
    {
        $tests = $this->extension->getTests();

        $this->assertCount(1, $tests);
        $this->assertInstanceOf('Twig_SimpleTest', $tests[0]);
        $this->assertSame('phone_number_of_type', $tests[0]->getName());

        $callable = $tests[0]->getCallable();

        $this->assertSame($this->helper, $callable[0]);
        $this->assertSame('isType', $callable[1]);
    }

    public function testGetName()
    {
        $this->assertTrue(is_string($this->extension->getName()));
    }
}

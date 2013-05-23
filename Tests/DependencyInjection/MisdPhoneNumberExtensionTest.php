<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Tests\DependencyInjection;

use Misd\PhoneNumberBundle\DependencyInjection\MisdPhoneNumberExtension;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Bundle extension test.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class MisdPhoneNumberExtensionTest extends TestCase
{
    public function testLoad()
    {
        $extension = new MisdPhoneNumberExtension();

        $containerBuilder = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $parameterBag = $this->getMock('Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface');
        $containerBuilder->expects($this->any())->method('getParameterBag')->will($this->returnValue($parameterBag));

        $extension->load(array(), $containerBuilder);
    }
}

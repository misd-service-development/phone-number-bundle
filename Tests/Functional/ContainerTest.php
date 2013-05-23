<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Container test.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class ContainerTest extends WebTestCase
{
    public function testContainer()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $this->assertTrue($container->has('libphonenumber.phone_number_util'));
        $this->assertInstanceOf('libphonenumber\PhoneNumberUtil', $container->get('libphonenumber.phone_number_util'));
    }
}

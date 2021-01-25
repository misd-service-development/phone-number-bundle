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

use libphonenumber\PhoneNumberFormat;
use Misd\PhoneNumberBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    /**
     * @dataProvider configurationDataProvider
     */
    public function testConfiguration(array $configs, array $expected)
    {
        $processor = new Processor();
        $result = $processor->processConfiguration(new Configuration(), $configs);

        $this->assertSame($expected, $result);
    }

    public function configurationDataProvider()
    {
        yield [[], [
            'twig' => [
                'enabled' => true,
            ],
            'form' => [
                'enabled' => true,
            ],
            'serializer' => [
                'enabled' => true,
                'default_region' => 'ZZ',
                'format' => PhoneNumberFormat::E164,
            ],
            'validator' => [
                'enabled' => true,
                'default_region' => 'ZZ',
                'format' => PhoneNumberFormat::INTERNATIONAL,
            ],
        ]];

        yield [[
            'misd_phone_number' => [
                'twig' => false,
                'form' => false,
                'serializer' => false,
                'validator' => false,
            ],
        ], [
            'twig' => [
                'enabled' => false,
            ],
            'form' => [
                'enabled' => false,
            ],
            'serializer' => [
                'enabled' => false,
                'default_region' => 'ZZ',
                'format' => PhoneNumberFormat::E164,
            ],
            'validator' => [
                'enabled' => false,
                'default_region' => 'ZZ',
                'format' => PhoneNumberFormat::INTERNATIONAL,
            ],
        ]];

        yield [[
            'misd_phone_number' => [
                'twig' => [
                    'enabled' => false,
                ],
                'form' => [
                    'enabled' => false,
                ],
                'serializer' => [
                    'enabled' => false,
                    'default_region' => 'GB',
                    'format' => PhoneNumberFormat::E164,
                ],
                'validator' => [
                    'enabled' => false,
                    'default_region' => 'GB',
                    'format' => PhoneNumberFormat::INTERNATIONAL,
                ],
            ],
        ], [
            'twig' => [
                'enabled' => false,
            ],
            'form' => [
                'enabled' => false,
            ],
            'serializer' => [
                'enabled' => false,
                'default_region' => 'GB',
                'format' => PhoneNumberFormat::E164,
            ],
            'validator' => [
                'enabled' => false,
                'default_region' => 'GB',
                'format' => PhoneNumberFormat::INTERNATIONAL,
            ],
        ]];
    }
}

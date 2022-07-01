<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Bundle extension.
 */
class MisdPhoneNumberExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        if ($config['twig']['enabled']) {
            $loader->load('twig.xml');
        }
        if ($config['form']['enabled']) {
            $loader->load('form.xml');
        }
        if ($config['serializer']['enabled']) {
            $loader->load('serializer.xml');

            $container->setParameter('misd_phone_number.serializer.default_region', $config['serializer']['default_region']);
            $container->setParameter('misd_phone_number.serializer.format', $config['serializer']['format']);
        }
        if ($config['validator']['enabled']) {
            $loader->load('validator.xml');

            $container->setParameter('misd_phone_number.validator.default_region', $config['validator']['default_region']);
            $container->setParameter('misd_phone_number.validator.format', $config['validator']['format']);
        }
    }
}

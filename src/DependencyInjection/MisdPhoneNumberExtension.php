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
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        if (class_exists('Symfony\Bundle\TwigBundle\TwigBundle')) {
            $loader->load('twig.xml');
        }
        if (interface_exists('Symfony\Component\Form\FormTypeInterface')) {
            $loader->load('form.xml');
        }
        if (interface_exists('Symfony\Component\Serializer\Normalizer\NormalizerInterface')) {
            $loader->load('serializer.xml');
        }
    }
}

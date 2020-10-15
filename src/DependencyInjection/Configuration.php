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

use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('misd_phone_number');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('misd_phone_number');
        }
        $rootNode
            ->children()
                ->scalarNode('twig')->defaultValue(class_exists(TwigBundle::class))->end()
                ->scalarNode('form')->defaultValue(interface_exists(FormTypeInterface::class))->end()
                ->scalarNode('serializer')->defaultValue(interface_exists(NormalizerInterface::class))->end()
                ->scalarNode('validator')->defaultValue(interface_exists(ValidatorInterface::class))->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

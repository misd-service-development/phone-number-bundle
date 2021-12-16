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

use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('misd_phone_number');
        $rootNode = $treeBuilder->getRootNode();

        $normalizer = function ($value) {
            if (\is_bool($value)) {
                return [
                    'enabled' => $value,
                ];
            }

            return $value;
        };

        $rootNode
            ->children()
                ->arrayNode('twig')
                    ->addDefaultsIfNotSet()
                    ->beforeNormalization()->always($normalizer)->end()
                    ->children()
                        ->scalarNode('enabled')
                            ->defaultValue(class_exists(TwigBundle::class))
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('form')
                    ->addDefaultsIfNotSet()
                    ->beforeNormalization()->always($normalizer)->end()
                    ->children()
                        ->scalarNode('enabled')
                            ->defaultValue(interface_exists(FormTypeInterface::class))
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('serializer')
                    ->addDefaultsIfNotSet()
                    ->beforeNormalization()->always($normalizer)->end()
                    ->children()
                        ->scalarNode('enabled')
                            ->defaultValue(interface_exists(NormalizerInterface::class))
                        ->end()
                        ->scalarNode('default_region')
                            ->defaultValue(PhoneNumberUtil::UNKNOWN_REGION)
                            ->beforeNormalization()->always(function ($value) {
                                return strtoupper($value);
                            })->end()
                        ->end()
                        ->scalarNode('format')
                            ->defaultValue(PhoneNumberFormat::E164)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('validator')
                    ->addDefaultsIfNotSet()
                    ->beforeNormalization()->always($normalizer)->end()
                    ->children()
                        ->scalarNode('enabled')->defaultValue(interface_exists(ValidatorInterface::class))->end()
                        ->scalarNode('default_region')
                            ->defaultValue(PhoneNumberUtil::UNKNOWN_REGION)
                            ->beforeNormalization()->always(function ($value) {
                                return strtoupper($value);
                            })->end()
                        ->end()
                        ->scalarNode('format')
                            // The difference between serializer and validator is historical, they are here to keep the BC
                            ->defaultValue(PhoneNumberFormat::INTERNATIONAL)
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

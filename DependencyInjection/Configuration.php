<?php
/**
 * Created by dTatham
 * Date: 02.12.17
 * Time: 21:50
 */

namespace Misd\PhoneNumberBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();

        $builder
            ->root('misd_phone_number')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('template')
            ->defaultValue('@MisdPhoneNumberBundle/Form/tel.html.twig')
            ->end()
            ->end();

        return $builder;
    }
}

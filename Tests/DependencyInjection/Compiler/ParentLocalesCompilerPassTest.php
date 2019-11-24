<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Tests\DependencyInjection\Compiler;

use Misd\PhoneNumberBundle\DependencyInjection\Compiler\ParentLocalesCompilerPass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Parent locales compiler pass test.
 */
class ParentLocalesCompilerPassTest extends TestCase
{
    public function testNoTranslator()
    {
        $container = new ContainerBuilder();
        (new ParentLocalesCompilerPass())->process($container);

        $this->expectException(ServiceNotFoundException::class);
        $container->get('translator');
    }

    public function testDifferentTranslator()
    {
        $compilerPass = new ParentLocalesCompilerPass();

        $container = new ContainerBuilder();

        $translatorDefinition = new Definition(
            'Symfony\Component\Translation\IdentityTranslator'
        );

        $container->setDefinition('translator', $translatorDefinition);

        $compilerPass->process($container);

        $this->assertEmpty($translatorDefinition->getMethodCalls());
    }

    public function testTranslator()
    {
        $compilerPass = new ParentLocalesCompilerPass();

        $container = new ContainerBuilder();

        $translatorDefinition = new Definition(
            'Symfony\Component\Translation\Translator'
        );

        $container->setDefinition('translator', $translatorDefinition);

        $compilerPass->process($container);

        $this->assertCount(
            count($this->getLocalParents($compilerPass)),
            $translatorDefinition->getMethodCalls()
        );
    }

    private function getLocalParents(ParentLocalesCompilerPass $compilerPass)
    {
        $reflectionClass = new ReflectionClass('Misd\PhoneNumberBundle\DependencyInjection\Compiler\ParentLocalesCompilerPass');
        $reflectionProperty = $reflectionClass->getProperty('localParents');
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($compilerPass);
    }
}

<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Form Twig template compiler pass.
 */
class FormTwigTemplateCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasParameter('twig.form.resources')) {
            return;
        }

        $parameter = $container->getParameter('twig.form.resources');
        $template = $container->getParameter('phone_number.template');

        if (in_array($template, $parameter)) {
            return;
        }


        // Insert right after base template if it exists.
        if (($key = array_search('bootstrap_3_horizontal_layout.html.twig', $parameter)) !== false) {
            array_splice($parameter, ++$key, 0, array($template));
        } elseif (($key = array_search('bootstrap_3_layout.html.twig', $parameter)) !== false) {
            array_splice($parameter, ++$key, 0, array($template));
        } elseif (($key = array_search('bootstrap_4_layout.html.twig', $parameter)) !== false) {
            array_splice($parameter, ++$key, 0, array($template));
        } elseif (($key = array_search('bootstrap_4_horizontal_layout.html.twig', $parameter)) !== false) {
            array_splice($parameter, ++$key, 0, array($template));
        } elseif (($key = array_search('form_div_layout.html.twig', $parameter)) !== false) {
            array_splice($parameter, ++$key, 0, array($template));
        } else {
            // Put it in first position.
            array_unshift($parameter, $template);
        }

        $container->setParameter('twig.form.resources', $parameter);
    }
}

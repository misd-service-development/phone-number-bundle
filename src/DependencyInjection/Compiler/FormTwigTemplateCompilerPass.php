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
    private $phoneNumberLayout = '@MisdPhoneNumber/Form/phone_number.html.twig';
    private $phoneNumberBootstrapLayout = '@MisdPhoneNumber/Form/phone_number_bootstrap.html.twig';
    private $phoneNumberBootstrap4Layout = '@MisdPhoneNumber/Form/phone_number_bootstrap_4.html.twig';
    private $phoneNumberBootstrap5Layout = '@MisdPhoneNumber/Form/phone_number_bootstrap_5.html.twig';

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasParameter('twig.form.resources')) {
            return;
        }

        $parameter = $container->getParameter('twig.form.resources');

        if (\in_array($this->phoneNumberLayout, $parameter)) {
            return;
        }

        // Insert right after base template if it exists.
        if (false !== ($key = array_search('bootstrap_5_horizontal_layout.html.twig', $parameter))) {
            array_splice($parameter, ++$key, 0, [$this->phoneNumberBootstrap5Layout]);
        } elseif (false !== ($key = array_search('bootstrap_5_layout.html.twig', $parameter))) {
            array_splice($parameter, ++$key, 0, [$this->phoneNumberBootstrap5Layout]);
        } elseif (false !== ($key = array_search('bootstrap_4_horizontal_layout.html.twig', $parameter))) {
            array_splice($parameter, ++$key, 0, [$this->phoneNumberBootstrap4Layout]);
        } elseif (false !== ($key = array_search('bootstrap_4_layout.html.twig', $parameter))) {
            array_splice($parameter, ++$key, 0, [$this->phoneNumberBootstrap4Layout]);
        } elseif (false !== ($key = array_search('bootstrap_3_horizontal_layout.html.twig', $parameter))) {
            array_splice($parameter, ++$key, 0, [$this->phoneNumberBootstrapLayout]);
        } elseif (false !== ($key = array_search('bootstrap_3_layout.html.twig', $parameter))) {
            array_splice($parameter, ++$key, 0, [$this->phoneNumberBootstrapLayout]);
        } elseif (false !== ($key = array_search('form_div_layout.html.twig', $parameter))) {
            array_splice($parameter, ++$key, 0, [$this->phoneNumberLayout]);
        } else {
            // Put it in first position.
            array_unshift($parameter, [$this->phoneNumberLayout]);
        }

        $container->setParameter('twig.form.resources', $parameter);
    }
}

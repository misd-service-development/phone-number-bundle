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
 * Form PHP template compiler pass.
 */
class FormPhpTemplateCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasParameter('templating.helper.form.resources')) {
            return;
        }

        $parameter = $container->getParameter('templating.helper.form.resources');

        if (in_array('MisdPhoneNumberBundle:Form', $parameter)) {
            return;
        }

        // Insert right after FrameworkBundle:Form if exists.
        if (($key = array_search('FrameworkBundle:Form', $parameter)) !== false) {
            array_splice($parameter, ++$key, 0, array('MisdPhoneNumberBundle:Form'));
        } else {
            // Put it in first position.
            array_unshift($resources, array('MisdPhoneNumberBundle:Form'));
        }

        $container->setParameter('templating.helper.form.resources', $parameter);
    }
}

<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle;

use Misd\PhoneNumberBundle\DependencyInjection\Compiler\FormPhpTemplateCompilerPass;
use Misd\PhoneNumberBundle\DependencyInjection\Compiler\FormTwigTemplateCompilerPass;
use Misd\PhoneNumberBundle\DependencyInjection\Compiler\ParentLocalesCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Phone number bundle.
 */
class MisdPhoneNumberBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(
          new ParentLocalesCompilerPass(),
          PassConfig::TYPE_BEFORE_REMOVING
        );
        $container->addCompilerPass(new FormPhpTemplateCompilerPass());
        $container->addCompilerPass(new FormTwigTemplateCompilerPass());
    }
}

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

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Parent locales compiler pass.
 *
 * Symfony isn't aware of the ICU locale inheritance rules, so to save having
 * to have lots of duplicated translations we add them in here.
 *
 * See https://github.com/symfony/symfony/issues/12319 for further details.
 */
class ParentLocalesCompilerPass implements CompilerPassInterface
{
    /**
     * Locale => parent locale map (as defined in ICU).
     *
     * @var array
     */
    private $localParents = array(
      'es_AR' => 'es_419',
      'es_BO' => 'es_419',
      'es_CL' => 'es_419',
      'es_CO' => 'es_419',
      'es_CR' => 'es_419',
      'es_CU' => 'es_419',
      'es_DO' => 'es_419',
      'es_EC' => 'es_419',
      'es_GT' => 'es_419',
      'es_HN' => 'es_419',
      'es_MX' => 'es_419',
      'es_NI' => 'es_419',
      'es_PA' => 'es_419',
      'es_PE' => 'es_419',
      'es_PR' => 'es_419',
      'es_PY' => 'es_419',
      'es_SV' => 'es_419',
      'es_US' => 'es_419',
      'es_UY' => 'es_419',
      'es_VE' => 'es_419',
    );

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        try {
            $translator = $container->findDefinition('translator');
        } catch (InvalidArgumentException $e) {
            return;
        }

        if (
          'Symfony\Component\Translation\Translator' !== $translator->getClass()
          &&
          false === is_subclass_of($translator->getClass(), 'Symfony\Component\Translation\Translator')
        ) {
            return;
        }

        $methodCalls = array_values($translator->getMethodCalls());

        foreach ($this->localParents as $locale => $parent) {
            $path = realpath(
              sprintf(
                '%s/../../Resources/translations/validators.%s.xlf',
                __DIR__,
                $parent
              )
            );

            if (false === $path) {
                continue;
            }

            $parentKey = null;

            // Find the position of the parent locale addResource() call so that
            // we can insert the child locale's call directly after rather than
            // just appending it. This means that the user can then still
            // override the translation.
            foreach ($methodCalls as $i => $methodCall) {
                if (
                  'addResource' === $methodCall[0]
                  &&
                  $path === realpath($methodCall[1][1])
                ) {
                    $parentKey = $i;
                    break;
                }
            }

            $extraMethodCall = array(
              'addResource',
              array(
                'xlf',
                $path,
                $locale,
                'validators',
              ),
            );

            if (null === $parentKey) {
                $methodCalls[] = $extraMethodCall;
            } else {
                array_splice(
                  $methodCalls,
                  $parentKey + 1,
                  0,
                  array($extraMethodCall)
                );
            }
        }

        $translator->setMethodCalls($methodCalls);
    }
}

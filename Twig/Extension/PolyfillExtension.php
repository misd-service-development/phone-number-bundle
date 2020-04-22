<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Twig\Extension;

use Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

if (\class_exists(AbstractExtension::class)) {
    // Twig 2.0 and upper
    abstract class PolyfillExtension extends AbstractExtension
    {
        /**
         * {@inheritdoc}
         */
        public function getFunctions()
        {
            return [
                new TwigFunction('phone_number_format', [$this->getHelper(), 'format'], ['deprecated' => '1.2']),
                new TwigFunction('phone_number_is_type', [$this->getHelper(), 'isType'], ['deprecated' => '1.2']),
            ];
        }

        /**
         * {@inheritdoc}
         */
        public function getFilters()
        {
            return [
                new TwigFilter('phone_number_format', [$this->getHelper(), 'format']),
            ];
        }

        /**
         * {@inheritdoc}
         */
        public function getTests()
        {
            return [
                new TwigTest('phone_number_of_type', [$this->getHelper(), 'isType']),
            ];
        }

        /**
         * @return PhoneNumberHelper
         */
        abstract protected function getHelper();
    }
} else {
    // Twig 1.0
    abstract class PolyfillExtension extends \Twig_Extension
    {
        /**
         * {@inheritdoc}
         */
        public function getFunctions()
        {
            return [
                new \Twig_SimpleFunction('phone_number_format', [$this->getHelper(), 'format'], ['deprecated' => '1.2']),
                new \Twig_SimpleFunction('phone_number_is_type', [$this->getHelper(), 'isType'], ['deprecated' => '1.2']),
            ];
        }

        /**
         * {@inheritdoc}
         */
        public function getFilters()
        {
            return [
                new \Twig_SimpleFilter('phone_number_format', [$this->getHelper(), 'format']),
            ];
        }

        /**
         * {@inheritdoc}
         */
        public function getTests()
        {
            return [
                new \Twig_SimpleTest('phone_number_of_type', [$this->getHelper(), 'isType']),
            ];
        }

        /**
         * @return PhoneNumberHelper
         */
        abstract protected function getHelper();
    }
}

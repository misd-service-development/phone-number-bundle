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

use Misd\PhoneNumberBundle\Formatter\PhoneNumberFormatter;
use Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberHelper;

/**
 * Phone number helper Twig extension.
 */
class PhoneNumberHelperExtension extends \Twig_Extension
{
    /**
     * Phone number formatter.
     *
     * @var PhoneNumberFormatter
     */
    protected $formatter;

    /**
     * Constructor.
     *
     * @param PhoneNumberFormatter $formatter Phone number formatter.
     */
    public function __construct($formatter)
    {
        if ($formatter instanceof PhoneNumberHelper) {
            // throw deprecation message
        }
        $this->formatter = $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('phone_number_format', array($this->formatter, 'format'), array('deprecated' => '1.2')),
            new \Twig_SimpleFunction('phone_number_is_type', array($this->formatter, 'isType'), array('deprecated' => '1.2')),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('phone_number_format', array($this->formatter, 'format')),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return array(
            new \Twig_SimpleTest('phone_number_of_type', array($this->formatter, 'isType')),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'phone_number_helper';
    }
}

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

/**
 * Phone number helper Twig extension.
 */
class PhoneNumberHelperExtension extends AbstractExtension
{
    /**
     * Phone number helper.
     *
     * @var PhoneNumberHelper
     */
    protected $helper;

    /**
     * Constructor.
     *
     * @param PhoneNumberHelper $helper Phone number helper.
     */
    public function __construct(PhoneNumberHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('phone_number_format', array($this->helper, 'format'), array('deprecated' => '1.2')),
            new TwigFunction('phone_number_is_type', array($this->helper, 'isType'), array('deprecated' => '1.2')),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('phone_number_format', array($this->helper, 'format')),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return array(
            new TwigTest('phone_number_of_type', array($this->helper, 'isType')),
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

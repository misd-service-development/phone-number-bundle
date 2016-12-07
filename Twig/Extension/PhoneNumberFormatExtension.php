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

use Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberFormatHelper;

/**
 * Phone number format Twig extension.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
class PhoneNumberFormatExtension extends \Twig_Extension
{
    /**
     * Phone number format helper.
     *
     * @var PhoneNumberFormatHelper
     */
    protected $helper;

    /**
     * Constructor.
     *
     * @param PhoneNumberFormatHelper $helper Phone number format helper.
     */
    public function __construct(PhoneNumberFormatHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('phone_number_format', array($this->helper, 'format'), array('deprecated' => '1.2'))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('phone_number_format', array($this->helper, 'format')),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'phone_number_format';
    }
}

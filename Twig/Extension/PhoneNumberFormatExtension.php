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
use Twig_Extension as Extension;
use Twig_SimpleFunction as SimpleFunction;

/**
 * Phone number format Twig extension.
 */
class PhoneNumberFormatExtension extends Extension
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
            new SimpleFunction('phone_number_format', array($this->helper, 'format'))
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

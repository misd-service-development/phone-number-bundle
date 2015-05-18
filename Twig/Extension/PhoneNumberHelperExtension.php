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
use Twig_Extension as Extension;
use Twig_SimpleFunction as SimpleFunction;

/**
 * Phone number helper Twig extension.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class PhoneNumberHelperExtension extends Extension
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
            new SimpleFunction('phone_number_format', array($this->helper, 'format')),
            new SimpleFunction('phone_number_is_mobile', array($this->helper, 'isMobile')),
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

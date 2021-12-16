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
     * @param PhoneNumberHelper $helper phone number helper
     */
    public function __construct(PhoneNumberHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            // Deprecated in favor of the phone_number_format filter
            new TwigFunction('phone_number_format', [$this->helper, 'format'], ['deprecated' => '1.2']),
            new TwigFunction('phone_number_is_type', [$this->helper, 'isType'], ['deprecated' => '1.2']),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            new TwigFilter('phone_number_format', [$this->helper, 'format']),
            new TwigFilter('phone_number_format_out_of_country_calling_number', [$this->helper, 'formatOutOfCountryCallingNumber']),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getTests()
    {
        return [
            new TwigTest('phone_number_of_type', [$this->helper, 'isType']),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'phone_number_helper';
    }
}

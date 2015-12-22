<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Form\Type;

use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Misd\PhoneNumberBundle\Form\DataTransformer\PhoneNumberToArrayTransformer;
use Misd\PhoneNumberBundle\Form\DataTransformer\PhoneNumberToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Phone number form type.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class PhoneNumberType extends AbstractType
{
    const WIDGET_SINGLE_TEXT = 'single_text';
    const WIDGET_COUNTRY_CHOICE = 'country_choice';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (self::WIDGET_COUNTRY_CHOICE === $options['widget']) {
            $util = PhoneNumberUtil::getInstance();

            $countries = array();

            if (is_array($options['country_choices'])) {
                foreach ($options['country_choices'] as $country) {
                    $code = $util->getCountryCodeForRegion($country);

                    if ($code) {
                        $countries[$country] = $code;
                    }
                }
            }

            if (empty($countries)) {
                foreach ($util->getSupportedRegions() as $country) {
                    $countries[$country] = $util->getCountryCodeForRegion($country);
                }
            }

            $countryChoices = array();

            foreach (Intl::getRegionBundle()->getCountryNames() as $region => $name) {
                if (false === isset($countries[$region])) {
                    continue;
                }

                $countryChoices[$region] = sprintf('%s (+%s)', $name, $countries[$region]);
            }

            $countryOptions = $numberOptions = array(
                'error_bubbling' => true,
                'required' => $options['required'],
                'disabled' => $options['disabled'],
                'translation_domain' => $options['translation_domain'],
            );

            $countryOptions['required'] = true;
            $countryOptions['choices'] = $countryChoices;
            $countryOptions['preferred_choices'] = $options['preferred_country_choices'];
            $countryOptions['choice_translation_domain'] = false;

            $builder
                ->add('country', 'choice', $countryOptions)
                ->add('number', 'text', $numberOptions)
                ->addViewTransformer(new PhoneNumberToArrayTransformer(array_keys($countryChoices)));
        } else {
            $builder->addViewTransformer(
                new PhoneNumberToStringTransformer($options['default_region'], $options['format'])
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['type'] = 'tel';
        $view->vars['widget'] = $options['widget'];
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated To be removed when the Symfony Form component compatibility
     *             is bumped to at least 2.7.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'widget' => self::WIDGET_SINGLE_TEXT,
                'compound' => function (Options $options) {
                    return PhoneNumberType::WIDGET_SINGLE_TEXT !== $options['widget'];
                },
                'default_region' => PhoneNumberUtil::UNKNOWN_REGION,
                'format' => PhoneNumberFormat::INTERNATIONAL,
                'invalid_message' => 'This value is not a valid phone number.',
                'by_reference' => false,
                'error_bubbling' => false,
                'country_choices' => array(),
                'preferred_country_choices' => array(),
            )
        );

        if (method_exists($resolver, 'setDefault')) {
            $resolver->setAllowedValues(
                'widget',
                array(
                    self::WIDGET_SINGLE_TEXT,
                    self::WIDGET_COUNTRY_CHOICE,
                )
            );
        } else {
            // To be removed when dependency on Symfony OptionsResolver is bumped to 2.6.
            $resolver->setAllowedValues(
                array(
                    'widget' => array(
                        self::WIDGET_SINGLE_TEXT,
                        self::WIDGET_COUNTRY_CHOICE,
                    ),
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tel';
    }
}

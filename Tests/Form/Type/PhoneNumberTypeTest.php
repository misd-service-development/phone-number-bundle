<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Tests\Form\Type;

use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Locale;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Intl\Util\IntlTestHelper;

/**
 * Phone number form type test.
 */
class PhoneNumberTypeTest extends TypeTestCase
{
    protected function setUp()
    {
        Locale::setDefault('en');

        parent::setUp();
    }

    /**
     * @dataProvider singleFieldProvider
     */
    public function testSingleField($input, $options, $output)
    {
        if (method_exists('Symfony\\Component\\Form\\FormTypeInterface', 'getName')) {
            $type = new PhoneNumberType();
        } else {
            $type = 'Misd\\PhoneNumberBundle\\Form\\Type\\PhoneNumberType';
        }
        
        $form = $this->factory->create($type, null, $options);

        $form->submit($input);

        if(method_exists($form, 'getTransformationFailure') && $failure = $form->getTransformationFailure()) {
            throw $failure;
        } else {
            $this->assertTrue($form->isSynchronized());
        }

        $view = $form->createView();

        $this->assertSame('tel', $view->vars['type']);
        $this->assertSame($output, $view->vars['value']);
    }

    /**
     * 0 => Input
     * 1 => Options
     * 2 => Output
     */
    public function singleFieldProvider()
    {
        return array(
            array('+441234567890', array(), '+44 1234 567890'),
            array('+44 1234 567890', array('format' => PhoneNumberFormat::NATIONAL), '+44 1234 567890'),
            array('+44 1234 567890', array('default_region' => 'GB', 'format' => PhoneNumberFormat::NATIONAL), '01234 567890'),
            array('+1 650-253-0000', array('default_region' => 'GB', 'format' => PhoneNumberFormat::NATIONAL), '00 1 650-253-0000'),
            array('01234 567890', array('default_region' => 'GB'), '+44 1234 567890'),
            array('', array(), ''),
        );
    }

    /**
     * @dataProvider countryChoiceValuesProvider
     */
    public function testCountryChoiceValues($input, $options, $output)
    {
        $options['widget'] = PhoneNumberType::WIDGET_COUNTRY_CHOICE;
        if (method_exists('Symfony\\Component\\Form\\FormTypeInterface', 'getName')) {
            $type = new PhoneNumberType();
        } else {
            $type = 'Misd\\PhoneNumberBundle\\Form\\Type\\PhoneNumberType';
        }
        $form = $this->factory->create($type, null, $options);

        $form->submit($input);

        if(method_exists($form, 'getTransformationFailure') && $failure = $form->getTransformationFailure()) {
            throw $failure;
        } else {
            $this->assertTrue($form->isSynchronized());
        }

        $view = $form->createView();

        $this->assertSame('tel', $view->vars['type']);
        $this->assertSame($output, $view->vars['value']);
    }

    /**
     * 0 => Input
     * 1 => Options
     * 2 => Output
     */
    public function countryChoiceValuesProvider()
    {
        return array(
            array(array('country' => 'GB', 'number' => '01234 567890'), array(), array('country' => 'GB', 'number' => '01234 567890')),
            array(array('country' => 'GB', 'number' => '+44 1234 567890'), array(), array('country' => 'GB', 'number' => '01234 567890')),
            array(array('country' => 'GB', 'number' => '1234 567890'), array(), array('country' => 'GB', 'number' => '01234 567890')),
            array(array('country' => 'GB', 'number' => '+1 650-253-0000'), array(), array('country' => 'US', 'number' => '(650) 253-0000')),
            array(array('country' => '', 'number' => ''), array(), array('country' => '', 'number' => '')),
        );
    }

    /**
     * @dataProvider countryChoiceChoicesProvider
     */
    public function testCountryChoiceChoices(array $choices, $expectedChoicesCount, array $expectedChoices)
    {
        IntlTestHelper::requireIntl($this);

        if (method_exists('Symfony\\Component\\Form\\FormTypeInterface', 'getName')) {
            $type = new PhoneNumberType();
        } else {
            $type = 'Misd\\PhoneNumberBundle\\Form\\Type\\PhoneNumberType';
        }
        $form = $this->factory->create($type, null, array('widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE, 'country_choices' => $choices));

        $view = $form->createView();
        $choices = $view['country']->vars['choices'];

        $this->assertCount($expectedChoicesCount, $choices);
        foreach ($expectedChoices as $expectedChoice) {
            $this->assertContains($expectedChoice, $choices, '', false, false);
        }
    }

    /**
     * 0 => Choices
     * 1 => Expected choices count
     * 2 => Expected choices
     */
    public function countryChoiceChoicesProvider()
    {
        return array(
            array(
                array(),
                count(PhoneNumberUtil::getInstance()->getSupportedRegions()),
                array(
                    $this->createChoiceView('United Kingdom (+44)', 'GB'),
                ),
            ),
            array(
                array('GB', 'US'),
                2,
                array(
                    $this->createChoiceView('United Kingdom (+44)', 'GB'),
                    $this->createChoiceView('United States (+1)', 'US'),
                ),
            ),
            array(
                array('GB', 'US', PhoneNumberUtil::UNKNOWN_REGION),
                2,
                array(
                    $this->createChoiceView('United Kingdom (+44)', 'GB'),
                    $this->createChoiceView('United States (+1)', 'US'),
                ),
            ),
        );
    }

    public function testCountryChoiceTranslations()
    {
        IntlTestHelper::requireFullIntl($this);
        Locale::setDefault('fr');

        $form = $this->factory->create(new PhoneNumberType(), null, array('widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE));

        $view = $form->createView();
        $choices = $view['country']->vars['choices'];

        $this->assertContains($this->createChoiceView('Royaume-Uni (+44)', 'GB'), $choices, '', false, false);
        $this->assertFalse($view['country']->vars['choice_translation_domain']);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testInvalidWidget()
    {
        if (method_exists('Symfony\\Component\\Form\\FormTypeInterface', 'getName')) {
            $type = new PhoneNumberType();
        } else {
            $type = 'Misd\\PhoneNumberBundle\\Form\\Type\\PhoneNumberType';
        }
        $this->factory->create($type, null, array('widget' => 'foo'));
    }

    public function testGetNameAndBlockPrefixAreTel()
    {
        $type = new PhoneNumberType();

        $this->assertSame('tel', $type->getBlockPrefix());
        $this->assertSame($type->getBlockPrefix(), $type->getName());
    }

    private function createChoiceView($label, $code)
    {
        if (class_exists('Symfony\Component\Form\ChoiceList\View\ChoiceView')) {
            $class = 'Symfony\Component\Form\ChoiceList\View\ChoiceView';
        } else {
            $class = 'Symfony\Component\Form\Extension\Core\View\ChoiceView';
        }

        return new $class($code, $code, $label);
    }
}

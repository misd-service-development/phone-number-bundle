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
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

/**
 * Phone number form type test.
 */
class PhoneNumberTypeTest extends TypeTestCase
{
    /**
     * @dataProvider singleFieldProvider
     */
    public function testSingleField($input, $options, $output)
    {
        Locale::setDefault('en');
        if (method_exists('Symfony\\Component\\Form\\FormTypeInterface', 'getName')) {
            $type = new PhoneNumberType();
        } else {
            $type = 'Misd\\PhoneNumberBundle\\Form\\Type\\PhoneNumberType';
        }

        $form = $this->factory->create($type, null, $options);

        $form->submit($input);

        if (method_exists($form, 'getTransformationFailure') && $failure = $form->getTransformationFailure()) {
            throw $failure;
        }
        $this->assertTrue($form->isSynchronized());

        $view = $form->createView();

        $this->assertSame('tel', $view->vars['type']);
        $this->assertSame($output, $view->vars['value']);
    }

    /**
     * 0 => Input
     * 1 => Options
     * 2 => Output.
     */
    public function singleFieldProvider()
    {
        return [
            ['+441234567890', [], '+44 1234 567890'],
            ['+44 1234 567890', ['format' => PhoneNumberFormat::NATIONAL], '+44 1234 567890'],
            ['+44 1234 567890', ['default_region' => 'GB', 'format' => PhoneNumberFormat::NATIONAL], '01234 567890'],
            ['+1 650-253-0000', ['default_region' => 'GB', 'format' => PhoneNumberFormat::NATIONAL], '00 1 650-253-0000'],
            ['01234 567890', ['default_region' => 'GB'], '+44 1234 567890'],
            ['', [], ''],
        ];
    }

    /**
     * @dataProvider countryChoiceValuesProvider
     */
    public function testCountryChoiceValues($input, $options, $output)
    {
        Locale::setDefault('en');
        $options['widget'] = PhoneNumberType::WIDGET_COUNTRY_CHOICE;
        if (method_exists('Symfony\\Component\\Form\\FormTypeInterface', 'getName')) {
            $type = new PhoneNumberType();
        } else {
            $type = 'Misd\\PhoneNumberBundle\\Form\\Type\\PhoneNumberType';
        }
        $form = $this->factory->create($type, null, $options);

        $form->submit($input);

        if (method_exists($form, 'getTransformationFailure') && $failure = $form->getTransformationFailure()) {
            throw $failure;
        }
        $this->assertTrue($form->isSynchronized());

        $view = $form->createView();

        $this->assertSame('tel', $view->vars['type']);
        $this->assertSame($output, $view->vars['value']);
    }

    /**
     * 0 => Input
     * 1 => Options
     * 2 => Output.
     */
    public function countryChoiceValuesProvider()
    {
        return [
            [['country' => 'GB', 'number' => '01234 567890'], [], ['country' => 'GB', 'number' => '01234 567890']],
            [['country' => 'GB', 'number' => '+44 1234 567890'], [], ['country' => 'GB', 'number' => '01234 567890']],
            [['country' => 'GB', 'number' => '1234 567890'], [], ['country' => 'GB', 'number' => '01234 567890']],
            [['country' => 'GB', 'number' => '+1 650-253-0000'], [], ['country' => 'US', 'number' => '(650) 253-0000']],
            [['country' => '', 'number' => ''], [], ['country' => '', 'number' => '']],
        ];
    }

    /**
     * @dataProvider countryChoiceChoicesProvider
     */
    public function testCountryChoiceChoices(array $choices, $expectedChoicesCount, array $expectedChoices)
    {
        Locale::setDefault('en');
        IntlTestHelper::requireIntl($this);

        if (method_exists('Symfony\\Component\\Form\\FormTypeInterface', 'getName')) {
            $type = new PhoneNumberType();
        } else {
            $type = 'Misd\\PhoneNumberBundle\\Form\\Type\\PhoneNumberType';
        }
        $form = $this->factory->create($type, null, ['widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE, 'country_choices' => $choices]);

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
     * 2 => Expected choices.
     */
    public function countryChoiceChoicesProvider()
    {
        return [
            [
                [],
                count(PhoneNumberUtil::getInstance()->getSupportedRegions()),
                [
                    $this->createChoiceView('United Kingdom (+44)', 'GB'),
                ],
            ],
            [
                ['GB', 'US'],
                2,
                [
                    $this->createChoiceView('United Kingdom (+44)', 'GB'),
                    $this->createChoiceView('United States (+1)', 'US'),
                ],
            ],
            [
                ['GB', 'US', PhoneNumberUtil::UNKNOWN_REGION],
                2,
                [
                    $this->createChoiceView('United Kingdom (+44)', 'GB'),
                    $this->createChoiceView('United States (+1)', 'US'),
                ],
            ],
        ];
    }

    /**
     * @dataProvider countryChoicePlaceholderProvider
     *
     * @param $placeholder
     * @param $expectedPlaceholder
     */
    public function testCountryChoicePlaceholder($placeholder, $expectedPlaceholder)
    {
        IntlTestHelper::requireIntl($this);
        Locale::setDefault('en');
        if (method_exists('Symfony\\Component\\Form\\FormTypeInterface', 'getName')) {
            $type = new PhoneNumberType();
        } else {
            $type = 'Misd\\PhoneNumberBundle\\Form\\Type\\PhoneNumberType';
        }
        $form = $this->factory->create($type, null, ['widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE, 'country_placeholder' => $placeholder]);

        $view = $form->createView();
        $renderedPlaceholder = $view['country']->vars['placeholder'];
        $this->assertEquals($expectedPlaceholder, $renderedPlaceholder);
    }

    /**
     * 0 => Filled
     * 1 => not filled
     * 2 => empty.
     */
    public function countryChoicePlaceholderProvider()
    {
        return [
            [
                'Choose a country',
                'Choose a country',
            ],
            [
                null,
                null,
            ],
            [
                '',
                '',
            ],
        ];
    }

    public function testCountryChoiceTranslations()
    {
        IntlTestHelper::requireFullIntl($this);
        Locale::setDefault('fr');

        if (method_exists('Symfony\\Component\\Form\\FormTypeInterface', 'getName')) {
            $type = new PhoneNumberType();
        } else {
            $type = 'Misd\\PhoneNumberBundle\\Form\\Type\\PhoneNumberType';
        }

        $form = $this->factory->create($type, null, ['widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE]);

        $view = $form->createView();
        $choices = $view['country']->vars['choices'];

        $this->assertContains($this->createChoiceView('Royaume-Uni (+44)', 'GB'), $choices, '', false, false);
        $this->assertFalse($view['country']->vars['choice_translation_domain']);
    }

    public function testInvalidWidget()
    {
        Locale::setDefault('en');
        $this->expectException(InvalidOptionsException::class);
        if (method_exists('Symfony\\Component\\Form\\FormTypeInterface', 'getName')) {
            $type = new PhoneNumberType();
        } else {
            $type = 'Misd\\PhoneNumberBundle\\Form\\Type\\PhoneNumberType';
        }
        $this->factory->create($type, null, ['widget' => 'foo']);
    }

    public function testGetNameAndBlockPrefixAreTel()
    {
        Locale::setDefault('en');
        $type = new PhoneNumberType();

        $this->assertSame('phone_number', $type->getBlockPrefix());
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

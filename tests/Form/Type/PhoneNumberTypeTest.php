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
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Intl\Util\IntlTestHelper;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

/**
 * Phone number form type test.
 */
class PhoneNumberTypeTest extends TestCase
{
    /**
     * @var FormFactoryInterface
     */
    protected $factory;

    protected function setUp(): void
    {
        Locale::setDefault('en');

        $this->factory = Forms::createFormFactoryBuilder()->getFormFactory();
    }

    /**
     * @dataProvider singleFieldProvider
     */
    public function testSingleField($input, $options, $output)
    {
        $form = $this->factory->create(PhoneNumberType::class, null, $options);

        $form->submit($input);

        if (method_exists($form, 'getTransformationFailure') && $failure = $form->getTransformationFailure()) {
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
        $options['widget'] = PhoneNumberType::WIDGET_COUNTRY_CHOICE;
        $form = $this->factory->create(PhoneNumberType::class, null, $options);

        $form->submit($input);

        if (method_exists($form, 'getTransformationFailure') && $failure = $form->getTransformationFailure()) {
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
        IntlTestHelper::requireIntl($this);

        $form = $this->factory->create(PhoneNumberType::class, null, ['widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE, 'country_choices' => $choices]);

        $view = $form->createView();
        $choices = $view['country']->vars['choices'];

        $this->assertCount($expectedChoicesCount, $choices);
        foreach ($expectedChoices as $expectedChoice) {
            $this->assertContainsEquals($expectedChoice, $choices);
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
                \count(PhoneNumberUtil::getInstance()->getSupportedRegions()),
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
        $form = $this->factory->create(PhoneNumberType::class, null, ['widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE, 'country_placeholder' => $placeholder]);

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

        $form = $this->factory->create(PhoneNumberType::class, null, ['widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE]);

        $view = $form->createView();
        $choices = $view['country']->vars['choices'];

        $this->assertContainsEquals($this->createChoiceView('Royaume-Uni (+44)', 'GB'), $choices);
        $this->assertFalse($view['country']->vars['choice_translation_domain']);
    }

    public function testInvalidWidget()
    {
        $this->expectException(InvalidOptionsException::class);

        $this->factory->create(PhoneNumberType::class, null, ['widget' => 'foo']);
    }

    public function testGetNameAndBlockPrefixAreTel()
    {
        $type = new PhoneNumberType();

        $this->assertSame('phone_number', $type->getBlockPrefix());
        $this->assertSame($type->getBlockPrefix(), $type->getName());
    }

    public function testCountryChoiceCountryOptions()
    {
        $form = $this->factory->create(PhoneNumberType::class, null, [
            'widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE,
            'country_options' => [
                'attr' => [
                    'class' => 'custom-select-class',
                ],
            ],
        ]);
        $view = $form->createView();

        $this->assertEquals(['class' => 'custom-select-class'], $view['country']->vars['attr']);
    }

    public function testCountryChoiceNumberOptions()
    {
        $form = $this->factory->create(PhoneNumberType::class, null, [
            'widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE,
            'number_options' => [
                'attr' => [
                    'placeholder' => '000 000',
                ],
            ],
        ]);
        $view = $form->createView();

        $this->assertEquals(['placeholder' => '000 000'], $view['number']->vars['attr']);
    }

    private function createChoiceView($label, $code)
    {
        return new ChoiceView($code, $code, $label);
    }
}

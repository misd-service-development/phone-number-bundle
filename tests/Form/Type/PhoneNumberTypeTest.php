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
    protected FormFactoryInterface $factory;

    protected function setUp(): void
    {
        \Locale::setDefault('en');

        $this->factory = Forms::createFormFactoryBuilder()->getFormFactory();
    }

    /**
     * @dataProvider singleFieldProvider
     *
     * @param array<string, mixed> $options
     */
    public function testSingleField(string $input, array $options, string $output): void
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
     *
     * @return iterable<array{string, array<string, mixed>, string}>
     */
    public function singleFieldProvider(): iterable
    {
        yield ['+441234567890', [], '+44 1234 567890'];
        yield ['+44 1234 567890', ['format' => PhoneNumberFormat::NATIONAL], '+44 1234 567890'];
        yield ['+44 1234 567890', ['default_region' => 'GB', 'format' => PhoneNumberFormat::NATIONAL], '01234 567890'];
        yield ['+1 650-253-0000', ['default_region' => 'GB', 'format' => PhoneNumberFormat::NATIONAL], '00 1 650-253-0000'];
        yield ['01234 567890', ['default_region' => 'GB'], '+44 1234 567890'];
        yield ['', [], ''];
    }

    /**
     * @dataProvider countryChoiceValuesProvider
     *
     * @param array<string, string> $input
     * @param array<string, string> $output
     */
    public function testCountryChoiceValues(array $input, array $output): void
    {
        $options = ['widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE];
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
     *
     * @return iterable<array{array<string, string>, array<string, string>}>
     */
    public function countryChoiceValuesProvider(): iterable
    {
        yield [['country' => 'GB', 'number' => '01234 567890'], ['country' => 'GB', 'number' => '01234 567890']];
        yield [['country' => 'GB', 'number' => '+44 1234 567890'], ['country' => 'GB', 'number' => '01234 567890']];
        yield [['country' => 'GB', 'number' => '1234 567890'], ['country' => 'GB', 'number' => '01234 567890']];
        yield [['country' => 'GB', 'number' => '+1 650-253-0000'], ['country' => 'US', 'number' => '(650) 253-0000']];
        yield [['country' => '', 'number' => ''], ['country' => '', 'number' => '']];
    }

    /**
     * @dataProvider countryChoiceChoicesProvider
     *
     * @param string[]     $choices
     * @param ChoiceView[] $expectedChoices
     */
    public function testCountryChoiceChoices(array $choices, int $expectedChoicesCount, array $expectedChoices): void
    {
        IntlTestHelper::requireIntl($this);

        $form = $this->factory->create(
            PhoneNumberType::class,
            null,
            ['widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE, 'country_choices' => $choices]
        );

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
     *
     * @return iterable<array{string[], int, ChoiceView[]}>
     */
    public function countryChoiceChoicesProvider(): iterable
    {
        yield [
            [],
            // 3 regions have an already used label "TA", "AC" and XK
            // @see https://fr.wikipedia.org/wiki/ISO_3166-2#cite_note-UPU-1
            242,
            [
                $this->createChoiceView('United Kingdom (+44)', 'GB'),
            ],
        ];
        yield [
            ['GB', 'US'],
            2,
            [
                $this->createChoiceView('United Kingdom (+44)', 'GB'),
                $this->createChoiceView('United States (+1)', 'US'),
            ],
        ];
        yield [
            ['GB', 'US', PhoneNumberUtil::UNKNOWN_REGION],
            2,
            [
                $this->createChoiceView('United Kingdom (+44)', 'GB'),
                $this->createChoiceView('United States (+1)', 'US'),
            ],
        ];
    }

    /**
     * @dataProvider countryChoiceFormatProvider
     *
     * @param ChoiceView[] $expectedChoices
     */
    public function testCountryChoiceFormat(string $displayType, bool $displayEmojiFlag, array $expectedChoices): void
    {
        $options['widget'] = PhoneNumberType::WIDGET_COUNTRY_CHOICE;
        $options['country_display_type'] = $displayType;
        $options['country_display_emoji_flag'] = $displayEmojiFlag;
        $form = $this->factory->create(PhoneNumberType::class, null, $options);

        $view = $form->createView();
        $choices = $view['country']->vars['choices'];

        foreach ($expectedChoices as $expectedChoice) {
            $this->assertContainsEquals($expectedChoice, $choices);
        }
    }

    /**
     * 0 => Display type
     * 1 => Display emoji flag
     * 2 => Expected choices.
     *
     * @return iterable<array{string, bool, ChoiceView[]}>
     */
    public function countryChoiceFormatProvider(): iterable
    {
        yield [
            PhoneNumberType::DISPLAY_COUNTRY_FULL,
            false,
            [
                $this->createChoiceView('United Kingdom (+44)', 'GB'),
            ],
        ];
        yield [
            PhoneNumberType::DISPLAY_COUNTRY_SHORT,
            false,
            [
                $this->createChoiceView('GB +44', 'GB'),
            ],
        ];
        yield [
            PhoneNumberType::DISPLAY_COUNTRY_FULL,
            true,
            [
                $this->createChoiceView('🇬🇧 United Kingdom (+44)', 'GB'),
            ],
        ];
        yield [
            PhoneNumberType::DISPLAY_COUNTRY_SHORT,
            true,
            [
                $this->createChoiceView('🇬🇧 GB +44', 'GB'),
            ],
        ];
    }

    /**
     * @dataProvider countryChoicePlaceholderProvider
     */
    public function testCountryChoicePlaceholder(?string $placeholder, ?string $expectedPlaceholder): void
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
     *
     * @return iterable<array{?string, ?string}>
     */
    public function countryChoicePlaceholderProvider(): iterable
    {
        yield ['Choose a country', 'Choose a country'];
        yield [null, null];
        yield ['', ''];
    }

    public function testCountryChoiceTranslations(): void
    {
        IntlTestHelper::requireFullIntl($this);
        \Locale::setDefault('fr');

        $form = $this->factory->create(PhoneNumberType::class, null, ['widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE]);

        $view = $form->createView();
        $choices = $view['country']->vars['choices'];

        $this->assertContainsEquals($this->createChoiceView('Royaume-Uni (+44)', 'GB'), $choices);
        $this->assertFalse($view['country']->vars['choice_translation_domain']);
    }

    public function testInvalidWidget(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->factory->create(PhoneNumberType::class, null, ['widget' => 'foo']);
    }

    public function testGetNameAndBlockPrefixAreTel(): void
    {
        $type = new PhoneNumberType();

        $this->assertSame('phone_number', $type->getBlockPrefix());
        $this->assertSame($type->getBlockPrefix(), $type->getName());
    }

    public function testCountryChoiceCountryOptions(): void
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

    public function testCountryChoiceNumberOptions(): void
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

    private function createChoiceView(string $label, string $code): ChoiceView
    {
        return new ChoiceView($code, $code, $label);
    }
}

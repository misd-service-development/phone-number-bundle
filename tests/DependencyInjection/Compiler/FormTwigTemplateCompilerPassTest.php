<?php

namespace Misd\PhoneNumberBundle\Tests\DependencyInjection\Compiler;

use Misd\PhoneNumberBundle\DependencyInjection\Compiler\FormTwigTemplateCompilerPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class FormTwigTemplateCompilerPassTest extends TestCase
{
    public function testItDoesNothingWhenWithIsNotEnabled(): void
    {
        $subject = new FormTwigTemplateCompilerPass();
        $container = new ContainerBuilder();

        $subject->process($container);

        $this->assertFalse($container->hasParameter('twig.form.resources'));
    }

    public function testItDoesNothingIfThePhoneNumberTwigIsAlreadyConfigured(): void
    {
        $subject = new FormTwigTemplateCompilerPass();
        $container = new ContainerBuilder();
        $inputParameter = ['@MisdPhoneNumber/Form/phone_number.html.twig'];
        $container->setParameter('twig.form.resources', $inputParameter);

        $subject->process($container);

        $this->assertSame(
            $inputParameter,
            $container->getParameter('twig.form.resources')
        );
    }

    /**
     * @dataProvider themesProvider
     */
    public function testItAddsPhoneTemplatesAccordingToSymfonyTemplates(string $sfTemplate, string $phoneTemplate): void
    {
        $subject = new FormTwigTemplateCompilerPass();
        $container = new ContainerBuilder();
        $inputParameter = [$sfTemplate];
        $container->setParameter('twig.form.resources', $inputParameter);

        $subject->process($container);

        $resources = $container->getParameter('twig.form.resources');

        $this->assertTrue(\is_array($resources));
        $this->assertTrue(\in_array($phoneTemplate, $resources, true));
    }

    /**
     * @return iterable<array{string, string}>
     */
    public function themesProvider(): iterable
    {
        yield 'Bootstrap 5 horizontal' => [
            'bootstrap_5_horizontal_layout.html.twig',
            '@MisdPhoneNumber/Form/phone_number_bootstrap_5.html.twig',
        ];
        yield 'Bootstrap 5 vertical' => [
            'bootstrap_5_layout.html.twig',
            '@MisdPhoneNumber/Form/phone_number_bootstrap_5.html.twig',
        ];
        yield 'Bootstrap 4 horizontal' => [
            'bootstrap_4_horizontal_layout.html.twig',
            '@MisdPhoneNumber/Form/phone_number_bootstrap_4.html.twig',
        ];
        yield 'Bootstrap 4 vertical' => [
            'bootstrap_4_layout.html.twig',
            '@MisdPhoneNumber/Form/phone_number_bootstrap_4.html.twig',
        ];
        yield 'Bootstrap 3 horizontal' => [
            'bootstrap_3_horizontal_layout.html.twig',
            '@MisdPhoneNumber/Form/phone_number_bootstrap.html.twig',
        ];
        yield 'Bootstrap 3 vertical' => [
            'bootstrap_3_layout.html.twig',
            '@MisdPhoneNumber/Form/phone_number_bootstrap.html.twig',
        ];
        yield 'It adds phone number when using standard symfony form layout' => [
            'form_div_layout.html.twig',
            '@MisdPhoneNumber/Form/phone_number.html.twig',
        ];
        yield 'Test it enables phone number template anyway' => [
            'i_have_yolo_layout.html.twig',
            '@MisdPhoneNumber/Form/phone_number.html.twig',
        ];
    }
}

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
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Phone number form type test.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class PhoneNumberTypeTest extends TypeTestCase
{
    /**
     * @dataProvider defaultFormattingProvider
     */
    public function testDefaultFormatting($input, $options, $output)
    {
        $type = new PhoneNumberType();
        $form = $this->factory->create($type, null, $options);

        $form->submit($input);

        $this->assertTrue($form->isSynchronized());

        $view = $form->createView();

        $this->assertSame('tel', $view->vars['type']);
        $this->assertSame($output, $view->vars['value']);
    }

    public function defaultFormattingProvider()
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
}

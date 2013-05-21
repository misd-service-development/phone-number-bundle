<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Phone number constraint.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 *
 * @Annotation
 */
class PhoneNumber extends Constraint
{
    public $message = 'This value is not a valid {{ type }} number.';
    public $type = 'phone';
    public $defaultRegion = 'ZZ';
}

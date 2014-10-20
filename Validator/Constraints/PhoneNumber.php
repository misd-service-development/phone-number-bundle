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

use libphonenumber\PhoneNumberUtil;
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
    private $defaultMessage = 'This value is not a valid phone number.';
    private $typedMessage = 'This value is not a valid {{ type }} number.';

    public $message = 'This value is not a valid phone number.';
    public $type = 'phone';
    public $defaultRegion = PhoneNumberUtil::UNKNOWN_REGION;

    public function getMessage()
    {
        if ('phone' !== $this->type && $this->message === $this->defaultMessage) {
            return strtr($this->typedMessage, array('{{ type }}' => $this->type));
        }

        return $this->message;
    }
}

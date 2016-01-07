<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Exception;

use InvalidArgumentException as BaseInvalidArgumentException;

/**
 * Exception thrown if an argument does not match with the expected value.
 */
class InvalidArgumentException extends BaseInvalidArgumentException
{
}

<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

if (class_exists(ArrayDenormalizer::class) && !method_exists(ArrayDenormalizer::class, 'setSerializer')) {
    // Symfony >= 6.0
    /**
     * Phone number serialization for Symfony serializer.
     */
    class PhoneNumberNormalizer implements NormalizerInterface, DenormalizerInterface
    {
        use PhoneNumberNormalizerTrait;
    }
} else {
    // Symfony < 6.0
    /**
     * Phone number serialization for Symfony serializer.
     */
    class PhoneNumberNormalizer implements NormalizerInterface, DenormalizerInterface
    {
        use LegacyPhoneNumberNormalizerTrait;
    }
}

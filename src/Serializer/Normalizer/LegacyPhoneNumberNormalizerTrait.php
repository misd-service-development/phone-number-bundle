<?php

namespace Misd\PhoneNumberBundle\Serializer\Normalizer;

use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * @internal
 *
 * Do not use directly. Just used for achieving compatibility with Symfony < 6 and >= 6.
 */
trait LegacyPhoneNumberNormalizerTrait
{
    use CommonPhoneNumberNormalizerTrait;

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return $this->doNormalize($object, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $this->doSupportsNormalization($data, $format);
    }

    /**
     * {@inheritdoc}
     *
     * @throws UnexpectedValueException
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return $this->doDenormalize($data, $class, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $this->doSupportsDenormalization($data, $type, $format);
    }
}

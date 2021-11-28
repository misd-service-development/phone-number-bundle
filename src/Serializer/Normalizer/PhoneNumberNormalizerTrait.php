<?php

namespace Misd\PhoneNumberBundle\Serializer\Normalizer;

use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * @internal
 *
 * Do not use directly. Just used for achieving compatibility with Symfony < 6 and >= 6.
 */
trait PhoneNumberNormalizerTrait
{
    use CommonPhoneNumberNormalizerTrait;

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        return $this->doNormalize($object, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $this->doSupportsNormalization($data, $format);
    }

    /**
     * {@inheritdoc}
     *
     * @throws UnexpectedValueException
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
    {
        return $this->doDenormalize($data, $type, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        return $this->doSupportsDenormalization($data, $type, $format);
    }
}

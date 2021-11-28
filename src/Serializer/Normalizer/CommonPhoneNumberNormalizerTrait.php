<?php

namespace Misd\PhoneNumberBundle\Serializer\Normalizer;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * @internal
 *
 * Do not use directly. Just used for achieving compatibility with Symfony < 6 and >= 6.
 */
trait CommonPhoneNumberNormalizerTrait
{
    /**
     * Region code.
     *
     * @var string
     */
    private $region;

    /**
     * Display format.
     *
     * @var int
     */
    private $format;

    /**
     * Display format.
     *
     * @var PhoneNumberUtil
     */
    private $phoneNumberUtil;

    /**
     * Constructor.
     *
     * @param PhoneNumberUtil $phoneNumberUtil phone number utility
     * @param string          $region          region code
     * @param int             $format          display format
     */
    public function __construct(PhoneNumberUtil $phoneNumberUtil, $region = PhoneNumberUtil::UNKNOWN_REGION, $format = PhoneNumberFormat::E164)
    {
        $this->phoneNumberUtil = $phoneNumberUtil;
        $this->region = $region;
        $this->format = $format;
    }

    private function doNormalize($object, $format = null, array $context = [])
    {
        return $this->phoneNumberUtil->format($object, $this->format);
    }

    private function doSupportsNormalization($data, $format = null)
    {
        return $data instanceof PhoneNumber;
    }

    private function doDenormalize($data, $class, $format = null, array $context = [])
    {
        if (null === $data) {
            return null;
        }

        try {
            return $this->phoneNumberUtil->parse($data, $this->region);
        } catch (NumberParseException $e) {
            throw new UnexpectedValueException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function doSupportsDenormalization($data, $type, $format = null)
    {
        return 'libphonenumber\PhoneNumber' === $type && \is_string($data);
    }
}

<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Serializer\Handler;

use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\VisitorInterface;
use JMS\Serializer\XmlDeserializationVisitor;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use SimpleXMLElement;

/**
 * Phone number serialization handler.
 */
class PhoneNumberHandler
{
    /**
     * Phone number utility.
     *
     * @var PhoneNumberUtil
     */
    private $phoneNumberUtil;

    /**
     * Constructor.
     *
     * @param PhoneNumberUtil $phoneNumberUtil phone number utility
     */
    public function __construct(PhoneNumberUtil $phoneNumberUtil)
    {
        $this->phoneNumberUtil = $phoneNumberUtil;
    }

    /**
     * Serialize a phone number.
     *
     * @param VisitorInterface $visitor     serialization visitor
     * @param PhoneNumber      $phoneNumber phone number
     * @param array            $type        type
     * @param mixed            $context     context
     *
     * @return mixed serialized phone number
     */
    public function serializePhoneNumber(VisitorInterface $visitor, PhoneNumber $phoneNumber, array $type, $context)
    {
        $formatted = $this->phoneNumberUtil->format($phoneNumber, PhoneNumberFormat::E164);

        return $visitor->visitString($formatted, $type, $context);
    }

    /**
     * Deserialize a phone number from JSON.
     *
     * @param JsonDeserializationVisitor $visitor deserialization visitor
     * @param string|null                $data    data
     * @param array                      $type    type
     *
     * @return PhoneNumber|null phone number
     */
    public function deserializePhoneNumberFromJson(JsonDeserializationVisitor $visitor, $data, array $type)
    {
        if (null === $data) {
            return null;
        }

        return $this->phoneNumberUtil->parse($data, PhoneNumberUtil::UNKNOWN_REGION);
    }

    /**
     * Deserialize a phone number from XML.
     *
     * @param XmlDeserializationVisitor $visitor deserialization visitor
     * @param SimpleXMLElement          $data    data
     * @param array                     $type    type
     *
     * @return PhoneNumber|null phone number
     */
    public function deserializePhoneNumberFromXml(XmlDeserializationVisitor $visitor, $data, array $type)
    {
        $attributes = $data->attributes();
        if (
            (isset($attributes['nil'][0]) && 'true' === (string) $attributes['nil'][0]) ||
            (isset($attributes['xsi:nil'][0]) && 'true' === (string) $attributes['xsi:nil'][0])
        ) {
            return null;
        }

        return $this->phoneNumberUtil->parse($data, PhoneNumberUtil::UNKNOWN_REGION);
    }
}

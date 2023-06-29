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

use Misd\PhoneNumberBundle\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

/**
 * Phone number constraint.
 *
 * @Annotation
 *
 * @NamedArgumentConstructor
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class PhoneNumber extends Constraint
{
    public const ANY = 'any';
    public const FIXED_LINE = 'fixed_line';
    public const MOBILE = 'mobile';
    public const PAGER = 'pager';
    public const PERSONAL_NUMBER = 'personal_number';
    public const PREMIUM_RATE = 'premium_rate';
    public const SHARED_COST = 'shared_cost';
    public const TOLL_FREE = 'toll_free';
    public const UAN = 'uan';
    public const VOIP = 'voip';
    public const VOICEMAIL = 'voicemail';

    public const INVALID_PHONE_NUMBER_ERROR = 'ca23f4ca-38f4-4325-9bcc-eb570a4abe7f';

    protected const ERROR_NAMES = [
        self::INVALID_PHONE_NUMBER_ERROR => 'INVALID_PHONE_NUMBER_ERROR',
    ];

    /**
     * @deprecated since PhoneNumberBundle 3.6, use const ERROR_NAMES instead
     */
    protected static $errorNames = self::ERROR_NAMES;

    public $message;
    public $type = self::ANY;
    public $defaultRegion;
    public $regionPath;
    public $format;

    /**
     * @param int|array|null    $format Specify the format (\libphonenumber\PhoneNumberFormat::*)
     *                                  or options (an associative array)
     * @param string|array|null $type
     */
    #[HasNamedArguments]
    public function __construct($format = null, $type = null, string $defaultRegion = null, string $regionPath = null, string $message = null, array $groups = null, $payload = null, array $options = [])
    {
        if (\is_array($format)) {
            @trigger_error('Usage of the argument $format to specify options is deprecated and will be removed in 4.0. Use "$option" argument instead.', \E_USER_DEPRECATED);
            $options = array_merge($format, $options);
        } else {
            $phoneFormat = $format;
        }

        parent::__construct($options, $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->format = $phoneFormat ?? $this->format;
        $this->type = $type ?? $this->type;
        $this->defaultRegion = $defaultRegion ?? $this->defaultRegion;
        $this->regionPath = $regionPath ?? $this->regionPath;
    }

    public function getType(): ?string
    {
        @trigger_error(__METHOD__.' is deprecated and will be removed in 4.0. Use "getTypes" instead.', \E_USER_DEPRECATED);

        $types = $this->getTypes();
        if (0 === \count($types)) {
            return null;
        }

        return reset($types);
    }

    public function getTypes(): array
    {
        if (\is_array($this->type)) {
            return $this->type;
        }

        return [$this->type];
    }

    public function getMessage(): string
    {
        if (null !== $this->message) {
            return $this->message;
        }

        $types = $this->getTypes();
        if (1 === \count($types)) {
            $typeName = $this->getTypeName($types[0]);

            return "This value is not a valid $typeName.";
        }

        return 'This value is not a valid phone number.';
    }

    public function getTypeNames(): array
    {
        $types = \is_array($this->type) ? $this->type : [$this->type];

        $typeNames = [];
        foreach ($types as $type) {
            $typeNames[] = $this->getTypeName($type);
        }

        return $typeNames;
    }

    private function getTypeName(string $type): string
    {
        switch ($type) {
            case self::FIXED_LINE:
                return 'fixed-line number';
            case self::MOBILE:
                return 'mobile number';
            case self::PAGER:
                return 'pager number';
            case self::PERSONAL_NUMBER:
                return 'personal number';
            case self::PREMIUM_RATE:
                return 'premium-rate number';
            case self::SHARED_COST:
                return 'shared-cost number';
            case self::TOLL_FREE:
                return 'toll-free number';
            case self::UAN:
                return 'UAN';
            case self::VOIP:
                return 'VoIP number';
            case self::VOICEMAIL:
                return 'voicemail access number';
            case self::ANY:
                return 'phone number';
        }

        throw new InvalidArgumentException("Unknown phone number type \"$type\".");
    }
}

<?php

declare(strict_types=1);

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
 * @NamedArgumentConstructor
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
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

    public ?string $message = null;
    /**
     * @var string|string[]
     */
    public string|array $type = self::ANY;
    public ?string $defaultRegion = null;
    public ?string $regionPath = null;
    public ?int $format = null;

    /**
     * @param int|null             $format  Specify the format (\libphonenumber\PhoneNumberFormat::*)
     * @param string|string[]|null $type
     * @param array<mixed>         $options
     */
    #[HasNamedArguments]
    public function __construct(int $format = null, string|array $type = null, string $defaultRegion = null, string $regionPath = null, string $message = null, array $groups = null, $payload = null, array $options = [])
    {
        parent::__construct($options, $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->format = $format ?? $this->format;
        $this->type = $type ?? $this->type;
        $this->defaultRegion = $defaultRegion ?? $this->defaultRegion;
        $this->regionPath = $regionPath ?? $this->regionPath;
    }

    /**
     * @return string[]
     */
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

    /**
     * @return string[]
     */
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

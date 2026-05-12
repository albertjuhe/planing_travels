<?php

namespace App\Domain\Travel\ValueObject;

class TimeZone
{
    private string $value;

    public function __construct(string $ianaTimezone)
    {
        if (!in_array($ianaTimezone, \DateTimeZone::listIdentifiers(), true)) {
            throw new \InvalidArgumentException("Invalid IANA timezone: {$ianaTimezone}");
        }
        $this->value = $ianaTimezone;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function toDateTimeZone(): \DateTimeZone
    {
        return new \DateTimeZone($this->value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromString(string $tz): self
    {
        return new self($tz);
    }

    public static function tryFromString(string $tz): ?self
    {
        try {
            return new self($tz);
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }
}

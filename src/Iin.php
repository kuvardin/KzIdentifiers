<?php

declare(strict_types=1);

namespace Kuvardin\KzIdentifiers;

use DateTime;
use RuntimeException;

/**
 * Индивидуальный идентификационный номер
 */
class Iin extends KzIdentifier
{
    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function tryFrom(string $value): ?self
    {
        return self::checkValidity($value) ? new self($value) : null;
    }

    public static function require(string $value): self
    {
        $result = self::tryFrom($value);
        if ($result === null) {
            throw new RuntimeException("Incorrect IIN: $value");
        }

        return $result;
    }

    public static function checkValidity(string $value): bool
    {
        if (!preg_match('/^\d{12}$/', $value)) {
            return false;
        }

        $month = (int)substr($value, 2, 2);
        if ($month < 1 || $month > 12) {
            return false;
        }

        $day = (int)substr($value, 4, 2);
        if ($day < 1 || $day > 31) {
            return false;
        }

        if ($value[6] > 6) {
            return false;
        }

        return Biin::checkControl($value);
    }

    public function isMale(): bool
    {
        return (bool)($this->value[6] & 1);
    }

    public function getBirthDate(): ?DateTime
    {
        $birth_year = $this->getBirthYear();
        if ($birth_year === null) {
            return null;
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        return new DateTime(
            $this->getBirthYear() . '-' . $this->getBirthMonth() . '-' . $this->getBirthDay()
        );
    }

    public function getBirthYear(): ?int
    {
        $century = $this->getCentury();
        if ($century === null) {
            return null;
        }

        return (int)(($century - 1) . substr($this->value, 0, 2));
    }

    public function getCentury(): ?int
    {
        if ($this->value[6] === '0') {
            return null;
        }

        return (int)ceil($this->value[6] / 2) + 18;
    }

    public function getBirthMonth(): int
    {
        return (int)substr($this->value, 2, 2);
    }

    public function getBirthDay(): int
    {
        return (int)substr($this->value, 4, 2);
    }

    public function getAge(DateTime $current_date = null): ?int
    {
        return $this->getBirthDate()?->diff($current_date ?? new DateTime('now'))->y;
    }
}
<?php

declare(strict_types=1);

namespace Kuvardin\KzIdentifiers;

use DateTime;
use RuntimeException;

/**
 * Индивидуальный идентификационный номер
 */
class Iin
{
    readonly public string $value;

    public function __construct(string $value)
    {
        if (!self::checkValidity($value)) {
            throw new RuntimeException("Incorrect IIN: $value");
        }

        $this->value = $value;
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

        return self::checkControl($value);
    }

    public static function checkControl(string $value): bool
    {
        $control = self::getControl($value, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]);
        if ($control === 10) {
            $control = self::getControl($value, [3, 4, 5, 6, 7, 8, 9, 10, 11, 1, 2]);
        }

        return $control === (int)$value[11];
    }

    private static function getControl(string $iin, array $weights): int
    {
        $result = 0;
        for ($i = 0; $i < 11; $i++) {
            $result += (int)$iin[$i] * $weights[$i];
        }
        return $result % 11;
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
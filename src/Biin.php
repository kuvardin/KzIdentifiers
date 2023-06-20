<?php

declare(strict_types=1);

namespace Kuvardin\KzIdentifiers;

use RuntimeException;

class Biin
{
    protected ?Bin $bin = null;

    protected ?Iin $iin = null;

    public function __construct(string $value)
    {
        if (Bin::checkValidity($value)) {
            $this->bin = new Bin($value);
        } elseif (Iin::checkValidity($value)) {
            $this->iin = new Iin($value);
        } else {
            throw new RuntimeException("Incorrect identifier: $value");
        }
    }

    public static function checkValidity(string $value): bool
    {
        return Bin::checkValidity($value) || Iin::checkValidity($value);
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

    public function getValue(): string
    {
        return $this->iin?->value ?? $this->bin?->value;
    }

    public function isIin(): bool
    {
        return $this->iin !== null;
    }

    public function isBin(): bool
    {
        return $this->bin !== null;
    }

    public function getBin(): ?Bin
    {
        return $this->bin;
    }

    public function getIin(): ?Iin
    {
        return $this->iin;
    }
}
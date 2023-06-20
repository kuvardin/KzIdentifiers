<?php

declare(strict_types=1);

namespace Kuvardin\KzIdentifiers;

use RuntimeException;

class Biin
{
    private function __construct(
        readonly public ?Bin $bin,
        readonly public ?Iin $iin,
    )
    {
    }

    public static function tryFrom(string $value): ?self
    {
        $bin = Bin::tryFrom($value);
        if ($bin !== null) {
            return new self($bin, null);
        }

        $iin = Iin::tryFrom($value);
        if ($iin !== null) {
            return new self(null, $iin);
        }

        return null;
    }

    public function require(string $value): self
    {
        $result = self::tryFrom($value);
        if ($result === null) {
            throw new RuntimeException("Incorrect BIIN: $value");
        }

        return $result;
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

    public function getIdentifier(): KzIdentifier
    {
        return $this->iin ?? $this->bin;
    }

    public function getValue(): string
    {
        return $this->iin?->getValue() ?? $this->bin?->getValue();
    }
}
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
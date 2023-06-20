<?php

declare(strict_types=1);

namespace Kuvardin\KzIdentifiers;

abstract class KzIdentifier
{
    protected string $value;

    abstract public static function tryFrom(string $value): ?self;
    abstract public static function require(string $value): self;
    abstract public static function checkValidity(string $value): bool;

    public function getValue(): string
    {
        return $this->value;
    }
}
<?php

declare(strict_types=1);

namespace Kuvardin\KzIdentifiers;

abstract class KzIdentifier
{
    abstract public static function tryFrom(string $value): ?self;
    abstract public static function require(string $value): self;
    abstract public static function checkValidity(string $value): bool;
}
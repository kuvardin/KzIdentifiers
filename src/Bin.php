<?php

declare(strict_types=1);

namespace Kuvardin\KzIdentifiers;

use Kuvardin\KzIdentifiers\Enums\BinSign;
use Kuvardin\KzIdentifiers\Enums\BinType;
use RuntimeException;

/**
 * Бизнес-идентификационный номер
 */
class Bin extends KzIdentifier
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
        if (!preg_match('/(\d{12})/', $value)) {
            return false;
        }

        $month = (int)substr($value, 2, 2);
        if ($month < 1 || $month > 12) {
            return false;
        }

        $type = BinType::tryFrom((int)$value[4]);
        if ($type === null) {
            return false;
        }

        $sign = BinSign::tryFrom((int)$value[5]);
        if ($sign === null) {
            return false;
        }

        return Biin::checkControl($value);
    }

    public function getType(): BinType
    {
        return BinType::from((int)$this->value[4]);
    }

    public function getRegistrationMonth(): int
    {
        return (int)substr($this->value, 2, 2);
    }

    public function getSign(): BinSign
    {
        return BinSign::from((int)$this->value[5]);
    }
}
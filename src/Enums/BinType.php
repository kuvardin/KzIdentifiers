<?php

declare(strict_types=1);

namespace Kuvardin\KzIdentifiers\Enums;

/**
 * Тип юридического лица
 */
enum BinType: int
{
    /**
     * Резидент
     */
    case Resident = 4;

    /**
     * Нерезидент
     */
    case NonResident = 5;

    /**
     * ИП(С)
     */
    case Individual = 6;
}
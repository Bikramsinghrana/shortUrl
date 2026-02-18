<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SUPER_ADMIN = 'SuperAdmin';
    case ADMIN = 'Admin';
    case MEMBER = 'Member';
    case MANAGER = 'Manager';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $role) => $role->value, self::cases());
    }
}

<?php

namespace App\Enums;

enum NotificationSendTypeEnum: int
{
    case ADMIN = 1;

    private static function getNames(): array
    {
        return [
            self::ADMIN->value => 'Admin',
        ];
    }

    public static function getNameByValue(int $value): string
    {
        $names = self::getNames();
        return $names[$value] ?? '';
    }

    public static function getData(): array
    {
        return [
            [
                'id' => self::ADMIN->value,
                'name' => self::getNameByValue(self::ADMIN->value),
            ],
        ];
    }
}

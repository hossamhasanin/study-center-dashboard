<?php

namespace App\Filament\Resources;

use App\Models\User;

class ResourcesHelpers
{
    public static function deleteUser(int $userId)
    {
        User::query()->find($userId)->delete();
    }
}

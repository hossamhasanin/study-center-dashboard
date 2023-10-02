<?php

namespace App\Filament\Resources\TeacherResource;

use App\Models\Teacher;
use App\Models\User;

class TeacherResourceHelpers
{
    public static function prepareTeacherDataToView(array $data): array
    {
        $userData = User::query()->find($data["user_id"]);
        /** @var Teacher $teacher */
        $teacher = Teacher::query()->find($data["id"]);
        $academicYears = $teacher
            ->academicYears()
            ->get(["name"])
            ->map(fn ($model) => $model->name)
            ->all();
        return collect($data)->merge([
            "name" => $userData->name,
            "email" => $userData->email,
            "academic_years" => $academicYears
        ])->all();
    }
}

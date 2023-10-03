<?php

namespace App\Filament\Resources\StudentResource;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;

class StudentResourceHelper
{
    public static function prepareStudentDataToView(array $data): array
    {
        $userData = User::query()->find($data["user_id"]);
        /** @var Student $student */
        $student = Student::query()->find($data["id"]);
        $teachers = $student
            ->teachers()
            ->get(["subject_teacher_name"])
            ->map(fn ($model) => $model->subject_teacher_name)
            ->all();
        return collect($data)->merge([
            "name" => $userData->name,
            "email" => $userData->email,
            "teachers" => $teachers
        ])->all();
    }
}

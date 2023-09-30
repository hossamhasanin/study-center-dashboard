<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Teacher extends Model
{
    use HasFactory;

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subjects::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            "students_teachers",
            "teacher_id",
            "student_id"
        );
    }

    public function academicYears(): BelongsToMany
    {
        return $this->belongsToMany(
            AcademicYear::class,
            "teachers_academic_years",
            "teacher_id",
            "academic_year_id"
        );
    }
}

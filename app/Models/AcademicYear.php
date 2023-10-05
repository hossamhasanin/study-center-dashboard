<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        "name"
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(
            Teacher::class,
            "teachers_academic_years",
            "academic_year_id",
            "teacher_id"
        );
    }
}

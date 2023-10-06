<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        "teacher_id",
        "academic_year_id",
        "name",
        "description",
        "start_date",
        "end_date",
        "degree"
    ];

    public function teacher() : BelongsTo {
        return $this->belongsTo(Teacher::class);
    }

    public function academicYear() : BelongsTo {
        return $this->belongsTo(AcademicYear::class);
    }
}

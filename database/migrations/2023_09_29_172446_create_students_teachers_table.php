<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students_teachers', function (Blueprint $table) {
            $table->foreignId("student_id")->index();
            $table->foreign("student_id")->on("students")->references("id")->cascadeOnDelete();
            $table->foreignId("teacher_id")->index();
            $table->foreign("teacher_id")->on("teachers")->references("id")->cascadeOnDelete();
            $table->primary(["teacher_id", "student_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students_teachers');
    }
};

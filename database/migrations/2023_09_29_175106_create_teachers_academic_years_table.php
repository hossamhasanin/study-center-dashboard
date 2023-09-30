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
        Schema::create('teachers_academic_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId("teacher_id")->index();
            $table->foreign("teacher_id")->on("teachers")->references("id")->cascadeOnDelete();
            $table->foreignId("academic_year_id")->index();
            $table->foreign("academic_year_id")->on("academic_years")->references("id")->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers_academic_years');
    }
};

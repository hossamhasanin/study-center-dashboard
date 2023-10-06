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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId("teacher_id");
            $table->foreign("teacher_id")->on("teachers")->references("id")->cascadeOnDelete();
            $table->foreignId("academic_year_id");
            $table->foreign("academic_year_id")->on("academic_years")->references("id")->cascadeOnDelete();
            $table->string("name");
            $table->string("description")->nullable();
            $table->integer("degree");
            $table->date("start_date");
            $table->date("end_date");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};

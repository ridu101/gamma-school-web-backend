<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->string('exam_name');

            $table->decimal('gpa', 3, 2)->default(0);

            $table->unsignedInteger('full_marks')->default(0);
            $table->unsignedInteger('total_marks')->default(0);

            $table->boolean('passed')->default(true);

            $table->timestamps();

            $table->unique(
                ['student_id', 'exam_name'],
                'student_exam_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
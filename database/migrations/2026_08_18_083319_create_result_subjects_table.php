<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('result_subjects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('result_id')
                ->constrained('results')
                ->cascadeOnDelete();

            $table->string('subject');

            $table->unsignedInteger('full_marks')->default(100);
            $table->unsignedInteger('obtained_marks')->default(0);

            $table->string('grade')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_subjects');
    }
};
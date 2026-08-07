<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conduct_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['Positive', 'Negative'])->default('Negative');
            $table->text('description');
            $table->date('incident_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conduct_records');
    }
};

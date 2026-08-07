<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('grade_categories')->nullOnDelete();
            $table->string('label')->nullable();
            $table->decimal('grade_value', 5, 2);
            $table->timestamps();
            // NOTE: legacy 'remarks' generated column intentionally dropped.
            // Passed/Failed is now computed via Grade::status accessor against
            // the admin-configurable Setting::passingThreshold().
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};

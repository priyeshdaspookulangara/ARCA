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
        Schema::create('hr_positions', function (Blueprint $table) {
            $table->id();
            $table->string('position_title'); // Specific title for this instance of a job, e.g., "Senior Developer - Team Lead"
            $table->foreignId('hr_job_id')->constrained('hr_jobs')->onDelete('cascade');
            $table->foreignId('hr_department_id')->constrained('hr_departments')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->foreignId('reports_to_position_id')->nullable()->constrained('hr_positions')->onDelete('set null');
            $table->boolean('is_vacant')->default(true);
            $table->date('effective_date_start')->nullable();
            $table->date('effective_date_end')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_positions');
    }
};

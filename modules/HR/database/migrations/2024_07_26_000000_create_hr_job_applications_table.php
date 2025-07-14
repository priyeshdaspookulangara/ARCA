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
        Schema::create('hr_job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_job_id')->constrained('hr_jobs')->onDelete('cascade');

            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();

            $table->string('resume_path')->nullable()->comment('Path to the stored resume file');
            $table->text('cover_letter')->nullable();

            $table->string('status')->default('applied')->comment('e.g., applied, screening, interviewing, offered, hired, rejected');
            $table->date('applied_date');

            $table->text('notes')->nullable()->comment('Internal notes by recruiters/managers');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['hr_job_id', 'status']);
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_job_applications');
    }
};

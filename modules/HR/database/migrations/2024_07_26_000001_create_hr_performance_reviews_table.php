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
        Schema::create('hr_performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->foreignId('reviewer_id')->comment('Manager/Reviewer')->constrained('hr_employees')->onDelete('cascade');

            $table->date('review_period_start_date');
            $table->date('review_period_end_date');

            $table->unsignedTinyInteger('overall_rating')->nullable()->comment('e.g., 1 to 5');
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('employee_comments')->nullable();
            $table->text('manager_comments')->nullable();

            $table->string('status')->default('draft')->comment('e.g., draft, pending_employee_review, pending_manager_review, finalized');
            $table->timestamp('finalized_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['hr_employee_id', 'review_period_end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_performance_reviews');
    }
};

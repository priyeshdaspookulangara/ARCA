<?php

namespace Modules\HR\TalentManagement\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\TalentManagement\Domain\Entities\PerformanceReview;
use Illuminate\Validation\Rule;

class PerformanceReviewController extends Controller
{
    /**
     * List performance reviews.
     * Can be for a specific employee or all (for admin/manager).
     */
    public function index(Request $request, Employee $employee = null): JsonResponse
    {
        // Add authorization checks here. E.g., employee can see their own,
        // manager can see their direct reports', admin can see all.
        $query = PerformanceReview::with(['employee:id,first_name,last_name', 'reviewer:id,first_name,last_name']);

        if ($employee) {
            $query->where('hr_employee_id', $employee->id);
        }
        // Add other filters like review period, status, etc.
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $reviews = $query->orderBy('review_period_end_date', 'desc')->get();
        return response()->json($reviews);
    }

    /**
     * Create a new (draft) performance review for an employee.
     * Typically initiated by a manager.
     */
    public function store(Request $request, Employee $employee): JsonResponse
    {
        $validated = $request->validate([
            'review_period_start_date' => 'required|date',
            'review_period_end_date' => 'required|date|after:review_period_start_date',
            'reviewer_id' => ['required', 'integer', Rule::exists('hr_employees', 'id')], // Should be manager
            'manager_comments' => 'nullable|string', // Initial comments from manager
        ]);

        $review = $employee->performanceReviews()->create([
            'review_period_start_date' => $validated['review_period_start_date'],
            'review_period_end_date' => $validated['review_period_end_date'],
            'reviewer_id' => $validated['reviewer_id'],
            'manager_comments' => $validated['manager_comments'] ?? null,
            'status' => PerformanceReview::STATUS_DRAFT, // Starts as draft
        ]);

        return response()->json($review, 201);
    }

    /**
     * Display a specific performance review.
     */
    public function show(PerformanceReview $review): JsonResponse
    {
        return response()->json($review->load(['employee', 'reviewer']));
    }

    /**
     * Update a performance review.
     * Used by manager and employee to add comments and change status.
     */
    public function update(Request $request, PerformanceReview $review): JsonResponse
    {
        // Complex authorization logic would be needed here to determine
        // who can update what fields based on the review's current status.
        // E.g., employee can only add comments when status is 'pending_employee_review'.
        // Manager can only add rating when status is 'pending_manager_review'.

        $validated = $request->validate([
            'overall_rating' => 'nullable|integer|min:1|max:5',
            'strengths' => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'employee_comments' => 'nullable|string',
            'manager_comments' => 'nullable|string',
            'status' => ['sometimes', 'required', 'string', Rule::in([
                PerformanceReview::STATUS_DRAFT,
                PerformanceReview::STATUS_PENDING_EMPLOYEE_REVIEW,
                PerformanceReview::STATUS_PENDING_MANAGER_REVIEW,
                PerformanceReview::STATUS_FINALIZED,
            ])],
        ]);

        // If changing status to finalized, set the finalized_at timestamp
        if (isset($validated['status']) && $validated['status'] === PerformanceReview::STATUS_FINALIZED) {
            // Ensure rating is present when finalizing
            if (!isset($validated['overall_rating']) && !$review->overall_rating) {
                return response()->json(['errors' => ['overall_rating' => ['An overall rating is required to finalize the review.']]], 422);
            }
            $validated['finalized_at'] = now();
        }

        $review->update($validated);

        return response()->json($review);
    }

    /**
     * Soft delete a performance review.
     */
    public function destroy(PerformanceReview $review): JsonResponse
    {
        // Authorization needed
        $review->delete();
        return response()->json(null, 204);
    }
}

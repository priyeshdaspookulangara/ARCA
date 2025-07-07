<?php

namespace Modules\HR\PersonnelAdmin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\HR\PersonnelAdmin\Domain\Entities\Position;
use Modules\HR\PersonnelAdmin\Domain\Entities\Job;
use Modules\HR\PersonnelAdmin\Domain\Entities\Department;
use Illuminate\Validation\Rule;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Position::with(['job:id,job_title', 'department:id,name', 'reportsTo:id,position_title']);

        if ($request->has('department_id')) {
            $query->where('hr_department_id', $request->department_id);
        }
        if ($request->has('job_id')) {
            $query->where('hr_job_id', $request->job_id);
        }
        if ($request->has('is_vacant')) {
            $query->where('is_vacant', filter_var($request->is_vacant, FILTER_VALIDATE_BOOLEAN));
        }

        $positions = $query->select(
                                'id',
                                'position_title',
                                'hr_job_id',
                                'hr_department_id',
                                'reports_to_position_id',
                                'is_vacant',
                                'effective_date_start'
                            )
                           ->orderBy('position_title')
                           ->get();
        return response()->json($positions);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'position_title' => 'required|string|max:255',
            'hr_job_id' => [
                'required',
                'integer',
                Rule::exists('hr_jobs', 'id'),
            ],
            'hr_department_id' => [
                'required',
                'integer',
                Rule::exists('hr_departments', 'id'),
            ],
            'description' => 'nullable|string',
            'reports_to_position_id' => [
                'nullable',
                'integer',
                Rule::exists('hr_positions', 'id'),
            ],
            'is_vacant' => 'sometimes|boolean',
            'effective_date_start' => 'nullable|date',
            'effective_date_end' => 'nullable|date|after_or_equal:effective_date_start',
        ]);

        $position = Position::create($validated);

        return response()->json($position->load(['job:id,job_title', 'department:id,name', 'reportsTo:id,position_title']), 201);
    }

    /**
     * Display the specified resource.
     * @param Position $position
     * @return JsonResponse
     */
    public function show(Position $position): JsonResponse
    {
        return response()->json(
            $position->load([
                'job:id,job_title,job_code',
                'department:id,name',
                'reportsTo:id,position_title',
                'directReports:id,position_title', // Assuming directReports relationship is defined
                'currentEmployee:id,first_name,last_name,employee_id_number' // Assuming currentEmployee relationship
            ])
        );
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Position $position
     * @return JsonResponse
     */
    public function update(Request $request, Position $position): JsonResponse
    {
        $validated = $request->validate([
            'position_title' => 'sometimes|required|string|max:255',
            'hr_job_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('hr_jobs', 'id'),
            ],
            'hr_department_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('hr_departments', 'id'),
            ],
            'description' => 'nullable|string',
            'reports_to_position_id' => [
                'nullable',
                'integer',
                Rule::exists('hr_positions', 'id'),
                Rule::notIn([$position->id]), // Cannot report to itself
            ],
            'is_vacant' => 'sometimes|boolean',
            'effective_date_start' => 'nullable|date',
            'effective_date_end' => 'nullable|date|after_or_equal:' . ($request->input('effective_date_start') ?? $position->effective_date_start ?? 'effective_date_start'),
        ]);

        // Prevent setting reports_to_position_id to one of its own direct reports (basic cycle prevention)
        if (isset($validated['reports_to_position_id']) && $validated['reports_to_position_id'] !== null) {
            $potentialParentPosition = Position::find($validated['reports_to_position_id']);
            if ($potentialParentPosition && $position->isAncestorOf($potentialParentPosition)) {
                 return response()->json(['error' => 'Cannot set "reports to" to one of its own descendants.'], 422);
            }
        }

        $position->update($validated);

        return response()->json($position->load(['job:id,job_title', 'department:id,name', 'reportsTo:id,position_title']));
    }

    /**
     * Remove the specified resource from storage.
     * @param Position $position
     * @return JsonResponse
     */
    public function destroy(Position $position): JsonResponse
    {
        // Prevent deletion if position is currently filled by an employee
        if (!$position->is_vacant && $position->currentEmployee()->exists()) {
            return response()->json(['error' => 'Cannot delete a filled position. Vacate the position or reassign the employee first.'], 422);
        }

        // Prevent deletion if other positions report to this one
        if ($position->directReports()->exists()) {
            return response()->json(['error' => 'Cannot delete position with direct reports. Reassign reporting structure first.'], 422);
        }

        $position->delete();

        return response()->json(null, 204);
    }
}

// Add isAncestorOf method to Position model if not already present
// For brevity, I'll assume it would be added to the Position model like this:
/*
// In Modules\HR\PersonnelAdmin\Domain\Entities\Position.php
public function isAncestorOf(Position $otherPosition): bool
{
    $parent = $otherPosition->reportsTo;
    while ($parent) {
        if ($parent->id === $this->id) {
            return true;
        }
        if ($parent->id === $parent->reports_to_position_id) { // Safeguard
            return false;
        }
        $parent = $parent->reportsTo;
    }
    return false;
}

// Add directReports relationship to Position model
public function directReports()
{
    return $this->hasMany(Position::class, 'reports_to_position_id');
}
*/

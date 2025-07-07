<?php

namespace Modules\HR\PersonnelAdmin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\HR\PersonnelAdmin\Domain\Entities\Department;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $departments = Department::with(['parentDepartment', 'manager', 'childDepartments:id,name,parent_department_id'])
                                  ->select('id', 'name', 'description', 'parent_department_id', 'manager_id') // Specify columns
                                  ->orderBy('name')
                                  ->get();
        return response()->json($departments);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:hr_departments,name',
            'description' => 'nullable|string',
            'parent_department_id' => [
                'nullable',
                'integer',
                Rule::exists('hr_departments', 'id'),
            ],
            'manager_id' => [
                'nullable',
                'integer',
                Rule::exists('hr_employees', 'id'), // Ensure manager is an existing employee
            ],
        ]);

        $department = Department::create($validated);

        return response()->json($department->load(['parentDepartment', 'manager']), 201);
    }

    /**
     * Display the specified resource.
     * @param Department $department
     * @return JsonResponse
     */
    public function show(Department $department): JsonResponse
    {
        return response()->json($department->load(['parentDepartment', 'manager', 'childDepartments', 'positions', 'employees']));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Department $department
     * @return JsonResponse
     */
    public function update(Request $request, Department $department): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('hr_departments', 'name')->ignore($department->id),
            ],
            'description' => 'nullable|string',
            'parent_department_id' => [
                'nullable',
                'integer',
                Rule::exists('hr_departments', 'id'),
                Rule::notIn([$department->id]), // Cannot be its own parent
            ],
            'manager_id' => [
                'nullable',
                'integer',
                Rule::exists('hr_employees', 'id'),
            ],
        ]);

        // Prevent setting parent_department_id to one of its own children (basic cycle prevention)
        if (isset($validated['parent_department_id']) && $validated['parent_department_id'] !== null) {
            $potentialParent = Department::find($validated['parent_department_id']);
            if ($potentialParent && $department->isAncestorOf($potentialParent)) {
                 return response()->json(['error' => 'Cannot set parent department to one of its own descendants.'], 422);
            }
        }


        $department->update($validated);

        return response()->json($department->load(['parentDepartment', 'manager']));
    }

    /**
     * Remove the specified resource from storage.
     * @param Department $department
     * @return JsonResponse
     */
    public function destroy(Department $department): JsonResponse
    {
        // Basic check: prevent deletion if it has child departments
        if ($department->childDepartments()->exists()) {
            return response()->json(['error' => 'Cannot delete department with child departments. Reassign children first.'], 422);
        }

        // Basic check: prevent deletion if it has positions or employees
        // More robust checks might be needed (e.g., reassigning them)
        if ($department->positions()->exists() || $department->employees()->exists()) {
             return response()->json(['error' => 'Cannot delete department with associated positions or employees. Reassign them first.'], 422);
        }

        $department->delete();

        return response()->json(null, 204);
    }
}

// Add isAncestorOf method to Department model if not already present
// For brevity, I'll assume it would be added to the Department model like this:
/*
// In Modules\HR\PersonnelAdmin\Domain\Entities\Department.php
public function isAncestorOf(Department $otherDepartment): bool
{
    $parent = $otherDepartment->parentDepartment;
    while ($parent) {
        if ($parent->id === $this->id) {
            return true;
        }
        $parent = $parent->parentDepartment;
    }
    return false;
}
*/

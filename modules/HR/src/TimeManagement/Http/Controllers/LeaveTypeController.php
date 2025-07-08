<?php

namespace Modules\HR\TimeManagement\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller; // Base controller
use Illuminate\Http\Request;
use Modules\HR\TimeManagement\Domain\Entities\LeaveType;
use Illuminate\Validation\Rule;

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = LeaveType::query();
        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }
        $leaveTypes = $query->orderBy('name')->get();
        return response()->json($leaveTypes);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:hr_leave_types,name',
            'description' => 'nullable|string',
            'is_paid' => 'sometimes|boolean',
            'default_entitlement_days' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $leaveType = LeaveType::create($validated);
        return response()->json($leaveType, 201);
    }

    /**
     * Display the specified resource.
     * @param LeaveType $leaveType
     * @return JsonResponse
     */
    public function show(LeaveType $leaveType): JsonResponse
    {
        return response()->json($leaveType);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param LeaveType $leaveType
     * @return JsonResponse
     */
    public function update(Request $request, LeaveType $leaveType): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('hr_leave_types', 'name')->ignore($leaveType->id),
            ],
            'description' => 'nullable|string',
            'is_paid' => 'sometimes|boolean',
            'default_entitlement_days' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $leaveType->update($validated);
        return response()->json($leaveType);
    }

    /**
     * Remove the specified resource from storage.
     * @param LeaveType $leaveType
     * @return JsonResponse
     */
    public function destroy(LeaveType $leaveType): JsonResponse
    {
        // Check if any leave requests are using this leave type
        // if ($leaveType->leaveRequests()->exists()) {
        //     return response()->json(['error' => 'Cannot delete leave type with associated leave requests. Consider deactivating it instead.'], 422);
        // }
        // For now, direct deletion is allowed as per plan. Add check later if LeaveRequest model relationship is uncommented.

        $leaveType->delete(); // Soft delete
        return response()->json(null, 204);
    }
}

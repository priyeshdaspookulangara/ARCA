<?php

namespace Modules\HR\TimeManagement\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\TimeManagement\Domain\Entities\LeaveType;
use Modules\HR\TimeManagement\Domain\Entities\LeaveRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // Assuming auth for user IDs
use Carbon\Carbon;
use Modules\HR\TimeManagement\Services\LeaveBalanceService;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of leave requests.
     * Can be for a specific employee or all (for admin/manager).
     * @param Request $request
     * @param Employee $employee (optional route model binding)
     * @return JsonResponse
     */
    public function index(Request $request, Employee $employee = null): JsonResponse
    {
        $query = LeaveRequest::with(['employee:id,first_name,last_name,employee_id_number', 'leaveType:id,name']);

        if ($employee) {
            // Typically, an employee should only see their own requests.
            // Add authorization logic here if not handled by middleware.
            // For now, assuming if $employee is passed, it's for that employee.
            $query->where('hr_employee_id', $employee->id);
        } else {
            // Admin/Manager view: potentially filter by department, status, etc.
            // Add authorization: e.g., if (Auth::user()->can('view_all_leave_requests'))
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            if ($request->has('leave_type_id')) {
                $query->where('hr_leave_type_id', $request->leave_type_id);
            }
            if ($request->has('date_from')) {
                $query->where('start_date', '>=', $request->date_from);
            }
            if ($request->has('date_to')) {
                $query->where('end_date', '<=', $request->date_to);
            }
        }

        $leaveRequests = $query->orderBy('start_date', 'desc')->get();
        return response()->json($leaveRequests);
    }

    /**
     * Store a newly created leave request for an employee.
     * @param Request $request
     * @param Employee $employee
     * @return JsonResponse
     */
    public function store(Request $request, Employee $employee): JsonResponse
    {
        $validated = $request->validate([
            'hr_leave_type_id' => ['required', 'integer', Rule::exists('hr_leave_types', 'id')->where('is_active', true)],
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'duration_days' => 'nullable|numeric|min:0.5', // Optional, can be calculated
            'reason' => 'required|string|max:1000',
            'employee_remarks' => 'nullable|string|max:1000',
        ]);

        // Calculate duration if not provided or to verify
        $calculatedDuration = LeaveRequest::calculateDuration($validated['start_date'], $validated['end_date'], true); // true = exclude weekends

        if (isset($validated['duration_days']) && (float)$validated['duration_days'] !== $calculatedDuration && $calculatedDuration > 0) {
            // If duration is provided and doesn't match calculation (and calc is not zero),
            // it might be a special case (e.g. half day on a specific day).
            // For now, we can either use provided, or throw error, or use calculated.
            // Let's prefer calculated if it's a full day span, or allow provided for precision.
            // If provided is very different from calculated for full days, it's suspicious.
            // For simplicity, if duration_days is provided, we use it. Otherwise, calculate.
             if(abs((float)$validated['duration_days'] - $calculatedDuration) > 0.1 && $durationDays > 0.5) { // Allow small diff for float, only if not half day
                // return response()->json(['error' => "Provided duration {$validated['duration_days']} does not match calculated duration {$calculatedDuration} (excluding weekends)."], 422);
             }
             // Using provided duration if available
             $durationToStore = (float) $validated['duration_days'];
        } else {
            $durationToStore = $calculatedDuration;
        }

        if ($durationToStore <= 0) {
            return response()->json(['errors' => ['duration_days' => ['Leave duration must be at least 0.5 days. Check dates and ensure they do not only span a weekend.']]], 422);
        }


        // Basic overlapping check (can be more sophisticated)
        $overlappingExists = $employee->leaveRequests()
            ->where('status', '!=', LeaveRequest::STATUS_REJECTED)
            ->where('status', '!=', LeaveRequest::STATUS_CANCELLED_BY_ADMIN)
            ->where('status', '!=', LeaveRequest::STATUS_CANCELLED_BY_EMPLOYEE)
            ->where(function ($query) use ($validated) {
                $query->where(function ($q) use ($validated) { // Request starts within existing
                    $q->where('start_date', '<=', $validated['start_date'])
                      ->where('end_date', '>=', $validated['start_date']);
                })->orWhere(function ($q) use ($validated) { // Request ends within existing
                    $q->where('start_date', '<=', $validated['end_date'])
                      ->where('end_date', '>=', $validated['end_date']);
                })->orWhere(function ($q) use ($validated) { // Request encapsulates existing
                    $q->where('start_date', '>=', $validated['start_date'])
                      ->where('end_date', '<=', $validated['end_date']);
                });
            })->exists();

        if ($overlappingExists) {
            return response()->json(['error' => 'This leave request overlaps with an existing request.'], 422);
        }

        $leaveRequest = $employee->leaveRequests()->create([
            'hr_leave_type_id' => $validated['hr_leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'duration_days' => $durationToStore,
            'reason' => $validated['reason'],
            'employee_remarks' => $validated['employee_remarks'] ?? null,
            'status' => LeaveRequest::STATUS_PENDING,
        ]);

        return response()->json($leaveRequest->load(['employee:id,first_name,last_name', 'leaveType:id,name']), 201);
    }

    /**
     * Display the specified leave request.
     * @param LeaveRequest $leaveRequest
     * @return JsonResponse
     */
    public function show(LeaveRequest $leaveRequest): JsonResponse
    {
        // Add authorization: Employee can see their own, manager/admin can see based on hierarchy/role
        // if (Auth::id() !== $leaveRequest->hr_employee_id && !Auth::user()->can('view_any_leave_request')) {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }
        return response()->json($leaveRequest->load(['employee:id,first_name,last_name', 'leaveType:id,name']));
    }

    /**
     * Update the specified leave request.
     * Employee can cancel their PENDING request.
     * Manager/Admin can approve/reject PENDING requests.
     * @param Request $request
     * @param LeaveRequest $leaveRequest
     * @return JsonResponse
     */
    public function update(Request $request, LeaveRequest $leaveRequest): JsonResponse
    {
        // Authorization: Who can update what?
        // $user = Auth::user(); // Assuming you have user authentication

        // Scenario 1: Employee cancelling their own PENDING request
        // if ($user->id === $leaveRequest->employee->user_id && $leaveRequest->status === LeaveRequest::STATUS_PENDING) {
        // For now, let's assume role/permission checks are separate or this is an employee endpoint
        if ($request->has('status') && $request->input('status') === LeaveRequest::STATUS_CANCELLED_BY_EMPLOYEE) {
            if ($leaveRequest->status !== LeaveRequest::STATUS_PENDING) {
                return response()->json(['error' => 'Only pending requests can be cancelled by the employee.'], 400);
            }
            $leaveRequest->status = LeaveRequest::STATUS_CANCELLED_BY_EMPLOYEE;
            $leaveRequest->cancelled_at = now();
            $leaveRequest->cancelled_by_role = LeaveRequest::CANCELLED_BY_EMPLOYEE_ROLE;
            $leaveRequest->save();
            return response()->json($leaveRequest);
        }

        // Scenario 2: Manager/Admin approving or rejecting a PENDING request
        // Add authorization check here: e.g., if ($user->can('manage_leave_requests'))
        if ($request->has('status') && in_array($request->input('status'), [LeaveRequest::STATUS_APPROVED, LeaveRequest::STATUS_REJECTED])) {
            if ($leaveRequest->status !== LeaveRequest::STATUS_PENDING) {
                return response()->json(['error' => 'Only pending requests can be approved or rejected.'], 400);
            }

            $validatedStatusUpdate = $request->validate([
                'status' => ['required', Rule::in([LeaveRequest::STATUS_APPROVED, LeaveRequest::STATUS_REJECTED])],
                'approver_remarks' => 'nullable|string|max:1000',
                'rejection_reason' => ($request->input('status') === LeaveRequest::STATUS_REJECTED) ? 'required|string|max:1000' : 'nullable|string|max:1000',
            ]);

        $leaveBalanceService = new LeaveBalanceService();

        // If approving, check balance first
        if ($validatedStatusUpdate['status'] === LeaveRequest::STATUS_APPROVED) {
            if (!$leaveBalanceService->hasSufficientBalance($leaveRequest)) {
                return response()->json(['error' => 'Insufficient leave balance for this request.'], 422);
            }
        }

            $leaveRequest->status = $validatedStatusUpdate['status'];
            $leaveRequest->approver_remarks = $validatedStatusUpdate['approver_remarks'] ?? null;
            // $leaveRequest->approver_user_id = $user->id; // Assuming Auth gives the manager/admin ID

            if ($leaveRequest->status === LeaveRequest::STATUS_APPROVED) {
                $leaveRequest->approved_at = now();
                $leaveRequest->rejection_reason = null; // Clear rejection reason if any
            $leaveBalanceService->debitApprovedLeave($leaveRequest);
            } elseif ($leaveRequest->status === LeaveRequest::STATUS_REJECTED) {
                $leaveRequest->rejected_at = now();
                $leaveRequest->rejection_reason = $validatedStatusUpdate['rejection_reason'];
            }
            $leaveRequest->save();
            return response()->json($leaveRequest);
        }

        // Scenario 3: Admin cancelling an already approved request (less common, needs care)
        if ($request->has('status') && $request->input('status') === LeaveRequest::STATUS_CANCELLED_BY_ADMIN) {
             // Add authorization: if ($user->can('cancel_approved_leave'))
            if (!in_array($leaveRequest->status, [LeaveRequest::STATUS_PENDING, LeaveRequest::STATUS_APPROVED])) {
                 return response()->json(['error' => 'Only pending or approved requests can be cancelled by an admin/manager.'], 400);
            }
            $validatedCancel = $request->validate([
                'cancellation_reason' => 'required|string|max:1000', // Custom field for admin cancellation reason
            ]);

        $originalStatus = $leaveRequest->getOriginal('status');

            $leaveRequest->status = LeaveRequest::STATUS_CANCELLED_BY_ADMIN;
            $leaveRequest->cancelled_at = now();
            $leaveRequest->cancelled_by_role = LeaveRequest::CANCELLED_BY_ADMIN_ROLE; // Or manager
            $leaveRequest->approver_remarks = ($leaveRequest->approver_remarks ? $leaveRequest->approver_remarks . "\n" : '') . "Cancelled by admin/manager: " . $validatedCancel['cancellation_reason'];
            $leaveRequest->save();

        // If the request was previously approved, credit the leave back
        if ($originalStatus === LeaveRequest::STATUS_APPROVED) {
            (new LeaveBalanceService())->creditCancelledLeave($leaveRequest);
        }

            return response()->json($leaveRequest);
        }


        return response()->json(['error' => 'Invalid update operation or missing parameters.'], 400);
    }

    // No direct DELETE endpoint for leave requests; they are cancelled or rejected.
    // Soft deletes are enabled on the model if admin needs to remove entirely.
}

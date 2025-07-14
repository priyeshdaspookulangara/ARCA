<?php

namespace Modules\HR\TalentManagement\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\HR\PersonnelAdmin\Domain\Entities\Job;
use Modules\HR\TalentManagement\Domain\Entities\JobApplication;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage; // If handling file uploads

class JobApplicationController extends Controller
{
    /**
     * List all job applications, optionally filtered by job.
     */
    public function index(Request $request): JsonResponse
    {
        // Authorization: Should be restricted to recruiters/admins
        $query = JobApplication::with('job:id,job_title');

        if ($request->has('hr_job_id')) {
            $query->where('hr_job_id', $request->hr_job_id);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->orderBy('applied_date', 'desc')->get();
        return response()->json($applications);
    }

    /**
     * Public-facing method to store a new application for a specific job.
     */
    public function apply(Request $request, Job $job): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048', // Example validation for file upload
            'cover_letter' => 'nullable|string',
        ]);

        // Check if already applied for this job with this email
        $alreadyApplied = JobApplication::where('hr_job_id', $job->id)
                                        ->where('email', $validated['email'])
                                        ->exists();
        if ($alreadyApplied) {
            return response()->json(['error' => 'You have already applied for this job with this email address.'], 422);
        }

        $resumePath = null;
        if ($request->hasFile('resume')) {
            // Path needs to be configured in filesystems.php config
            $resumePath = $request->file('resume')->store('resumes', 'public');
        }

        $application = $job->applications()->create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'resume_path' => $resumePath,
            'cover_letter' => $validated['cover_letter'] ?? null,
            'status' => JobApplication::STATUS_APPLIED,
            'applied_date' => now()->toDateString(),
        ]);

        return response()->json($application, 201);
    }

    /**
     * Display a specific job application.
     */
    public function show(JobApplication $application): JsonResponse
    {
        // Authorization: Should be restricted to recruiters/admins
        return response()->json($application->load('job:id,job_title'));
    }

    /**
     * Update the status or notes of a job application.
     */
    public function update(Request $request, JobApplication $application): JsonResponse
    {
        // Authorization: Should be restricted to recruiters/admins
        $validated = $request->validate([
            'status' => ['sometimes', 'required', 'string', Rule::in([
                JobApplication::STATUS_APPLIED,
                JobApplication::STATUS_SCREENING,
                JobApplication::STATUS_INTERVIEWING,
                JobApplication::STATUS_OFFERED,
                JobApplication::STATUS_HIRED,
                JobApplication::STATUS_REJECTED,
            ])],
            'notes' => 'nullable|string',
        ]);

        // Logic for when an applicant is hired:
        // This is a complex process. It would likely trigger other events/actions
        // rather than being a simple status change.
        // E.g., it would kick off the Employee creation process.
        // For now, this is just a status update.
        if (isset($validated['status']) && $validated['status'] === JobApplication::STATUS_HIRED) {
            // In a real app:
            // 1. Check if an employee record already exists for this person.
            // 2. Fire an event like 'ApplicantHired' with the application data.
            // 3. A listener would handle creating the Employee, Contract, etc.
            // For this phase, we just update the status.
            $validated['notes'] = ($application->notes ? $application->notes . "\n" : '') . "Status changed to Hired on " . now()->toDateTimeString();
        }

        $application->update($validated);
        return response()->json($application);
    }

    /**
     * Remove a job application.
     */
    public function destroy(JobApplication $application): JsonResponse
    {
        // Authorization: Should be restricted
        // Potentially delete resume file from storage
        if ($application->resume_path) {
            Storage::disk('public')->delete($application->resume_path);
        }
        $application->delete(); // Soft delete
        return response()->json(null, 204);
    }
}

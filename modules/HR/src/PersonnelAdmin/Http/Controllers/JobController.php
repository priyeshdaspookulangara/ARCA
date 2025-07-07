<?php

namespace Modules\HR\PersonnelAdmin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\HR\PersonnelAdmin\Domain\Entities\Job;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $jobs = Job::select('id', 'job_title', 'job_code', 'job_description', 'min_salary', 'max_salary')
                   ->orderBy('job_title')
                   ->get();
        return response()->json($jobs);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'job_title' => 'required|string|max:255|unique:hr_jobs,job_title',
            'job_description' => 'nullable|string',
            'job_code' => 'nullable|string|max:50|unique:hr_jobs,job_code',
            'min_salary' => 'nullable|numeric|min:0|lte:max_salary', // lte:max_salary requires max_salary to be present
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary', // gte:min_salary requires min_salary to be present
        ]);

        $job = Job::create($validated);

        return response()->json($job, 201);
    }

    /**
     * Display the specified resource.
     * @param Job $job
     * @return JsonResponse
     */
    public function show(Job $job): JsonResponse
    {
        // Consider loading related positions if that's a common requirement: $job->load('positions')
        return response()->json($job);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Job $job
     * @return JsonResponse
     */
    public function update(Request $request, Job $job): JsonResponse
    {
        $validated = $request->validate([
            'job_title' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('hr_jobs', 'job_title')->ignore($job->id),
            ],
            'job_description' => 'nullable|string',
            'job_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('hr_jobs', 'job_code')->ignore($job->id),
            ],
            'min_salary' => 'nullable|numeric|min:0|lte:' . ($request->input('max_salary') ?? $job->max_salary ?? 'max_salary'),
            'max_salary' => 'nullable|numeric|min:0|gte:' . ($request->input('min_salary') ?? $job->min_salary ?? 'min_salary'),
        ]);

        // Handle cases where only one salary is provided for update, ensuring validation still makes sense
        if ($request->has('min_salary') && !$request->has('max_salary') && $job->max_salary !== null) {
            if ($validated['min_salary'] > $job->max_salary) {
                return response()->json(['errors' => ['min_salary' => ['Min salary cannot exceed the current max salary of ' . $job->max_salary]]], 422);
            }
        }
        if ($request->has('max_salary') && !$request->has('min_salary') && $job->min_salary !== null) {
             if ($validated['max_salary'] < $job->min_salary) {
                return response()->json(['errors' => ['max_salary' => ['Max salary cannot be less than the current min salary of ' . $job->min_salary]]], 422);
            }
        }


        $job->update($validated);

        return response()->json($job);
    }

    /**
     * Remove the specified resource from storage.
     * @param Job $job
     * @return JsonResponse
     */
    public function destroy(Job $job): JsonResponse
    {
        // Prevent deletion if job is associated with any positions
        if ($job->positions()->exists()) {
            return response()->json(['error' => 'Cannot delete job title with associated positions. Reassign or delete positions first.'], 422);
        }

        $job->delete();

        return response()->json(null, 204);
    }
}

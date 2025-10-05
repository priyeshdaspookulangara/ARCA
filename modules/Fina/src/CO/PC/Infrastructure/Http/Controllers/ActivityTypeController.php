<?php

namespace Modules\Fina\CO\PC\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\CO\PC\Domain\ActivityTypeService;

class ActivityTypeController extends Controller
{
    private ActivityTypeService $activityTypeService;

    public function __construct(ActivityTypeService $activityTypeService)
    {
        $this->activityTypeService = $activityTypeService;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $activityType = $this->activityTypeService->createActivityType($data);

        return response()->json($activityType, 201);
    }

    public function show($id)
    {
        $activityType = $this->activityTypeService->getActivityType($id);

        if (!$activityType) {
            return response()->json(['message' => 'Activity type not found'], 404);
        }

        return response()->json($activityType);
    }

    public function index()
    {
        $activityTypes = $this->activityTypeService->getAllActivityTypes();

        return response()->json($activityTypes);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'unit' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $activityType = $this->activityTypeService->updateActivityType($id, $data);

        if (!$activityType) {
            return response()->json(['message' => 'Activity type not found'], 404);
        }

        return response()->json($activityType);
    }

    public function destroy($id)
    {
        $deleted = $this->activityTypeService->deleteActivityType($id);

        if (!$deleted) {
            return response()->json(['message' => 'Activity type not found'], 404);
        }

        return response()->json(null, 204);
    }
}
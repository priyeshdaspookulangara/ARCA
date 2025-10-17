<?php

namespace Modules\CRM\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Sales\Domain\ActivityLogRepositoryInterface;
use Modules\CRM\Sales\Domain\Model\ActivityLog;

class ActivityLogController extends Controller
{
    private $activityLogRepository;

    public function __construct(ActivityLogRepositoryInterface $activityLogRepository)
    {
        $this->activityLogRepository = $activityLogRepository;
    }

    public function index()
    {
        return $this->activityLogRepository->getAll();
    }

    public function store(Request $request)
    {
        $activityLog = new ActivityLog($request->all());
        return $this->activityLogRepository->save($activityLog);
    }

    public function show($id)
    {
        return $this->activityLogRepository->findById($id);
    }

    public function update(Request $request, $id)
    {
        $activityLog = $this->activityLogRepository->findById($id);
        $activityLog->fill($request->all());
        return $this->activityLogRepository->save($activityLog);
    }

    public function destroy($id)
    {
        $activityLog = $this->activityLogRepository->findById($id);
        return response()->json(['success' => $this->activityLogRepository->delete($activityLog)]);
    }
}
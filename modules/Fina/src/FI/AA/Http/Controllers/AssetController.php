<?php

namespace Modules\Fina\FI\AA\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\FI\AA\Application\CreateAssetService;
use Modules\Fina\FI\AA\Domain\Repositories\AssetMasterRepositoryInterface;

class AssetController extends Controller
{
    private $createAssetService;
    private $assetMasterRepository;

    public function __construct(
        CreateAssetService $createAssetService,
        AssetMasterRepositoryInterface $assetMasterRepository
    ) {
        $this->createAssetService = $createAssetService;
        $this->assetMasterRepository = $assetMasterRepository;
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $asset = ($this->createAssetService)($data);
        return response()->json($asset, 201);
    }

    public function show(int $id)
    {
        $asset = $this->assetMasterRepository->find($id);
        if (!$asset) {
            return response()->json(['message' => 'Asset not found'], 404);
        }
        return response()->json($asset);
    }
}

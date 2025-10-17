<?php

namespace Modules\CRM\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Product\Domain\ProductCatalogRepositoryInterface;
use Modules\CRM\Product\Domain\Model\ProductCatalog;

class ProductCatalogController extends Controller
{
    private $productCatalogRepository;

    public function __construct(ProductCatalogRepositoryInterface $productCatalogRepository)
    {
        $this->productCatalogRepository = $productCatalogRepository;
    }

    public function index()
    {
        return $this->productCatalogRepository->getAll();
    }

    public function store(Request $request)
    {
        $productCatalog = new ProductCatalog($request->all());
        return $this->productCatalogRepository->save($productCatalog);
    }

    public function show($id)
    {
        return $this->productCatalogRepository->findById($id);
    }

    public function update(Request $request, $id)
    {
        $productCatalog = $this->productCatalogRepository->findById($id);
        $productCatalog->fill($request->all());
        return $this->productCatalogRepository->save($productCatalog);
    }

    public function destroy($id)
    {
        $productCatalog = $this->productCatalogRepository->findById($id);
        return response()->json(['success' => $this->productCatalogRepository->delete($productCatalog)]);
    }
}
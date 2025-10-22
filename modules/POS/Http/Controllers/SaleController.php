<?php

namespace Modules\POS\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\POS\Models\OfflineTransaction;
use Illuminate\Support\Str;
use Modules\POS\Events\OfflineTransactionStored;
use Modules\POS\Models\ProductCache;
use Modules\POS\Models\LoyaltyCache;
use Modules\EventBus\Services\EventProducer;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        // Logic to list sales
        return response()->json(['message' => 'List of sales']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, EventProducer $eventProducer): JsonResponse
    {
        $isOffline = !$this->isNetworkAvailable();

        if ($isOffline) {
            return $this->storeOffline($request);
        }

        $validatedData = $request->validate([
            'product_id' => 'required|string',
            'loyalty_id' => 'nullable|string',
            'user_id' => 'required|string',
            'shift_id' => 'required|string',
        ]);

        $eventProducer->publish('POS.Sale.Completed', 'ARCA.POS.Terminal', $validatedData);

        return response()->json(['message' => 'Sale created successfully online'], 201);
    }

    private function storeOffline(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'product_id' => 'required|string',
            'loyalty_id' => 'nullable|string',
            'user_id' => 'required|string',
            'shift_id' => 'required|string',
        ]);

        $product = ProductCache::find($validatedData['product_id']);
        if (!$product) {
            return response()->json(['message' => 'Product not found in cache.'], 404);
        }

        if (isset($validatedData['loyalty_id'])) {
            $loyalty = LoyaltyCache::find($validatedData['loyalty_id']);
            if (!$loyalty) {
                return response()->json(['message' => 'Loyalty program not found in cache.'], 404);
            }
        }

        $transactionId = $this->generateOfflineTransactionId();

        try {
            $payload = json_encode([
                'product_id' => $product->product_id,
                'price' => $product->price,
                'tax' => $product->tax,
                'loyalty_id' => $validatedData['loyalty_id'] ?? null,
            ]);

            $offlineTransaction = OfflineTransaction::create([
                'transaction_id' => $transactionId,
                'timestamp' => now(),
                'payload_json' => $payload,
                'status' => 'PendingSync',
            ]);

            event(new OfflineTransactionStored(
                $offlineTransaction,
                $validatedData['user_id'],
                $validatedData['shift_id'],
                config('pos.terminal_id')
            ));

            return response()->json([
                'message' => 'Sale stored offline successfully',
                'transaction_id' => $transactionId
            ], 202);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to store sale offline.', 'error' => $e->getMessage()], 500);
        }
    }

    private function generateOfflineTransactionId(): string
    {
        $storeCode = config('pos.store_code');
        $terminalId = config('pos.terminal_id');
        $date = now()->format('Ymd');
        $autoNo = str_pad(OfflineTransaction::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

        return "TXN-OFF-{$storeCode}-{$terminalId}-{$date}-{$autoNo}";
    }

    protected function isNetworkAvailable(): bool
    {
        $rthEndpoint = config('pos.rth_endpoint');
        $urlParts = parse_url($rthEndpoint);
        $host = $urlParts['host'];
        $port = $urlParts['port'] ?? 80;
        $timeout = 5;

        try {
            $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
            if ($socket) {
                fclose($socket);
                return true;
            }
        } catch (\Exception $e) {
            // Ignore the exception and return false
        }

        return false;
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        // Logic to show a specific sale
        return response()->json(['message' => "Sale details for ID: $id"]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        // Logic to update a sale
        return response()->json(['message' => "Sale ID: $id updated successfully"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        // Logic to delete a sale
        return response()->json(['message' => "Sale ID: $id deleted successfully"]);
    }
}
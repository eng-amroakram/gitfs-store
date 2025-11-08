<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomersSyncController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    // Sync customers on server to api mobile app
    public function syncCustomers(Request $request)
    {
        $customers = $this->customerService->allUnsynced(['*']);
        return response()->json([
            'status' => 'success',
            'customers' => $customers,
        ]);
    }

    public function confirmSync(Request $request)
    {
        try {
            DB::beginTransaction();

            $ids = $request->input('ids', []); // array of IDs or UUIDs

            if (empty($ids)) {
                return response()->json(['message' => 'No IDs provided'], 400);
            }

            $this->customerService->confirmSync($ids);

            DB::commit();

            return response()->json([
                'message' => 'Customers synced_at updated successfully',
                'count' => count($ids)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to confirm sync', 'error' => $e->getMessage()], 500);
        }
    }
}

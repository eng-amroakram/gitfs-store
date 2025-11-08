<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\SaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesSyncController extends Controller
{
    protected $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    // Sync sales on server to api mobile app
    public function syncSales(Request $request)
    {
        $sales = $this->saleService->all(['*']);
        return response()->json([
            'status' => 'success',
            'data' => $sales,
        ]);
    }

    public function syncSalesToServer(Request $request)
    {
        $salesData = $request->input('sales', []); // قائمة المبيعات من التطبيق
        $syncedSalesIds = [];
        $syncedItemsIds = [];

        foreach ($salesData as $saleData) {
            // البحث أو إنشاء سجل بيع جديد حسب uuid
            $sale = Sale::firstOrNew(['uuid' => $saleData['uuid'] ?? null]);

            $sale->customer_id    = $saleData['customer_id'] ?? null;
            $sale->user_id        = $saleData['user_id'] ?? null;
            $sale->invoice_number = $saleData['invoice_number'] ?? null;
            $sale->total          = $saleData['total'] ?? 0;
            $sale->grand_total    = $saleData['grand_total'] ?? 0;
            $sale->discount       = $saleData['discount'] ?? 0;
            $sale->status         = $saleData['status'] ?? 'draft';
            $sale->description    = $saleData['description'] ?? null;
            $sale->notes          = $saleData['notes'] ?? null;
            $sale->created_by     = $saleData['created_by'] ?? null;
            $sale->updated_by     = $saleData['updated_by'] ?? null;
            $sale->synced_at      = now();

            $sale->save();

            // مزامنة البنود مباشرة من items
            $items = $saleData['items'] ?? [];
            foreach ($items as $itemData) {
                $saleItem = SaleItem::firstOrNew(['id' => $itemData['id'] ?? null]);
                $saleItem->sale_id  = $sale->id;
                $saleItem->item_id  = $itemData['item_id'] ?? null;
                $saleItem->quantity = $itemData['quantity'] ?? 0;
                $saleItem->price    = $itemData['price'] ?? 0;
                $saleItem->subtotal = $itemData['subtotal'] ?? 0;
                // $saleItem->created_by = $itemData['created_by'] ?? null;
                // $saleItem->updated_by = $itemData['updated_by'] ?? null;
                $saleItem->synced_at = now();
                $saleItem->save();

                // إضافة ID الخاص بالبند من التطبيق
                $syncedItemsIds[] = $itemData['id'] ?? null;
            }

            // إضافة ID الخاص بالبيع من التطبيق
            $syncedSalesIds[] = $saleData['id'] ?? null;
        }

        return response()->json([
            'synced_sales_ids' => $syncedSalesIds,
            'synced_items_ids' => $syncedItemsIds,
            'message' => count($syncedSalesIds) . " عملية بيع تمت مزامنتها، " .
                count($syncedItemsIds) . " عنصر بيع تمت مزامنته"
        ]);
    }
}

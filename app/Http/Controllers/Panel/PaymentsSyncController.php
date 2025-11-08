<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentsSyncController extends Controller
{
    public function syncPaymentsToServer(Request $request)
    {
        $paymentsData = $request->input('payments', []); // بيانات الدفعات من التطبيق
        $syncedPaymentIds = [];

        foreach ($paymentsData as $paymentData) {
            // البحث أو إنشاء الدفعة حسب uuid
            $payment = Payment::firstOrNew(['uuid' => $paymentData['uuid'] ?? null]);

            $payment->payment_reference  = $paymentData['payment_reference'];
            $payment->paymentable_type   = $paymentData['paymentable_type'] == 'sale' ? 'App\Models\Sale' : 'App\Models\Reservation'; // sale أو reservation
            $payment->paymentable_id     = $paymentData['paymentable_id'];
            $payment->customer_id        = $paymentData['customer_id'] ?? null;
            $payment->amount             = $paymentData['amount'];
            $payment->method             = $paymentData['method'];
            $payment->notes              = $paymentData['notes'] ?? null;
            $payment->created_by         = $paymentData['created_by'] ?? null;
            $payment->updated_by         = $paymentData['updated_by'] ?? null;
            $payment->synced_at          = now();

            $payment->save();

            $syncedPaymentIds[] = $paymentData['id']; // id من التطبيق
        }

        return response()->json([
            'synced_ids' => $syncedPaymentIds,
            'message'    => count($syncedPaymentIds) . " دفعة تمّت مزامنتها"
        ]);
    }
}

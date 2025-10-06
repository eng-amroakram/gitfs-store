<div class="container-fluid p-5">

    {{-- العنوان --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">دفعة <span dir="ltr">#{{ $payment->payment_reference }}</span></h3>
        <a href="{{ route('admin.panel.payments.list') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-2"></i> رجوع
        </a>
    </div>

    <div class="row">
        {{-- معلومات الدفعة --}}
        <div class="col-md-4 mb-3">
            <div class="card shadow-lg h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">معلومات الدفعة</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">الرقم:
                            <span dir="ltr">{{ $payment->payment_reference }}</span>
                        </li>
                        <li class="list-group-item">العميل: {{ $payment->paymentable->customer->name ?? '---' }}</li>
                        <li class="list-group-item">المبلغ:
                            <span class="badge badge-info text-dark" dir="ltr">
                                {{ number_format($payment->amount, 2) }}
                            </span>
                        </li>
                        <li class="list-group-item">طريقة الدفع:
                            <span class="badge badge-warning text-dark" dir="ltr">
                                {{ ucfirst($payment->method) }}
                            </span>
                        </li>
                        <li class="list-group-item">الحالة:
                            @php
                                $status = $payment->paymentable->status ?? '---';
                                $badgeClass = match ($status) {
                                    'paid' => 'badge-success',
                                    'partial' => 'badge-warning',
                                    default => 'badge-danger',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                        </li>
                        <li class="list-group-item">التاريخ:
                            <span dir="ltr">{{ $payment->created_at->format('Y-m-d H:i') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- تفاصيل الفاتورة أو الحجز المرتبط --}}
        <div class="col-md-8 mb-3">
            <div class="card shadow-lg h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">تفاصيل {{ __(class_basename($payment->paymentable_type)) }}</h5>

                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>الإجمالي</span>
                            <strong>{{ number_format($payment->paymentable->total ?? $payment->paymentable->grand_total, 2) }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>المدفوع حتى الآن</span>
                            @php
                                $totalPaid = $payment->paymentable->payments->sum('amount');
                                if ($payment->paymentable instanceof \App\Models\Reservation) {
                                    $totalPaid += $payment->paymentable->deposit; // ✅ أضف العربون
                                }
                            @endphp
                            <strong>{{ number_format($totalPaid, 2) }}</strong>
                        </li>

                        <li class="list-group-item d-flex justify-content-between">
                            <span>المتبقي</span>
                            @php
                                $totalAmount = $payment->paymentable->total ?? $payment->paymentable->grand_total;
                                $remaining = $totalAmount - $totalPaid;
                            @endphp
                            <strong class="text-danger">{{ number_format(max($remaining, 0), 2) }}</strong>
                        </li>
                    </ul>

                    {{-- جدول العناصر إذا كانت مبيعة --}}
                    @if ($payment->paymentable instanceof \App\Models\Sale)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>المنتج</th>
                                        <th>الكمية</th>
                                        <th>السعر</th>
                                        <th>المجموع</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payment->paymentable->items as $item)
                                        <tr>
                                            <td><span dir="ltr">{{ $loop->iteration }}</span></td>
                                            <td><span dir="ltr">{{ $item->item->name }}</span></td>
                                            <td><span dir="ltr">{{ $item->quantity }}</span></td>
                                            <td><span dir="ltr">{{ number_format($item->price, 2) }}</span></td>
                                            <td><span dir="ltr">{{ number_format($item->subtotal, 2) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">لا توجد عناصر</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{-- يمكن إضافة جدول للحجوزات إذا أردت --}}
                    @if ($payment->paymentable instanceof \App\Models\Reservation)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>المنتج</th>
                                        <th>الكمية</th>
                                        <th>السعر</th>
                                        <th>المجموع</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payment->paymentable->items as $item)
                                        <tr>
                                            <td><span dir="ltr">{{ $loop->iteration }}</span></td>
                                            <td><span dir="ltr">{{ $item->item->name }}</span></td>
                                            <td><span dir="ltr">{{ $item->quantity }}</span></td>
                                            <td><span dir="ltr">{{ number_format($item->price, 2) }}</span></td>
                                            <td><span dir="ltr">{{ number_format($item->subtotal, 2) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">لا توجد عناصر</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

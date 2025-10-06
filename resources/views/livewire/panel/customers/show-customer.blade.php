<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-user me-2"></i>تفاصيل العميل
            </h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>الاسم:</strong> {{ $customer->name }}</li>
                <li class="list-group-item"><strong>الهاتف:</strong> {{ $customer->phone ?? '-' }}</li>
                <li class="list-group-item"><strong>البريد:</strong> {{ $customer->email ?? '-' }}</li>
            </ul>
        </div>
    </div>

    {{-- جدول المبيعات --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-file-invoice-dollar me-2"></i>مبيعات العميل
            </h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>رقم الفاتورة</th>
                            <th>المستخدم</th>
                            <th>الإجمالي</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customer->sales as $sale)
                            <tr>
                                <td>{{ $sale->id }}</td>
                                <td>{{ $sale->invoice_number }}</td>
                                <td>{{ $sale->user->name ?? '-' }}</td>
                                <td>{{ number_format($sale->grand_total, 2) }}</td>
                                <td>
                                    <span class="badge
                                        @if($sale->status === 'paid') bg-success
                                        @elseif($sale->status === 'partial') bg-warning
                                        @else bg-danger @endif">
                                        {{ $sale->status }}
                                    </span>
                                </td>
                                <td>{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">لا توجد مبيعات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-truck me-2"></i>تفاصيل المورد
            </h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>الاسم:</strong> {{ $supplier->name }}</li>
                <li class="list-group-item"><strong>الهاتف:</strong> {{ $supplier->phone ?? '-' }}</li>
                <li class="list-group-item"><strong>البريد:</strong> {{ $supplier->email ?? '-' }}</li>
                <li class="list-group-item"><strong>العنوان:</strong> {{ $supplier->address ?? '-' }}</li>
            </ul>
        </div>
    </div>

    {{-- جدول المشتريات --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-file-invoice me-2"></i>مشتريات المورد
            </h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>رقم الفاتورة</th>
                            <th>المستخدم</th>
                            <th>الإجمالي</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($supplier->purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->id }}</td>
                                <td>{{ $purchase->invoice_number }}</td>
                                <td>{{ $purchase->user->name ?? '-' }}</td>
                                <td>{{ number_format($purchase->total, 2) }}</td>
                                <td>{{ $purchase->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">لا توجد مشتريات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

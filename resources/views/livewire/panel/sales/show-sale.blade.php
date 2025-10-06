<div class="container-fluid p-5">

    {{-- العنوان --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">فاتورة بيع <span dir="ltr">#{{ $sale->invoice_number }}</span></h3>
        <a href="{{ route('admin.panel.sales.list') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-2"></i> رجوع
        </a>
    </div>

    <div class="row">
        {{-- معلومات الفاتورة --}}
        <div class="col-md-4 mb-3">
            <div class="card shadow-lg h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">معلومات الفاتورة</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">الرقم:
                            <span dir="ltr">{{ $sale->invoice_number }}</span>
                        </li>
                        <li class="list-group-item">العميل: {{ $sale->customer->name ?? '---' }}</li>
                        <li class="list-group-item">المستخدم: {{ $sale->user->name ?? '---' }}</li>
                        <li class="list-group-item">الإجمالي:
                            <span class="badge badge-info" dir="ltr">
                                {{ number_format($sale->total, 2) }}
                            </span>
                        </li>
                        <li class="list-group-item">الخصم:
                            <span class="badge badge-warning"
                                dir="ltr">{{ number_format($sale->discount, 2) }}</span>
                        </li>
                        <li class="list-group-item">الصافي:
                            <span class="badge badge-success"
                                dir="ltr">{{ number_format($sale->grand_total, 2) }}</span>
                        </li>
                        <li class="list-group-item">الحالة:
                            <span
                                class="badge
                                @if ($sale->status == 'paid') badge-success
                                @elseif($sale->status == 'partial') badge-warning
                                @else badge-danger @endif">
                                {{ $sale->status }}
                            </span>
                        </li>
                        <li class="list-group-item">التاريخ:
                            <span dir="ltr">
                                {{ $sale->created_at->format('Y-m-d H:i') }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- تفاصيل العناصر --}}
        <div class="col-md-8 mb-3">
            <div class="card shadow-lg h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">تفاصيل العناصر</h5>
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
                                @forelse($sale->items as $item)
                                    <tr>
                                        <td><span dir="ltr">{{ $loop->iteration }}</span></td>
                                        <td><span dir="ltr">{{ $item->item->name }}</span></td>
                                        <td><span dir="ltr">{{ $item->quantity }}</span></td>
                                        <td><span dir="ltr">{{ number_format($item->price, 2) }}</span></td>
                                        <td><span dir="ltr">{{ number_format($item->subtotal, 2) }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">لا توجد عناصر</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

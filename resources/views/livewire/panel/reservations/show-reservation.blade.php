<div class="container-fluid p-5">
    {{-- العنوان --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="fw-bold">
                    <i class="fas fa-file-alt text-primary me-2"></i>
                    تفاصيل الحجز <span dir="ltr">#{{ $reservation->reservation_number }}</span>
                </h3>
                <a href="{{ route('admin.panel.reservations.list') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-2"></i> رجوع
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- معلومات الحجز --}}
        <div class="col-md-4">
            <div class="card border-primary shadow-lg h-100">
                <div class="card-header badge-primary text-dark">
                    <i class="fas fa-info-circle me-2"></i>معلومات الحجز
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">رقم الحجز: <span dir="ltr" class="fw-bold">{{ $reservation->reservation_number }}</span></li>
                        <li class="list-group-item">العميل: <span class="fw-bold">{{ $reservation->customer->name ?? '---' }}</span></li>
                        <li class="list-group-item">المستخدم: <span class="fw-bold">{{ $reservation->user->name ?? '---' }}</span></li>
                        <li class="list-group-item">الحالة:
                            @php
                                $badgeClass = match ($reservation->status) {
                                    'active' => 'badge-success',
                                    'completed' => 'badge-primary',
                                    'cancelled' => 'badge-danger',
                                    default => 'badge-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} text-dark px-2">{{ $reservation->status }}</span>
                        </li>
                        <li class="list-group-item">العربون:
                            <span class="badge badge-info text-dark px-2" dir="ltr">
                                {{ number_format($reservation->deposit, 2) }}
                            </span>
                        </li>
                        <li class="list-group-item">الخصم:
                            <span class="badge badge-secondary text-dark px-2" dir="ltr">
                                {{ number_format($reservation->discount, 2) }}
                            </span>
                        </li>
                        <li class="list-group-item">الإجمالي:
                            <span class="badge badge-warning text-dark px-2" dir="ltr">
                                {{ number_format($reservation->total, 2) }}
                            </span>
                        </li>
                        <li class="list-group-item">المتبقي:
                            <span class="badge badge-danger text-dark px-2" dir="ltr">
                                {{ number_format($reservation->remaining, 2) }}
                            </span>
                        </li>
                        <li class="list-group-item">من: <span dir="ltr">{{ $reservation->start_date }}</span></li>
                        <li class="list-group-item">إلى: <span dir="ltr">{{ $reservation->end_date }}</span></li>
                        <li class="list-group-item">الوصف: <span class="text-muted">{{ $reservation->description ?? '---' }}</span></li>
                        <li class="list-group-item">ملاحظات: <span class="text-muted">{{ $reservation->notes ?? '---' }}</span></li>
                        <li class="list-group-item">أنشئ بواسطة: <span class="fw-bold">{{ $reservation->createdBy->name ?? '---' }}</span></li>
                        <li class="list-group-item">آخر تعديل بواسطة: <span class="fw-bold">{{ $reservation->updatedBy->name ?? '---' }}</span></li>
                        <li class="list-group-item">تاريخ الإنشاء: <span dir="ltr">{{ $reservation->created_at->format('Y-m-d H:i') }}</span></li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- بنود الحجز --}}
        <div class="col-md-8">
            <div class="card border-secondary shadow-lg mb-4">
                <div class="card-header badge-secondary text-dark">
                    <i class="fas fa-list-ul me-2"></i>بنود الحجز
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>#</th>
                                    <th>المنتج</th>
                                    <th>الكمية</th>
                                    <th>السعر</th>
                                    <th>المجموع</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reservation->items as $item)
                                    <tr class="text-center">
                                        <td><span dir="ltr">{{ $loop->iteration }}</span></td>
                                        <td><span dir="ltr">{{ $item->item->name }}</span></td>
                                        <td><span dir="ltr">{{ $item->quantity }}</span></td>
                                        <td><span dir="ltr">{{ number_format($item->price, 2) }}</span></td>
                                        <td><span dir="ltr">{{ number_format($item->subtotal, 2) }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">لا توجد بنود</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- المدفوعات المرتبطة بالحجز --}}
            <div class="card border-success shadow-lg">
                <div class="card-header badge-success text-dark">
                    <i class="fas fa-money-check-alt me-2"></i>المدفوعات على الحجز
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>#</th>
                                    <th>المرجع</th>
                                    <th>المبلغ</th>
                                    <th>طريقة الدفع</th>
                                    <th>الحالة</th>
                                    <th>تاريخ الدفع</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalPaid = $reservation->payments->sum('amount') + $reservation->deposit;
                                @endphp
                                @forelse($reservation->payments as $payment)
                                    <tr class="text-center">
                                        <td><span dir="ltr">{{ $loop->iteration }}</span></td>
                                        <td><span dir="ltr">{{ $payment->payment_reference }}</span></td>
                                        <td><span dir="ltr">{{ number_format($payment->amount, 2) }}</span></td>
                                        <td><span dir="ltr">{{ ucfirst($payment->method) }}</span></td>
                                        <td>
                                            @php
                                                $status = $payment->paymentable->status ?? '---';
                                                $badgeClass = match ($status) {
                                                    'paid' => 'badge-success',
                                                    'partial' => 'badge-warning',
                                                    default => 'badge-danger',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }} text-dark px-2">{{ $status }}</span>
                                        </td>
                                        <td><span dir="ltr">{{ $payment->created_at->format('Y-m-d H:i') }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">لا توجد مدفوعات</td>
                                    </tr>
                                @endforelse
                                {{-- عرض العربون كبند مدفوع --}}
                                @if ($reservation->deposit > 0)
                                    <tr class="text-center">
                                        <td>--</td>
                                        <td>عربون</td>
                                        <td><span dir="ltr">{{ number_format($reservation->deposit, 2) }}</span></td>
                                        <td>---</td>
                                        <td><span class="badge badge-info text-dark px-2">مدفوع</span></td>
                                        <td><span dir="ltr">{{ $reservation->created_at->format('Y-m-d H:i') }}</span></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 text-end">
                        <span>إجمالي المدفوع حتى الآن: </span>
                        <strong class="text-success">{{ number_format($totalPaid, 2) }}</strong>
                        <span class="ms-3">المتبقي: </span>
                        <strong class="text-danger">{{ number_format(max($reservation->total - $totalPaid, 0), 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

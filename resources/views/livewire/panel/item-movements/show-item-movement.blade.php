<div class="container py-4">

    {{-- Header --}}
    <div class="card mb-4 shadow-lg">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">حركة المخزون
                    <span dir="ltr">#{{ $movement->id }}</span>
                    <small class="text-muted">({{ $movement->uuid }})</small>
                </h5>
                <div class="small text-muted">
                    <span>الصنف: <strong>{{ $movement->item->name ?? '-' }}</strong>
                        ({{ $movement->item->code ?? '-' }})</span>
                </div>
            </div>

            <div class="text-end">
                <span
                    class="badge
                    {{ $movement->movement_type === 'in' ? 'badge-success' : 'badge-danger' }}">
                    {{ $movement->movement_type === 'in' ? 'دخول' : 'خروج' }}
                </span>
                <div class="mt-2 small text-muted">الكمية: <strong>{{ $movement->quantity }}</strong></div>
            </div>
        </div>
    </div>

    {{-- Main details --}}
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h6 class="card-title">تفاصيل الحركة</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>السبب:</strong> {{ $movement->reason ?? '-' }}</li>
                        <li class="list-group-item"><strong>التاريخ:</strong>
                            <span dir="ltr">
                                {{ $movement->created_at->format('Y-m-d H:i') }}
                            </span>
                        </li>
                        <li class="list-group-item"><strong>تمت بواسطة:</strong> {{ $movement->createdBy->name ?? '-' }}
                        </li>
                        <li class="list-group-item"><strong>آخر تعديل:</strong>
                            <span dir="ltr">
                                {{ $movement->updated_at ? $movement->updated_at->format('Y-m-d H:i') : '-' }}
                            </span>
                        </li>
                        <li class="list-group-item"><strong>تزامن:</strong>
                            <span dir="ltr">
                                {{ $movement->synced_at ? $movement->synced_at->format('Y-m-d H:i') : 'غير متزامنة' }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Item snapshot --}}
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h6 class="card-title">معلومات الصنف</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>الكود:</strong> {{ $movement->item->code ?? '-' }}</li>
                        <li class="list-group-item"><strong>الاسم:</strong> {{ $movement->item->name ?? '-' }}</li>
                        <li class="list-group-item"><strong>الرصيد الحالي:</strong>
                            {{ $movement->item->quantity ?? 0 }}</li>
                        <li class="list-group-item"><strong>سعر الشراء:</strong>
                            <span dir="ltr">
                                {{ number_format($movement->item->purchase_price ?? 0, 2) }}
                            </span>
                        </li>
                        <li class="list-group-item"><strong>سعر البيع:</strong>
                            <span dir="ltr">
                                {{ number_format($movement->item->sale_price ?? 0, 2) }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Optional: if movement references a sale/purchase (see note) --}}
    @if (isset($movement->reference_type) && isset($movement->reference_id))
        <div class="card mt-3">
            <div class="card-body">
                <h6>معلومات مرجعية</h6>
                <p class="mb-0">هذا السجل مرتبط بـ <strong>{{ $movement->reference_type }}</strong> رقم:
                    <strong>{{ $movement->reference_id }}</strong>
                </p>
            </div>
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('admin.panel.item-movements.list') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>العودة لقائمة الحركات
        </a>
    </div>
</div>

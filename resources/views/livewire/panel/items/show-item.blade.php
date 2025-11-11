<div class="container p-5">
    <!-- Item Details -->
    <div class="card shadow-2-strong mb-4">
        <div class="card-body">
            <h4 class="mb-3"><i class="fas fa-box me-2"></i> {{ $item->name }}</h4>
            <p class="mb-1"><strong>الكود:</strong> {{ $item->code }}</p>
            <p class="mb-1"><strong>الوصف:</strong> {{ $item->description ?? '-' }}</p>
            <p class="mb-1"><strong>سعر الشراء:</strong> {{ number_format($item->purchase_price, 2) }}</p>
            <p class="mb-1"><strong>سعر البيع:</strong> {{ number_format($item->sale_price, 2) }}</p>
            <p class="mb-1"><strong>الرصيد الحالي:</strong> {{ $item->available_quantity }}</p>

        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3" id="itemTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="movements-tab" data-mdb-toggle="tab" data-mdb-target="#movements"
                type="button" role="tab">حركات المخزون</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sales-tab" data-mdb-toggle="tab" data-mdb-target="#sales" type="button"
                role="tab">المبيعات</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="purchases-tab" data-mdb-toggle="tab" data-mdb-target="#purchases"
                type="button" role="tab">المشتريات</button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Movements -->
        <div class="tab-pane fade show active" id="movements" role="tabpanel">
            <table class="table table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>التاريخ</th>
                        <th>الكمية</th>
                        <th>النوع</th>
                        <th>السبب</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item->movements as $move)
                        <tr>
                            <td>{{ $move->created_at->format('Y-m-d') }}</td>
                            <td>{{ $move->quantity }}</td>
                            <td>{{ $move->movement_type == 'in' ? 'دخول' : 'خروج' }}</td>
                            <td>{{ $move->reason }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Sales -->
        <div class="tab-pane fade" id="sales" role="tabpanel">
            <table class="table table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>التاريخ</th>
                        <th>العميل</th>
                        <th>الكمية</th>
                        <th>السعر</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item->saleItems as $saleItem)
                        <tr>
                            <td>{{ $saleItem->created_at->format('Y-m-d') }}</td>
                            <td>{{ $saleItem->sale->customer->name ?? '-' }}</td>
                            <td>{{ $saleItem->quantity }}</td>
                            <td>{{ number_format($saleItem->price, 2) }}</td>
                            <td>{{ number_format($saleItem->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Purchases -->
        <div class="tab-pane fade" id="purchases" role="tabpanel">
            <table class="table table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>التاريخ</th>
                        <th>المورد</th>
                        <th>الكمية</th>
                        <th>السعر</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item->purchaseItems as $purchaseItem)
                        <tr>
                            <td>{{ $purchaseItem->created_at->format('Y-m-d') }}</td>
                            <td>{{ $purchaseItem->purchase->supplier->name ?? '-' }}</td>
                            <td>{{ $purchaseItem->quantity }}</td>
                            <td>{{ number_format($purchaseItem->price, 2) }}</td>
                            <td>{{ number_format($purchaseItem->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

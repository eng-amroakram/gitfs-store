<div class="container-fluid p-5">
    <div class="row">

        @can('view-users', Auth::user())
            <!-- Users Count Card -->
            <div class="col-md-3 mb-4">
                <div class="card shadow-lg">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-users fa-2x text-primary me-3"></i>
                        <div>
                            <h6 class="card-title mb-1">الـمستخدمين</h6>
                            <p class="fw-bold mb-0">{{ $usersCount }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endcan

        <!-- Items Count Card -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-lg">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-gift fa-2x text-success me-3"></i>
                    <div>
                        <h6 class="card-title mb-1">عدد المنتجات</h6>
                        <p class="fw-bold mb-0">{{ $itemsCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reservations Count Card -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-lg">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-calendar-check fa-2x text-warning me-3"></i>
                    <div>
                        <h6 class="card-title mb-1">عدد الحجوزات</h6>
                        <p class="fw-bold mb-0">{{ $reservationsCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Count Card -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-lg">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-shopping-cart fa-2x text-danger me-3"></i>
                    <div>
                        <h6 class="card-title mb-1">عدد المبيعات</h6>
                        <p class="fw-bold mb-0">{{ $salesCount }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @can('view-financials', Auth::user())

        <!-- Second row: Amounts -->
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card shadow-lg">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-money-bill-wave fa-2x text-info me-3"></i>
                        <div>
                            <h6 class="card-title mb-1">إجمالي المبيعات</h6>
                            <p class="fw-bold mb-0">{{ number_format($totalSalesAmount, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card shadow-lg">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-credit-card fa-2x text-secondary me-3"></i>
                        <div>
                            <h6 class="card-title mb-1">إجمالي المدفوعات</h6>
                            <p class="fw-bold mb-0">{{ number_format($totalPaymentsAmount, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card shadow-lg">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-user-tie fa-2x text-dark me-3"></i>
                        <div>
                            <h6 class="card-title mb-1">رصيد العملاء (مبيعات)</h6>
                            <p class="fw-bold mb-0">{{ number_format($totalCustomersSaleBalance, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card shadow-lg">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-user-clock fa-2x text-primary me-3"></i>
                        <div>
                            <h6 class="card-title mb-1">رصيد العملاء (حجوزات)</h6>
                            <p class="fw-bold mb-0">{{ number_format($totalCustomersReservationBalance, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Third row: Quantities -->
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card shadow-lg">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-boxes fa-2x text-success me-3"></i>
                        <div>
                            <h6 class="card-title mb-1">إجمالي الكمية</h6>
                            <p class="fw-bold mb-0">{{ $totalItemsQuantity }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card shadow-lg">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-box-open fa-2x text-info me-3"></i>
                        <div>
                            <h6 class="card-title mb-1">الكمية المتاحة</h6>
                            <p class="fw-bold mb-0">{{ $totalItemsAvailableQuantity }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card shadow-lg">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-box fa-2x text-warning me-3"></i>
                        <div>
                            <h6 class="card-title mb-1">الكمية المحجوزة</h6>
                            <p class="fw-bold mb-0">{{ $totalItemsReservedQuantity }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card shadow-lg">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-balance-scale fa-2x text-secondary me-3"></i>
                        <div>
                            <h6 class="card-title mb-1">إجمالي رصيد العملاء</h6>
                            <p class="fw-bold mb-0">{{ number_format($totalCustomersTotalBalance, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Entities Section -->
        <div class="row mt-4">
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">أحدث المستخدمين</div>
                    <ul class="list-group list-group-flush">
                        @foreach ($recentUsers as $user)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $user->name }}
                                <span class="badge badge-primary">{{ $user->created_at->format('Y-m-d') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">أحدث المنتجات</div>
                    <ul class="list-group list-group-flush">
                        @foreach ($recentItems as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $item->name }}
                                <span class="badge badge-success">{{ $item->created_at->format('Y-m-d') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-warning text-white">أحدث الحجوزات</div>
                    <ul class="list-group list-group-flush">
                        @foreach ($recentReservations as $reservation)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $reservation->id }}
                                <span class="badge badge-warning">{{ $reservation->created_at->format('Y-m-d') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-danger text-white">أحدث المبيعات</div>
                    <ul class="list-group list-group-flush">
                        @foreach ($recentSales as $sale)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $sale->id }}
                                <span class="badge badge-danger">{{ $sale->created_at->format('Y-m-d') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Top Entities Section -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">الأكثر مبيعاً</div>
                    <ul class="list-group list-group-flush">
                        @foreach ($topSellingItems as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $item->name }}
                                <span class="badge badge-info">{{ $item->sale_items_sum_quantity }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-secondary text-white">أفضل العملاء (مبيعات)</div>
                    <ul class="list-group list-group-flush">
                        @foreach ($topCustomersBySales as $customer)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $customer->name }}
                                <span
                                    class="badge badge-secondary">{{ number_format($customer->sales_sum_grand_total, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white">أعلى العملاء (رصيد المبيعات)</div>
                    <ul class="list-group list-group-flush">
                        @foreach ($topCustomersBySaleBalance as $customer)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $customer->name }}
                                <span class="badge badge-secondary">{{ number_format($customer->sale_balance, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">أعلى العملاء (رصيد الحجوزات)</div>
                    <ul class="list-group list-group-flush">
                        @foreach ($topCustomersByReservationBalance as $customer)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $customer->name }}
                                <span
                                    class="badge badge-primary">{{ number_format($customer->reservation_balance, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">أعلى العملاء (إجمالي الرصيد)</div>
                    <ul class="list-group list-group-flush">
                        @foreach ($topCustomersByTotalBalance as $customer)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $customer->name }}
                                <span class="badge badge-success">{{ number_format($customer->total_balance, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endcan

</div>

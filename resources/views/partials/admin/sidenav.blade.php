<div id="sidenav-6" class="sidenav sidenav-sm sidenav-right" data-mdb-accordion="true" data-mdb-hidden="false"
    data-scroll-container="#scrollContainer" data-mdb-mode="side" role="navigation" data-mdb-right="false"
    data-mdb-color="light" style="background-color: #2d2c2c">

    <a class="ripple d-flex justify-content-center py-4 mb-3" style="padding-top: 4rem !important;"
        href="{{ route('admin.panel.dashboard', ['lang' => app()->getLocale()]) }}" data-mdb-ripple-color="primary">
        <img id="YassinERP-Logo" width="200" src="{{ asset('assets/mdb/marta-szymanska/images/login.png') }}"
            alt="YassinERP-Logo" draggable="false" class="img-fluid rounded" />
    </a>

    @php
        $locale = app()->getLocale() === 'en' ? true : false;
    @endphp

    <div id="scrollContainer">
        <ul class="sidenav-menu px-2 pb-5" style="max-height: 70vh; overflow-y: auto;">

            <!-- Dashboard -->
            @can('view-dashboard', Auth::user())
                <li class="sidenav-item">
                    <a class="sidenav-link" href="{{ route('admin.panel.dashboard', ['lang' => app()->getLocale()]) }}">
                        <i class="fas fa-home fa-fw {{ $locale ? 'me-3' : 'me-3' }}"></i>
                        <span>{{ __('Overview') }}</span>
                    </a>
                </li>
            @endcan

            <!-- Cashier -->
            @can('view-cashier', Auth::user())
                <li class="sidenav-item">
                    <a class="sidenav-link" href="{{ route('admin.panel.cashier', ['lang' => app()->getLocale()]) }}">
                        <i class="fas fa-cash-register fa-fw {{ $locale ? 'me-3' : 'me-3' }}"></i>
                        <span>{{ __('Cashier') }}</span>
                    </a>
                </li>
            @endcan

            <hr />

            <!-- Users -->
            @can('view-users', Auth::user())
                <li class="sidenav-item">
                    <a class="sidenav-link">
                        <i class="fas fa-users fa-fw {{ $locale ? 'me-3' : 'me-3' }}"></i>
                        <span>{{ __('Users') }}</span>
                    </a>
                    <ul class="sidenav-collapse">
                        <li class="sidenav-item">
                            <a class="sidenav-link"
                                href="{{ route('admin.panel.users.list', ['lang' => app()->getLocale()]) }}">
                                <i class="fas fa-user fa-fw {{ $locale ? 'me-2' : 'me-2' }}"></i>
                                <span>{{ __('Users') }}</span>
                            </a>
                        </li>
                        {{-- Roles (اختياري لاحقاً) --}}
                        {{-- <li class="sidenav-item">
                    <a class="sidenav-link"
                        href="{{ route('admin.panel.roles.list', ['lang' => app()->getLocale()]) }}">
                        <i class="fas fa-user-shield fa-fw {{ $locale ? 'me-2' : 'me-2' }}"></i>
                        <span>{{ __('Roles') }}</span>
                    </a>
                </li> --}}
                    </ul>
                </li>
            @endcan

            <!-- Customers -->
            @can('view-customers', Auth::user())
                <li class="sidenav-item">
                    <a class="sidenav-link">
                        <i class="fas fa-user-tie fa-fw {{ $locale ? 'me-3' : 'me-3' }}"></i>
                        <span>{{ __('Customers') }}</span>
                    </a>
                    <ul class="sidenav-collapse">
                        <li class="sidenav-item">
                            <a class="sidenav-link"
                                href="{{ route('admin.panel.customers.list', ['lang' => app()->getLocale()]) }}">
                                <i class="fas fa-address-book fa-fw {{ $locale ? 'me-2' : 'me-2' }}"></i>
                                <span>{{ __('Customers') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            <!-- Suppliers -->
            @can('view-suppliers', Auth::user())
                <li class="sidenav-item">
                    <a class="sidenav-link">
                        <i class="fas fa-truck fa-fw {{ $locale ? 'me-3' : 'me-3' }}"></i>
                        <span>{{ __('Suppliers') }}</span>
                    </a>
                    <ul class="sidenav-collapse">
                        <li class="sidenav-item">
                            <a class="sidenav-link"
                                href="{{ route('admin.panel.suppliers.list', ['lang' => app()->getLocale()]) }}">
                                <i class="fas fa-truck-loading fa-fw {{ $locale ? 'me-2' : 'me-2' }}"></i>
                                <span>{{ __('Suppliers') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            <!-- Stock Management -->
            @can('view-stock', Auth::user())
                <li class="sidenav-item">
                    <a class="sidenav-link">
                        <i class="fas fa-boxes-stacked fa-fw {{ $locale ? 'me-3' : 'me-3' }}"></i>
                        <span>{{ __('Stock Management') }}</span>
                    </a>
                    <ul class="sidenav-collapse">
                        <li class="sidenav-item">
                            <a class="sidenav-link"
                                href="{{ route('admin.panel.items.list', ['lang' => app()->getLocale()]) }}">
                                <i class="fas fa-box fa-fw {{ $locale ? 'me-2' : 'me-2' }}"></i>
                                <span>{{ __('Items') }}</span>
                            </a>
                        </li>
                        <li class="sidenav-item">
                            <a class="sidenav-link"
                                href="{{ route('admin.panel.item-movements.list', ['lang' => app()->getLocale()]) }}">
                                <i class="fas fa-exchange-alt fa-fw {{ $locale ? 'me-2' : 'me-2' }}"></i>
                                <span>{{ __('Item Movements') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            <!-- Sales -->
            @can('view-sales', Auth::user())
                <li class="sidenav-item">
                    <a class="sidenav-link">
                        <i class="fas fa-cash-register fa-fw {{ $locale ? 'me-3' : 'me-3' }}"></i>
                        <span>{{ __('Sales') }}</span>
                    </a>
                    <ul class="sidenav-collapse">
                        <li class="sidenav-item">
                            <a class="sidenav-link"
                                href="{{ route('admin.panel.sales.list', ['lang' => app()->getLocale()]) }}">
                                <i class="fas fa-shopping-cart fa-fw {{ $locale ? 'me-2' : 'me-2' }}"></i>
                                <span>{{ __('Sales') }}</span>
                            </a>
                        </li>
                        {{-- <li class="sidenav-item">
                    <a class="sidenav-link"
                        href="{{ route('admin.panel.returns.list', ['lang' => app()->getLocale()]) }}">
                        <i class="fas fa-undo fa-fw {{ $locale ? 'me-2' : 'me-2' }}"></i>
                        <span>{{ __('Returns') }}</span>
                    </a>
                </li> --}}
                    </ul>
                </li>
            @endcan

            <!-- Purchases -->
            @can('view-purchases', Auth::user())
                <li class="sidenav-item">
                    <a class="sidenav-link">
                        <i class="fas fa-shopping-bag fa-fw {{ $locale ? 'me-3' : 'me-3' }}"></i>
                        <span>{{ __('Purchases') }}</span>
                    </a>
                    <ul class="sidenav-collapse">
                        <li class="sidenav-item">
                            <a class="sidenav-link"
                                href="{{ route('admin.panel.purchases.list', ['lang' => app()->getLocale()]) }}">
                                <i class="fas fa-receipt fa-fw {{ $locale ? 'me-2' : 'me-2' }}"></i>
                                <span>{{ __('Purchases') }}</span>
                            </a>
                        </li>
                        {{-- <li class="sidenav-item">
                    <a class="sidenav-link"
                        href="{{ route('admin.panel.purchase-returns.list', ['lang' => app()->getLocale()]) }}">
                        <i class="fas fa-undo fa-fw {{ $locale ? 'me-2' : 'me-2' }}"></i>
                        <span>{{ __('Purchase Returns') }}</span>
                    </a>
                </li> --}}
                    </ul>
                </li>
            @endcan

            <!-- Reservations -->
            @can('view-reservations', Auth::user())
                <li class="sidenav-item">
                    <a class="sidenav-link">
                        <i class="fas fa-calendar-check fa-fw {{ $locale ? 'me-3' : 'me-3' }}"></i>
                        <span>{{ __('Reservations') }}</span>
                    </a>
                    <ul class="sidenav-collapse">
                        <li class="sidenav-item">
                            <a class="sidenav-link"
                                href="{{ route('admin.panel.reservations.list', ['lang' => app()->getLocale()]) }}">
                                <i class="fas fa-calendar fa-fw {{ $locale ? 'me-2' : 'me-2' }}"></i>
                                <span>{{ __('Reservations') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            <!-- Payments -->
            @can('view-payments', Auth::user())
                <li class="sidenav-item">
                    <a class="sidenav-link">
                        <i class="fas fa-credit-card fa-fw {{ $locale ? 'me-3' : 'me-3' }}"></i>
                        <span>{{ __('Payments') }}</span>
                    </a>
                    <ul class="sidenav-collapse">
                        <li class="sidenav-item">
                            <a class="sidenav-link"
                                href="{{ route('admin.panel.payments.list', ['lang' => app()->getLocale()]) }}">
                                <i class="fas fa-money-bill-wave fa-fw {{ $locale ? 'me-2' : 'me-2' }}"></i>
                                <span>{{ __('Payments') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan
            <hr />
            <!-- Reports -->
            @can('view-reports', Auth::user())
                <li class="sidenav-item">
                    <a class="sidenav-link">
                        <i class="fas fa-chart-line fa-fw {{ $locale ? 'me-3' : 'me-3' }}"></i>
                        <span>{{ __('Reports') }}</span>
                    </a>
                    <ul class="sidenav-collapse">
                        <li class="sidenav-item">
                            <a class="sidenav-link" href="#">
                                <i class="fas fa-file-invoice-dollar fa-fw {{ $locale ? 'me-2' : 'me-2' }}"></i>
                                <span>{{ __('Sales Report') }}</span>
                            </a>
                        </li>
                        <li class="sidenav-item">
                            <a class="sidenav-link" href="#">
                                <i class="fas fa-file-invoice fa-fw {{ $locale ? 'me-2' : 'me-2' }}"></i>
                                <span>{{ __('Purchases Report') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

        </ul>
    </div>

    <div class="sidenav-footer position-absolute w-100 bottom-0 text-center mb-2">
        <a href="#" class="btn btn-primary btn-sm w-75 mb-2">{{ __('Settings') }}</a>

        <a href="{{ route('admin.panel.logout', ['lang' => app()->getLocale()]) }}"
            class="btn btn-danger btn-sm w-75">{{ __('Logout') }}</a>
    </div>
</div>

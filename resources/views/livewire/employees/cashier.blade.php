<div class="container p-2 pb-5 px-0" style="overflow-y: auto; height: 100vh;">

    <!-- Header -->
    <div class="pt-5 bg-body-tertiary mb-4">
        <h1 class="fw-bold">{{ __('Cashier - Sale & Reservation') }}</h1>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="cashierTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link @if ($type === 'sale') active @endif" id="sale-tab"
                data-mdb-toggle="tab" data-mdb-target="#tab-sale" type="button" role="tab"
                wire:click="$set('type','sale')">
                <i class="fas fa-cash-register me-2"></i>{{ __('Sale') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link @if ($type === 'reservation') active @endif" id="reservation-tab"
                data-mdb-toggle="tab" data-mdb-target="#tab-reservation" type="button" role="tab"
                wire:click="$set('type','reservation')">
                <i class="fas fa-calendar-check me-2"></i>{{ __('Reservation') }}
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="cashierTabsContent">

        <!-- Sale Tab -->
        <div class="tab-pane fade @if ($type === 'sale') show active @endif" id="tab-sale" role="tabpanel">

            @include('livewire.employees.partials.cashier-form', ['transactionType' => 'sale'])

        </div>

        <!-- Reservation Tab -->
        <div class="tab-pane fade @if ($type === 'reservation') show active @endif" id="tab-reservation"
            role="tabpanel">

            @include('livewire.employees.partials.cashier-form', ['transactionType' => 'reservation'])

        </div>
    </div>

</div>

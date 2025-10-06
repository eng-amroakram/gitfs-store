<div class="container p-2 pb-5 px-0">

    <!-- Heading -->
    <div class="pt-5 bg-body-tertiary mb-4">
        <h1>{{ __('Create Payment') }}</h1>
        <nav class="d-flex">
            <h6 class="mb-0">
                <a href="{{ route('admin.panel.dashboard', ['lang' => app()->getLocale()]) }}"
                    class="text-reset">{{ __('Home') }}</a>
                <span>/</span>
                <a href="{{ route('admin.panel.payments.list', ['lang' => app()->getLocale()]) }}"
                    class="text-reset">{{ __('Payments') }}</a>
                <span>/</span>
                <u>{{ __('Create') }}</u>
            </h6>
        </nav>
    </div>
    <!-- Heading -->

    <!-- Form Card -->
    <div class="card shadow-lg rounded-3">
        <div class="card-body">
            <form wire:submit.prevent="create">

                <div class="row mb-4">

                    <!-- Payment Reference -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="payment_reference">{{ __('Payment Reference') }}</label>
                        <input type="text" id="payment_reference" wire:model.defer="payment_reference" class="form-control" disabled>
                        @error('payment_reference')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Paymentable Type -->
                    <div class="col-md-6 mb-4" wire:ignore>
                        <label class="form-label" for="paymentable_type">{{ __('Payment For') }}</label>
                        <select id="paymentable_type" class="mdb-select" x-data x-init="initSelect($el, 0)"
                            wire:model.live="paymentable_type">
                            <option value="">{{ __('Select Type') }}</option>
                            <option value="sale">{{ __('Sale') }}</option>
                            <option value="reservation">{{ __('Reservation') }}</option>
                        </select>
                        @error('paymentable_type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Paymentable ID -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="paymentable_id">{{ __('Select Record') }}</label>
                        <select id="paymentable_id" class="mdb-select" x-data x-init="initSelect($el, 1)"
                            wire:model.live="paymentable_id" wire:ignore.self data-mdb-filter="true">
                            <option value="">{{ __('Select') }}</option>
                            @if ($paymentable_type === 'sale')
                                @foreach ($allSales as $sale)
                                    <option value="{{ $sale->id }}" dir="ltr">
                                        ({{ $sale->invoice_number }})
                                        - ({{ $sale->customer?->name }}) -
                                        ({{ number_format($sale->grand_total, 2) }})
                                    </option>
                                @endforeach
                            @elseif ($paymentable_type === 'reservation')
                                @foreach ($allReservations as $res)
                                    <option value="{{ $res->id }}" dir="ltr">
                                        ({{ $res->customer?->name }})
                                        - ({{ number_format($res->total, 2) }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('paymentable_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <!-- Details Card -->
                @if ($selectedRecord)
                    <div class="card border shadow-sm mb-4">
                        <div class="card-body bg-light">
                            <h5 class="card-title mb-3">{{ __('Details') }}</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>{{ __('Total Amount') }}</span>
                                    <strong>{{ number_format($selectedRecord->total ?? $selectedRecord->grand_total, 2) }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>{{ __('Paid So Far') }}</span>
                                    <span
                                        class="text-success">{{ number_format($selectedRecord->paid_amount, 2) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>{{ __('Remaining') }}</span>
                                    <span class="text-danger fw-bold">
                                        {{ number_format(($selectedRecord->total ?? $selectedRecord->grand_total) - $selectedRecord->paid_amount, 2) }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Amount & Method -->
                <div class="row mb-4">
                    <!-- Amount -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="amount">{{ __('Payment Amount') }}</label>
                        <input type="number" min="0" step="0.01" wire:model.live="amount"
                            class="form-control">
                        @error('amount')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        @if ($validation_message)
                            <span class="text-danger">{{ $validation_message }}</span>
                        @endif
                    </div>

                    <!-- Method -->
                    <div class="col-md-6 mb-4" wire:ignore>
                        <label class="form-label" for="method">{{ __('Payment Method') }}</label>
                        <select id="method" class="mdb-select" x-data x-init="initSelect($el, 2)"
                            wire:model.defer="method">
                            <option value="cash">{{ __('Cash') }}</option>
                            <option value="card">{{ __('Card') }}</option>
                            <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
                            <option value="palpay">{{ __('Palpay') }}</option>
                            <option value="jawwalPay">{{ __('JawwalPay') }}</option>
                            <option value="other">{{ __('Other') }}</option>
                        </select>
                        @error('method')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-4">
                    <label class="form-label" for="notes">{{ __('Notes') }}</label>
                    <textarea id="notes" wire:model.defer="notes" class="form-control" rows="3"
                        placeholder="{{ __('Enter any additional notes here...') }}"></textarea>
                    @error('notes')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="create">
                            <i class="fas fa-plus"></i> {{ __('Create Payment') }}
                        </span>
                        <span wire:loading wire:target="create">
                            <i class="fas fa-spinner fa-spin me-2"></i>
                        </span>
                    </button>
                    <a href="{{ route('admin.panel.payments.list', ['lang' => app()->getLocale()]) }}"
                        class="btn btn-secondary">
                        <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i>
                        {{ __('Back') }}
                    </a>
                </div>

            </form>
        </div>
    </div>
    <!-- End Form Card -->

    <script>
        function initSelect(el, index) {
            const select = new mdb.Select(el);
            el.addEventListener('change', () => {
                Livewire.emit('selectUpdated', index, el.value);
            });
        }
    </script>
</div>

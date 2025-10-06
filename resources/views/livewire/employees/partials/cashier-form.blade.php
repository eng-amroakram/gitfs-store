<form wire:submit.prevent="save">
    <div class="row mb-4">

        @if ($transactionType === 'sale')
            <!-- Invoice Number -->
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('Invoice Number') }}</label>
                <input type="text" class="form-control" value="{{ $invoice_number }}" disabled>
                @error('invoice_number')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        @endif

        <!-- Reservation Number -->
        @if ($transactionType === 'reservation')
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('Reservation Number') }}</label>
                <input type="text" class="form-control" value="{{ $reservation_number }}" disabled>
                @error('reservation_number')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        @endif

        <!-- Customer -->
        <div class="col-md-6 mb-3">
            <label class="form-label">{{ __('Customer') }}</label>
            <input type="text" class="form-control" value="{{ $customer->name }}" disabled>
            @error('customer_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        @if ($transactionType === 'reservation')
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('Start Date') }}</label>
                <input type="date" class="form-control" wire:model="start_date">
                @error('start_date')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('End Date') }}</label>
                <input type="date" class="form-control" wire:model="end_date">
                @error('end_date')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        @endif
        {{-- @if ($transactionType === 'reservation')
            <div class="col-md-4 mb-3" wire:ignore>
                <label class="form-label">{{ __('Reservation Status') }}</label>
                <select class="select" wire:model="reservation_status">
                    <option value="active">{{ __('Active') }}</option>
                    <option value="completed">{{ __('Completed') }}</option>
                    <option value="cancelled">{{ __('Cancelled') }}</option>
                </select>
            </div>
        @endif --}}
    </div>

    <!-- Items Table -->
    <div class="table-responsive mb-4">
        <table class="table table-bordered align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Item') }}</th>
                    <th>{{ __('Quantity') }}</th>
                    <th>{{ __('Price') }}</th>
                    <th>{{ __('Total') }}</th>
                    <th>
                        <button type="button" class="btn btn-success btn-sm" wire:click="addItem">
                            <i class="fas fa-plus"></i>
                        </button>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $index => $it)
                    <tr>
                        <td>

                            @if ($transactionType === 'reservation')
                                <div>
                                    <select class="mdb-select" x-data x-init="initSelect($el, {{ $index }})"
                                        wire:model.live="items.{{ $index }}.item_id">
                                        <option value="">{{ __('Select an item') }}</option>
                                        @foreach ($allRentalItems as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->name . " ($item->available_quantity)" }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('items.' . $index . '.item_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            @endif

                            @if ($transactionType === 'sale')
                                <div>
                                    <select class="mdb-select" x-data x-init="initSelect($el, {{ $index }})"
                                        wire:model.live="items.{{ $index }}.item_id">
                                        <option value="">{{ __('Select an item') }}</option>
                                        @foreach ($allSaleItems as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->name . " ($item->quantity_total)" }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('items.' . $index . '.item_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            @endif

                        </td>
                        <td>
                            <input type="number" min="1" wire:model.live="items.{{ $index }}.quantity"
                                class="form-control text-center">
                            @error('items.' . $index . '.quantity')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </td>

                        <td>
                            <div wire:ignore>
                                <div class="form-outline" data-mdb-input-init>
                                    <input type="number" min="0" step="0.01"
                                        wire:model.live="items.{{ $index }}.price"
                                        class="form-control form-icon-trailing">
                                    <span class="trailing">₪</span>
                                </div>
                            </div>
                            @error('items.' . $index . '.price')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror

                        </td>
                        <td>
                            <input type="number" class="form-control text-center"
                                value="{{ $it['quantity'] * $it['price'] }}" disabled>
                            @error('items.' . $index . '.subtotal')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm"
                                wire:click="removeItem({{ $index }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Totals -->
    <div class="row mb-4">
        <div class="col-md-2 mb-3">
            <label class="form-label">{{ __('Discount') }}</label>
            <div class="form-outline" data-mdb-input-init wire:ignore>
                <input type="number" min="0" step="0.01" wire:model.live="discount"
                    class="form-control text-center">
                <span class="trailing">₪</span>
            </div>
        </div>

        @if ($transactionType === 'reservation')
            <div class="col-md-2 mb-3">
                <label class="form-label">{{ __('Deposit') }}</label>
                <div class="form-outline" data-mdb-input-init wire:ignore>
                    <input type="number" min="0" step="0.01" wire:model.live="deposit"
                        class="form-control text-center">
                    <span class="trailing">₪</span>
                </div>
            </div>
        @endif

        <div class="col-md-2 mb-3" wire:ignore>
            <label class="form-label">{{ __('Total') }}</label>
            <div class="form-outline" data-mdb-input-init>
                <input type="number" class="form-control text-center" wire:model.live="total" disabled>
                <span class="trailing">₪</span>
            </div>
        </div>
        <div class="col-md-3 mb-3" wire:ignore>
            <label class="form-label">{{ __('Net Total') }}</label>
            <div class="form-outline" data-mdb-input-init>
                <input type="number" class="form-control text-center" wire:model.live="grand_total" disabled>
                <span class="trailing">₪</span>
            </div>
        </div>
        <div class="col-md-3 mb-3" wire:ignore>
            <label class="form-label">{{ __('Amount Paid') }}</label>
            <div class="form-outline" data-mdb-input-init>
                <input type="number" min="0" step="0.01" wire:model.live="amount_paid"
                    class="form-control text-center">
                <span class="trailing">₪</span>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="d-flex justify-content-end gap-2">
        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="save">
                <i class="fas fa-check-circle"></i>
                {{ $transactionType === 'sale' ? __('Save Sale') : __('Save Reservation') }}
            </span>
            <span wire:loading wire:target="save">
                <i class="fas fa-spinner fa-spin"></i>
            </span>
        </button>
    </div>
</form>

<!-- MDB Select initialization -->
<script>
    function initSelect(el, index) {
        const select = new mdb.Select(el);
        el.addEventListener('change', () => {
            Livewire.emit('selectUpdated', index, el.value);
        });
    }
</script>

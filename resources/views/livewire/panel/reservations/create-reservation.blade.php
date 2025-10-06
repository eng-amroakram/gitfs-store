<div class="container p-2 pb-5 px-0">

    <!-- Heading -->
    <div class="pt-5 bg-body-tertiary mb-4">
        <h1>{{ __('Create Reservation') }}</h1>
        <nav class="d-flex">
            <h6 class="mb-0">
                <a href="{{ route('admin.panel.dashboard', ['lang' => app()->getLocale()]) }}"
                    class="text-reset">{{ __('Home') }}</a>
                <span>/</span>
                <a href="{{ route('admin.panel.reservations.list', ['lang' => app()->getLocale()]) }}"
                    class="text-reset">{{ __('Reservations') }}</a>
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

                <div class="row">

                    <!-- Reservation Number -->
                    <div class="col-md-4 mb-4">
                        <label class="form-label" for="reservation_number">{{ __('Reservation Number') }}</label>
                        <input type="text" id="reservation_number" wire:model.defer="reservation_number"
                            class="form-control" disabled>
                        @error('reservation_number')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Customer -->
                    <div class="col-md-4 mb-4">
                        <div wire:ignore>
                            <label class="form-label" for="customer">{{ __('Customer') }}</label>
                            <select id="customer" class="mdb-select" x-data x-init="initSelect($el, 'customer')"
                                wire:model.defer="customer_id">
                                <option value="">{{ __('Select Customer') }}</option>
                                @foreach ($allCustomers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('customer_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Start Date -->
                    <div class="col-md-4 mb-4">
                        <label class="form-label" for="start_date">{{ __('Start Date') }}</label>
                        <input type="date" id="start_date" wire:model.defer="start_date" class="form-control">
                        @error('start_date')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div class="col-md-4 mb-4">
                        <label class="form-label" for="end_date">{{ __('End Date') }}</label>
                        <input type="date" id="end_date" wire:model.defer="end_date" class="form-control">
                        @error('end_date')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <!-- Items Table -->
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center mb-4">
                        <thead>
                            <tr>
                                <th>{{ __('Item') }}</th>
                                <th>{{ __('Quantity') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Subtotal') }}</th>
                                <th>
                                    <button type="button" class="btn btn-success btn-sm" wire:click="addItem">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $index => $item)
                                <tr>
                                    <!-- Item Select -->
                                    <td>
                                        <div wire:ignore>
                                            <select class="mdb-select" x-data x-init="initSelect($el, {{ $index }})"
                                                wire:model.live.debounce.500ms="items.{{ $index }}.item_id">
                                                <option value="">{{ __('Select Item') }}</option>
                                                @foreach ($allItems as $i)
                                                    <option value="{{ $i->id }}">{{ $i->name }}
                                                        ({{ $i->available_quantity }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error("items.$index.item_id")
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </td>

                                    <!-- Quantity -->
                                    <td>
                                        <input type="number" min="1"
                                            wire:model.live.debounce.500ms="items.{{ $index }}.quantity"
                                            class="form-control">
                                        @error("items.$index.quantity")
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </td>

                                    <!-- Price -->
                                    <td>
                                        <input type="number" min="0" step="0.01"
                                            wire:model.live.debounce.500ms="items.{{ $index }}.price"
                                            class="form-control">
                                        @error("items.$index.price")
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </td>

                                    <!-- Subtotal -->
                                    <td>
                                        <input type="number" class="form-control"
                                            wire:model="items.{{ $index }}.subtotal" disabled>
                                    </td>

                                    <!-- Remove -->
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm"
                                            wire:click="removeItem({{ $index }})">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Deposit & Totals -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="deposit">{{ __('Deposit') }}</label>
                        <input type="number" min="0" step="0.01" wire:model.live.debounce.500ms="deposit"
                            class="form-control">
                        @error('deposit')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('Total') }}</label>
                        <input type="number" class="form-control" value="{{ collect($items)->sum('subtotal') }}"
                            disabled>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('Remaining') }}</label>
                        <input type="number" class="form-control"
                            value="{{ collect($items)->sum('subtotal') - $deposit }}" disabled>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label class="form-label" for="description">{{ __('Description') }}</label>
                    <textarea id="description" wire:model.defer="description" class="form-control" rows="3"></textarea>
                    @error('description')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label" for="notes">{{ __('Notes') }}</label>
                    <textarea id="notes" wire:model.defer="notes" class="form-control" rows="3"></textarea>
                    @error('notes')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="create">
                            <i class="fas fa-plus"></i> {{ __('Create') }}
                        </span>
                        <span wire:loading wire:target="create">
                            <i class="fas fa-spinner fa-spin me-2"></i>
                        </span>
                    </button>
                    <a href="{{ route('admin.panel.reservations.list', ['lang' => app()->getLocale()]) }}"
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
        function initSelect(el, key) {
            const select = new mdb.Select(el);
            el.addEventListener('change', () => {
                if (key === 'customer') {
                    Livewire.emit('customerUpdated', el.value);
                } else {
                    Livewire.emit('selectUpdated', key, el.value);
                }
            });
        }
    </script>
</div>

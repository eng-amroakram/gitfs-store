<div class="container p-2 pb-5 px-0">

    <!-- Heading -->
    <div class="pt-5 bg-body-tertiary mb-4">
        <h1>{{ __('Edit Purchase') }}</h1>
        <nav class="d-flex">
            <h6 class="mb-0">
                <a href="{{ route('admin.panel.dashboard', ['lang' => app()->getLocale()]) }}"
                    class="text-reset">{{ __('Home') }}</a>
                <span>/</span>
                <a href="{{ route('admin.panel.purchases.list', ['lang' => app()->getLocale()]) }}"
                    class="text-reset">{{ __('Purchases') }}</a>
                <span>/</span>
                <u>{{ __('Edit') }}</u>
            </h6>
        </nav>
    </div>
    <!-- Heading -->

    <!-- Form Card -->
    <div class="card shadow-lg rounded-3">
        <div class="card-body">
            <form wire:submit.prevent="update">

                <div class="row">

                    <!-- Invoice Number -->
                    <div class="col-md-4 mb-4">
                        <label class="form-label" for="invoice_number">{{ __('Invoice Number') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-file-invoice"></i></span>
                            <input type="text" id="invoice_number" wire:model.defer="invoice_number"
                                class="form-control" dir="ltr" disabled>
                        </div>
                        @error('invoice_number')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Supplier -->
                    <div class="col-md-4 mb-4">
                        <div wire:ignore>
                            <label class="form-label" for="supplier">{{ __('Supplier') }}</label>
                            <select id="supplier" class="mdb-select" x-data x-init="initSelect($el, 0)"
                                wire:model.defer="supplier_id">
                                <option value="">{{ __('Select Supplier') }}</option>
                                @foreach ($allSuppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('supplier_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- <!-- User (readonly) -->
                    <div class="col-md-4 mb-4">
                        <label class="form-label" for="user">{{ __('User') }}</label>
                        <input type="text" id="user" class="form-control" value="{{ $userName ?? '' }}"
                            disabled>
                    </div> --}}

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

                <!-- Totals -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('Total') }}</label>
                        <input type="number" class="form-control" value="{{ collect($items)->sum('subtotal') }}"
                            disabled>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('Grand Total') }}</label>
                        <input type="number" class="form-control" value="{{ collect($items)->sum('subtotal') }}"
                            disabled>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="update">
                            <i class="fas fa-save"></i> {{ __('Update') }}
                        </span>
                        <span wire:loading wire:target="update">
                            <i class="fas fa-spinner fa-spin me-2"></i>
                        </span>
                    </button>
                    <a href="{{ route('admin.panel.purchases.list', ['lang' => app()->getLocale()]) }}"
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

@push('scripts')
    <script>
        $(document).ready(function() {
            var $supplier = "{{ $supplier_id }}" + "";
            var $items = @this.items;

            if ($supplier) {
                const $supplierSelector = document.querySelector("#supplier");
                const $supplierSelectInstance = mdb.Select.getInstance($supplierSelector);
                $supplierSelectInstance.setValue($supplier);
            }

            $items.forEach((item, index) => {
                if (item.item_id) {
                    const itemSelector = document.querySelectorAll(".mdb-select")[index + 1];
                    const itemSelectInstance = mdb.Select.getInstance(itemSelector);
                    itemSelectInstance.setValue(item.item_id);
                }
            });
        });
    </script>
@endpush

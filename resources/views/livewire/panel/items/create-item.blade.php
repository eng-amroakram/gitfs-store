<div class="container p-2 pb-5 px-0">

    <!-- Heading -->
    <div class="pt-5 bg-body-tertiary mb-4">
        <h1>{{ __('Create Item') }}</h1>
        <nav class="d-flex">
            <h6 class="mb-0">
                <a href="{{ route('admin.panel.dashboard', ['lang' => app()->getLocale()]) }}"
                    class="text-reset">{{ __('Home') }}</a>
                <span>/</span>
                <a href="{{ route('admin.panel.items.list', ['lang' => app()->getLocale()]) }}"
                    class="text-reset">{{ __('Items') }}</a>
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
                    <!-- Code -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="code">{{ __('Code') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-barcode"></i>
                            </span>
                            <input type="text" id="code" wire:model.defer="code" class="form-control"
                                placeholder="{{ __('Enter code') }}" dir="ltr" disabled />
                        </div>
                        @error('code')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Name -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="name">{{ __('Name') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-gift"></i>
                            </span>
                            <input type="text" id="name" wire:model.defer="name" class="form-control"
                                placeholder="{{ __('Enter item name') }}" />
                        </div>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div class="col-md-6 mb-4">
                        <div class="mb-4" wire:ignore>
                            <label class="form-label" for="type">{{ __('Type') }}</label>
                            <select id="type" class="select" wire:model.defer="type">
                                <option value="sale">{{ __('Sale') }}</option>
                                <option value="rental">{{ __('Rental') }}</option>
                            </select>
                            @error('type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Purchase Price -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="purchase_price">{{ __('Purchase Price') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-dollar-sign"></i>
                            </span>
                            <input type="number" step="0.01" id="purchase_price" wire:model.defer="purchase_price"
                                class="form-control" placeholder="{{ __('Enter purchase price') }}" />
                        </div>
                        @error('purchase_price')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Sale Price -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="sale_price" id="sale_price_label">{{ __('Sale Price') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-tags"></i>
                            </span>
                            <input type="number" step="0.01" id="sale_price" wire:model.defer="sale_price"
                                class="form-control" placeholder="{{ __('Enter sale price') }}" />
                        </div>
                        @error('sale_price')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Quantity -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="quantity">{{ __('Quantity') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-cubes"></i>
                            </span>
                            <input type="number" id="quantity" wire:model.defer="quantity" class="form-control"
                                placeholder="{{ __('Enter quantity') }}" />
                        </div>
                        @error('quantity')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Low Stock Alert -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="low_stock_alert">{{ __('Low Stock Alert') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-bell"></i>
                            </span>
                            <input type="number" id="low_stock_alert" wire:model.defer="low_stock_alert"
                                class="form-control" placeholder="{{ __('Enter alert threshold') }}" />
                        </div>
                        @error('low_stock_alert')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>



                <!-- Description -->
                <div class="mb-4">
                    <label class="form-label" for="description">{{ __('Description') }}</label>
                    <textarea id="description" wire:model.defer="description" class="form-control" rows="3"
                        placeholder="{{ __('Enter description') }}"></textarea>
                    @error('description')
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
                            <span class="fas fa-spinner fa-spin me-2"></span>
                        </span>
                    </button>
                    <a href="{{ route('admin.panel.items.list', ['lang' => app()->getLocale()]) }}"
                        class="btn btn-secondary">
                        <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i>
                        {{ __('Back') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
    <!-- End Form Card -->

</div>


@push('scripts')
    <script>
        $(document).ready(function() {
            const $typeSelector = document.querySelector("#type");
            const $salePriceLabel = document.querySelector("#sale_price_label");

            // on change type print value to console
            $typeSelector.addEventListener("change", function(event) {
                if (event.target.value === "rental") {
                    $salePriceLabel.textContent = "{{ __('Rental Price') }}";
                } else {
                    $salePriceLabel.textContent = "{{ __('Sale Price') }}";
                }
            });

        });
    </script>
@endpush

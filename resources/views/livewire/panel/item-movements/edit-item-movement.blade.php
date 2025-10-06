<div class="container p-2 pb-5 px-0">

    <!-- Heading -->
    <div class="pt-5 bg-body-tertiary mb-4">
        <h1>{{ __('Edit Item Movement') }}</h1>
        <nav class="d-flex">
            <h6 class="mb-0">
                <a href="{{ route('admin.panel.dashboard', ['lang' => app()->getLocale()]) }}"
                    class="text-reset">{{ __('Home') }}</a>
                <span>/</span>
                <a href="{{ route('admin.panel.item-movements.list', ['lang' => app()->getLocale()]) }}"
                    class="text-reset">{{ __('Item Movements') }}</a>
                <span>/</span>
                <u>{{ __('Edit') }}</u>
            </h6>
        </nav>
    </div>

    <!-- Form Card -->
    <div class="card shadow-lg rounded-3">
        <div class="card-body">
            <form wire:submit.prevent="update">

                <div class="row">
                    <!-- Item -->
                    <div class="col-md-6 mb-4" wire:ignore>
                        <label class="form-label" for="item_id">{{ __('Item') }}</label>
                        <select id="item_id" class="select" wire:model.defer="item_id">
                            <option value="">{{ __('Select item') }}</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}">{{ $item->name . ' (' . $item->code . ')' }}
                                </option>
                            @endforeach
                        </select>
                        @error('item_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Quantity -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="quantity">{{ __('Quantity') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-cubes"></i></span>
                            <input type="number" id="quantity" wire:model.defer="quantity" class="form-control"
                                placeholder="{{ __('Enter quantity') }}" />
                        </div>
                        @error('quantity')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Movement Type -->
                    <div class="col-md-6 mb-4" wire:ignore>
                        <label class="form-label" for="movement_type">{{ __('Movement Type') }}</label>
                        <select id="movement_type" class="select" wire:model.defer="movement_type">
                            <option value="in">{{ __('In') }}</option>
                            <option value="out">{{ __('Out') }}</option>
                        </select>
                        @error('movement_type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Reason -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="reason">{{ __('Reason') }}</label>
                        <input type="text" id="reason" wire:model.defer="reason" class="form-control"
                            placeholder="{{ __('Enter reason (sale, purchase...)') }}" />
                        @error('reason')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="update">
                            <i class="fas fa-plus"></i> {{ __('Update') }}
                        </span>
                        <span wire:loading wire:target="update">
                            <span class="fas fa-spinner fa-spin me-2"></span>
                        </span>
                    </button>
                    <a href="{{ route('admin.panel.item-movements.list', ['lang' => app()->getLocale()]) }}"
                        class="btn btn-secondary">
                        <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i>
                        {{ __('Back') }}
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            var $item_id = "{{ $item_id }}" + "";
            var $movement_type = "{{ $movement_type }}" + "";
            if ($item_id) {
                const $itemSelector = document.querySelector("#item_id");
                const $itemSelectInstance = mdb.Select.getInstance($itemSelector);
                $itemSelectInstance.setValue($item_id);
            }
            if ($movement_type) {
                const $movementTypeSelector = document.querySelector("#movement_type");
                const $movementTypeSelectInstance = mdb.Select.getInstance($movementTypeSelector);
                $movementTypeSelectInstance.setValue($movement_type);
            }
        });
    </script>
@endpush

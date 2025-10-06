<div class="container-fluid p-5 rounded-3">

    <!-- Heading -->
    <div class="mb-4 bg-light">
        <h1 class="">{{ __('Item Movements') }}</h1>
        <!-- Breadcrumb -->
        <nav class="d-flex">
            <h6 class="mb-0">
                <a href="{{ route('admin.panel.dashboard', ['lang' => app()->getLocale()]) }}"
                    class="text-reset">{{ __('Home') }}</a>
                <span>/</span>
                <a href="{{ route('admin.panel.item-movements.list', ['lang' => app()->getLocale()]) }}"
                    class="text-reset"><u>{{ __('Item Movements') }}</u></a>
            </h6>
        </nav>
        <!-- Breadcrumb -->
    </div>
    <!-- Heading -->

    <!-- Filters -->
    <div class="row p-2 mb-3 align-items-center justify-content-between">
        <div class="col-md-9 d-flex gap-3" wire:ignore>
            <!-- Search -->
            <div class="mb-2">
                <label class="form-label mb-1" for="search"><strong>{{ __('Search') }}</strong></label>
                <div class="form-outline" data-mdb-input-init>
                    <input type="search" id="search" wire:model.live.debounce.500ms="search"
                        class="form-control form-icon-trailing" placeholder="{{ __('Search by item or reason') }}" />
                    <label class="form-label" for="search">{{ __('Search by item or reason') }}</label>
                    <i class="fas fa-search trailing"></i>
                </div>
            </div>
            <!-- Filter by movement type -->
            <div>
                <label class="form-label mb-1" for="movement_type"><strong>{{ __('Movement Type') }}</strong></label>
                <select id="movement_type" class="select" wire:model.live="movement_type">
                    <option value="">{{ __('All') }}</option>
                    <option value="in">{{ __('In') }}</option>
                    <option value="out">{{ __('Out') }}</option>
                </select>
            </div>
        </div>
        <div class="col-md-3 d-flex justify-content-end gap-2">
            <button class="btn btn-primary btn-sm" wire:click="create" wire:loading.attr="disabled"
                wire:target="create">
                <span wire:loading wire:target="create">
                    <i class="fas fa-spinner fa-spin text-light me-2"></i>
                </span>
                <i class="fas fa-plus"></i> {{ __('Create') }}
            </button>
            <button class="btn btn-secondary btn-sm" wire:click="resetFilters" wire:loading.attr="disabled"
                wire:target="resetFilters">
                <span wire:loading wire:target="resetFilters">
                    <i class="fas fa-spinner fa-spin text-light me-2"></i>
                </span>
                <i class="fas fa-undo"></i> {{ __('Reset') }}
            </button>
            @if (count($selectedItemMovements) > 0)
                <button class="btn btn-danger btn-sm" wire:click="confirmDeleteSelected" wire:loading.attr="disabled"
                    wire:target="confirmDeleteSelected">
                    <span wire:loading wire:target="confirmDeleteSelected">
                        <i class="fas fa-spinner fa-spin text-light me-2"></i>
                    </span>
                    <i class="fas fa-trash-alt"></i> {{ __('Delete') }} ({{ count($selectedMovements) }})
                </button>
            @endif
        </div>
    </div>
    <!-- Filters -->

    <!-- Start Movements Table Responsive Bordered -->
    <div class="table-responsive-md text-center">
        <div style="height: 8px; margin-bottom: 12px;">
            <div class="datatable-loader bg-light" style="height: 8px;" wire:loading>
                <span class="datatable-loader-inner">
                    <span class="datatable-progress bg-primary"></span>
                </span>
            </div>
        </div>
        <table class="table table-bordered table-hover align-middle text-center rounded-3 shadow-lg">
            <thead>
                <tr>
                    <th style="width: 30px;" class="text-center">
                        <div class="form-check font-size-16 d-flex justify-content-center">
                            <input type="checkbox" class="form-check-input" wire:model.live="selectAll" id="select-all">
                        </div>
                    </th>
                    <th>{{ __('ID') }}</th>
                    <th>{{ __('Item') }}</th>
                    <th>{{ __('Quantity') }}</th>
                    <th>{{ __('Movement Type') }}</th>
                    <th>{{ __('Reason') }}</th>
                    <th>{{ __('Created At') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($item_movements as $movement)
                    <tr>
                        <td class="text-center">
                            <div class="form-check font-size-16 d-flex justify-content-center align-items-center">
                                <input type="checkbox" class="form-check-input" value="{{ $movement->id }}"
                                    wire:model.live="selectedMovements">
                            </div>
                        </td>
                        <td>{{ $movement->id }}</td>
                        <td>{{ $movement->item->name ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $movement->movement_type == 'in' ? 'success' : 'danger' }}">
                                {{ $movement->quantity }}
                            </span>
                        </td>

                        <td>
                            @if ($movement->movement_type == 'in')
                                <span class="badge badge-primary">{{ ucfirst($movement->movement_type) }}</span>
                            @endif

                            @if ($movement->movement_type == 'out')
                                <span class="badge badge-warning">{{ ucfirst($movement->movement_type) }}</span>
                            @endif

                            @if ($movement->movement_type == 'reserved')
                                <span class="badge badge-warning">{{ ucfirst($movement->movement_type) }}</span>
                            @endif
                            @if ($movement->movement_type == 'released')
                                <span class="badge badge-danger">{{ ucfirst($movement->movement_type) }}</span>
                            @endif
                        </td>

                        <td>{{ $movement->reason ?? '-' }}</td>
                        <td>{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <!-- Edit Icon -->
                            <span wire:loading.remove wire:target="edit({{ $movement->id }})">
                                <a href="#edit" wire:click="edit({{ $movement->id }})"
                                    class="text-dark fa-lg me-2 ms-2" title="{{ __('Edit') }}">
                                    <x-icons.edit />
                                </a>
                            </span>
                            <span wire:loading wire:target="edit({{ $movement->id }})">
                                <span class="spinner-border spinner-border-sm text-dark me-2 ms-2"
                                    role="status"></span>
                            </span>
                            <!-- Delete Icon -->
                            <span wire:loading.remove wire:target="confirmDelete({{ $movement->id }})">
                                <a href="#" wire:click="confirmDelete({{ $movement->id }})"
                                    class="text-danger fa-lg me-2 ms-2" title="{{ __('Delete') }}">
                                    <x-icons.delete />
                                </a>
                            </span>
                            <span wire:loading wire:target="confirmDelete({{ $movement->id }})">
                                <span class="spinner-border spinner-border-sm text-danger me-2 ms-2"
                                    role="status"></span>
                            </span>
                            <!-- Show Icon -->
                            <span wire:loading.remove wire:target="show({{ $movement->id }})">
                                <a href="#" wire:click="show({{ $movement->id }})"
                                    class="text-primary fa-lg me-2 ms-2" title="{{ __('Show') }}">
                                    <x-icons.show />
                                </a>
                            </span>
                            <span wire:loading wire:target="show({{ $movement->id }})">
                                <span class="spinner-border spinner-border-sm text-primary me-2 ms-2"
                                    role="status"></span>
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('No data found') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- End Movements Table Responsive Bordered -->

    <!-- Table Pagination -->
    <div class="d-flex justify-content-between mt-4">
        <nav aria-label="...">
            <ul class="pagination pagination-circle">
                {{ $item_movements->withQueryString()->onEachSide(0)->links() }}
            </ul>
        </nav>
        <div class="col-md-1" wire:ignore>
            <select class="select" wire:model.live="pagination">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>
    <!-- Table Pagination -->

</div>

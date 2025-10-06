<div class="container-fluid p-5 rounded-3">

    <!-- Heading -->
    <div class="mb-4 bg-light">
        <h1 class="">{{ __('Sales') }}</h1>
        <!-- Breadcrumb -->
        <nav class="d-flex">
            <h6 class="mb-0">
                <a href="{{ route('admin.panel.dashboard', ['lang' => app()->getLocale()]) }}"
                    class="text-reset">{{ __('Home') }}</a>
                <span>/</span>
                <a href="{{ route('admin.panel.sales.list', ['lang' => app()->getLocale()]) }}"
                    class="text-reset"><u>{{ __('Sales') }}</u></a>
            </h6>
        </nav>
        <!-- Breadcrumb -->
    </div>
    <!-- Heading -->

    <!-- Filters -->
    <div class="row p-2 mb-3 align-items-center justify-content-between">
        <div class="col-md-9 d-flex gap-3" wire:ignore>
            <div class="mb-2">
                <label class="form-label mb-1" for="search"><strong>{{ __('Search') }}</strong></label>
                <div class="form-outline" data-mdb-input-init>
                    <input type="search" id="search" wire:model.live.debounce.500ms="search"
                        class="form-control form-icon-trailing"
                        placeholder="{{ __('Search by invoice or customer') }}" />
                    <label class="form-label" for="search">{{ __('Search by invoice or customer') }}</label>
                    <i class="fas fa-search trailing"></i>
                </div>
            </div>
            <div>
                <label class="form-label mb-1" for="status"><strong>{{ __('Status') }}</strong></label>
                <select id="status" class="select" wire:model.live="status">
                    <option value="">{{ __('All') }}</option>
                    <option value="paid">{{ __('Paid') }}</option>
                    <option value="partial">{{ __('Partial') }}</option>
                    <option value="unpaid">{{ __('Unpaid') }}</option>
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
            @if (count($selectedSales) > 0)
                <button class="btn btn-danger btn-sm" wire:click="confirmDeleteSelected" wire:loading.attr="disabled"
                    wire:target="confirmDeleteSelected">
                    <span wire:loading wire:target="confirmDeleteSelected">
                        <i class="fas fa-spinner fa-spin text-light me-2"></i>
                    </span>
                    <i class="fas fa-trash-alt"></i> {{ __('Delete') }} ({{ count($selectedSales) }})
                </button>
            @endif
        </div>
    </div>
    <!-- Filters -->

    <!-- Start Sales Table Responsive Bordered -->
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
                    <th>{{ __('Invoice Number') }}</th>
                    <th>{{ __('Customer') }}</th>
                    <th>{{ __('Total') }}</th>
                    <th>{{ __('Discount') }}</th>
                    <th>{{ __('Grand Total') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Created At') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $sale)
                    <tr>
                        <td class="text-center">
                            <div class="form-check font-size-16 d-flex justify-content-center align-items-center">
                                <input type="checkbox" class="form-check-input" value="{{ $sale->id }}"
                                    wire:model.live="selectedSales">
                            </div>
                        </td>
                        <td>{{ $sale->id }}</td>
                        <td>{{ $sale->invoice_number }}</td>
                        <td>
                            @if ($sale->customer)
                                {{ $sale->customer->name }}
                            @else
                                <span class="text-muted">{{ __('N/A') }}</span>
                            @endif
                        </td>
                        <td>{{ number_format($sale->total, 2) }}</td>
                        <td>{{ number_format($sale->discount, 2) }}</td>
                        <td>{{ number_format($sale->grand_total, 2) }}</td>
                        <td>
                            <span
                                class="badge badge-{{ $sale->status == 'paid' ? 'success' : ($sale->status == 'partial' ? 'warning' : 'danger') }}">
                                {{ ucfirst($sale->status) }}
                            </span>
                        </td>
                        <td>{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            @if (!$sale->payments()->exists())
                                <!-- Edit Icon -->
                                <span wire:loading.remove wire:target="edit({{ $sale->id }})">
                                    <a href="#edit" wire:click="edit({{ $sale->id }})"
                                        class="text-dark fa-lg me-2 ms-2" title="{{ __('Edit') }}">
                                        <x-icons.edit />
                                    </a>
                                </span>
                                <span wire:loading wire:target="edit({{ $sale->id }})">
                                    <span class="spinner-border spinner-border-sm text-dark me-2 ms-2"
                                        role="status"></span>
                                </span>
                                <!-- Delete Icon -->
                                <span wire:loading.remove wire:target="confirmDelete({{ $sale->id }})">
                                    <a href="#" wire:click="confirmDelete({{ $sale->id }})"
                                        class="text-danger fa-lg me-2 ms-2" title="{{ __('Delete') }}">
                                        <x-icons.delete />
                                    </a>
                                </span>
                                <span wire:loading wire:target="confirmDelete({{ $sale->id }})">
                                    <span class="spinner-border spinner-border-sm text-danger me-2 ms-2"
                                        role="status"></span>
                                </span>
                            @endif

                            <!-- Show Sale Icon -->
                            <span wire:loading.remove wire:target="show({{ $sale->id }})">
                                <a href="#items" wire:click="show({{ $sale->id }})"
                                    class="text-primary fa-lg me-2 ms-2" title="{{ __('Show') }}">
                                    <x-icons.show />
                                </a>
                            </span>
                            <span wire:loading wire:target="show({{ $sale->id }})">
                                <span class="spinner-border spinner-border-sm text-primary me-2 ms-2"
                                    role="status"></span>
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('No data found') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- End Sales Table Responsive Bordered -->

    <!-- Table Pagination -->
    <div class="d-flex justify-content-between mt-4">
        <nav aria-label="...">
            <ul class="pagination pagination-circle">
                {{ $sales->withQueryString()->onEachSide(0)->links() }}
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

<div class="container-fluid p-5 rounded-3">

    <!-- Heading -->
    <div class="mb-4 bg-light">
        <h1 class="">{{ __('Payments') }}</h1>
        <!-- Breadcrumb -->
        <nav class="d-flex">
            <h6 class="mb-0">
                <a href="{{ route('admin.panel.dashboard', ['lang' => app()->getLocale()]) }}"
                    class="text-reset">{{ __('Home') }}</a>
                <span>/</span>
                <a href="{{ route('admin.panel.payments.list', ['lang' => app()->getLocale()]) }}"
                    class="text-reset"><u>{{ __('Payments') }}</u></a>
            </h6>
        </nav>
        <!-- Breadcrumb -->
    </div>
    <!-- Heading -->

    <!-- Filters -->
    <div class="row p-2 mb-3 align-items-center justify-content-between">
        <div class="col-md-9 d-flex gap-3" wire:ignore>

            <div class="col-md-3 mb-2">
                <label class="form-label mb-1" for="search"><strong>{{ __('Search') }}</strong></label>
                <div class="form-outline" data-mdb-input-init>
                    <input type="search" id="search" wire:model.live.debounce.500ms="search"
                        class="form-control form-icon-trailing" placeholder="{{ __('Search by ID or paymentable') }}" />
                    <label class="form-label" for="search">{{ __('Search by ID or paymentable') }}</label>
                    <i class="fas fa-search trailing"></i>
                </div>
            </div>

            <div>
                <label class="form-label mb-1" for="method"><strong>{{ __('Method') }}</strong></label>
                <select id="method" class="select" wire:model.live="method">
                    <option value="">{{ __('All') }}</option>
                    <option value="cash">{{ __('Cash') }}</option>
                    <option value="card">{{ __('Card') }}</option>
                    <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
                    <option value="palpay">{{ __('PalPay') }}</option>
                    <option value="jawwalPay">{{ __('JawwalPay') }}</option>
                    <option value="other">{{ __('Other') }}</option>
                </select>
            </div>
            <div>
                <label class="form-label mb-1" for="status"><strong>{{ __('Status') }}</strong></label>
                <select id="status" class="select" wire:model.live="status">
                    <option value="">{{ __('All') }}</option>
                    <option value="synced">{{ __('Synced') }}</option>
                    <option value="unsynced">{{ __('Unsynced') }}</option>
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
            @if (count($selectedPayments) > 0)
                <button class="btn btn-danger btn-sm" wire:click="confirmDeleteSelected" wire:loading.attr="disabled"
                    wire:target="confirmDeleteSelected">
                    <span wire:loading wire:target="confirmDeleteSelected">
                        <i class="fas fa-spinner fa-spin text-light me-2"></i>
                    </span>
                    <i class="fas fa-trash-alt"></i> {{ __('Delete') }} ({{ count($selectedPayments) }})
                </button>
            @endif
        </div>
    </div>
    <!-- Filters -->

    <!-- Start Payments Table Responsive Bordered -->
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
                    <th>{{ __('Paymentable Type') }}</th>
                    <th>{{ __('Paymentable ID') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Method') }}</th>
                    <th>{{ __('Created At') }}</th>
                    <th>{{ __('Synced At') }}</th>
                    <th>{{ __('Created By') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td class="text-center">
                            <div class="form-check font-size-16 d-flex justify-content-center align-items-center">
                                <input type="checkbox" class="form-check-input" value="{{ $payment->id }}"
                                    wire:model.live="selectedPayments">
                            </div>
                        </td>
                        <td>{{ $payment->id }}</td>
                        <td>{{ class_basename($payment->paymentable_type) }}</td>
                        <td>{{ $payment->paymentable_id }}</td>
                        <td>{{ number_format($payment->amount, 2) }}</td>
                        <td>
                            <span
                                class="badge badge-{{ $payment->method == 'cash'
                                    ? 'success'
                                    : ($payment->method == 'card'
                                        ? 'primary'
                                        : ($payment->method == 'bank_transfer'
                                            ? 'info'
                                            : ($payment->method == 'palpay'
                                                ? 'warning'
                                                : ($payment->method == 'jawwalPay'
                                                    ? 'secondary'
                                                    : 'dark')))) }}">
                                {{ ucfirst($payment->method) }}
                            </span>
                        </td>
                        <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            @if ($payment->synced_at)
                                <span class="badge badge-success">{{ __('Synced') }}</span>
                                <br>
                                <small>{{ $payment->synced_at->format('Y-m-d H:i') }}</small>
                            @else
                                <span class="badge badge-danger">{{ __('Unsynced') }}</span>
                            @endif
                        </td>
                        <td>
                            @if ($payment->created_by)
                                {{ $payment->createdBy->name ?? __('N/A') }}
                            @else
                                <span class="text-muted">{{ __('N/A') }}</span>
                            @endif
                        </td>
                        <td>
                            <!-- Edit Icon -->
                            <span wire:loading.remove wire:target="edit({{ $payment->id }})">
                                <a href="#edit" wire:click="edit({{ $payment->id }})"
                                    class="text-dark fa-lg me-2 ms-2" title="{{ __('Edit') }}">
                                    <x-icons.edit />
                                </a>
                            </span>
                            <span wire:loading wire:target="edit({{ $payment->id }})">
                                <span class="spinner-border spinner-border-sm text-dark me-2 ms-2"
                                    role="status"></span>
                            </span>
                            <!-- Delete Icon -->
                            <span wire:loading.remove wire:target="confirmDelete({{ $payment->id }})">
                                <a href="#" wire:click="confirmDelete({{ $payment->id }})"
                                    class="text-danger fa-lg me-2 ms-2" title="{{ __('Delete') }}">
                                    <x-icons.delete />
                                </a>
                            </span>
                            <span wire:loading wire:target="confirmDelete({{ $payment->id }})">
                                <span class="spinner-border spinner-border-sm text-danger me-2 ms-2"
                                    role="status"></span>
                            </span>
                            <!-- Show Payment Icon -->
                            <span wire:loading.remove wire:target="show({{ $payment->id }})">
                                <a href="#items" wire:click="show({{ $payment->id }})"
                                    class="text-primary fa-lg me-2 ms-2" title="{{ __('Show') }}">
                                    <x-icons.show />
                                </a>
                            </span>
                            <span wire:loading wire:target="show({{ $payment->id }})">
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
    <!-- End Payments Table Responsive Bordered -->

    <!-- Table Pagination -->
    <div class="d-flex justify-content-between mt-4">
        <nav aria-label="...">
            <ul class="pagination pagination-circle">
                {{ $payments->withQueryString()->onEachSide(0)->links() }}
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

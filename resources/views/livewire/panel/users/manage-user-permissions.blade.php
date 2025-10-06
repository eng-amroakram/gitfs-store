<div class="container-fluid p-4 pb-5">
    <!-- Header -->
    <div class="pt-5 bg-light mb-4 rounded shadow-sm p-3 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">{{ __('Manage User Roles & Permissions') }}</h1>
            <nav class="d-flex">
                <a href="{{ route('admin.panel.dashboard', ['lang' => app()->getLocale()]) }}"
                    class="text-reset">{{ __('Home') }}</a>
                <span class="mx-1">/</span>
                <a href="{{ route('admin.panel.users.list', ['lang' => app()->getLocale()]) }}"
                    class="text-reset">{{ __('Users') }}</a>
                <span class="mx-1">/</span>
                <u>صلاحيات المستخدم {{ $user->name }}</u>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm" wire:click="update" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="update">
                    <i class="fas fa-save me-1"></i> {{ __('Save') }}
                </span>
                <span wire:loading wire:target="update">
                    <span class="fas fa-spinner fa-spin me-2"></span>
                </span>
            </button>
            <a href="{{ route('admin.panel.users.list', ['lang' => app()->getLocale()]) }}"
                class="btn btn-secondary btn-sm">
                <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-right' : 'fa-arrow-left' }} me-1"></i>
                {{ __('Back') }}
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Roles Card -->
        {{-- <div class="col-12 col-lg-4">
            <div class="card shadow-sm rounded-3 h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>{{ __('Roles') }}</h5>
                </div>
                <div class="card-body">
                    @foreach ($roles as $role)
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="role-{{ $role->id }}"
                                value="{{ $role->name }}" wire:model.live="selectedRoles">
                            <label class="form-check-label" for="role-{{ $role->id }}">
                                {{ __($role->name) }}
                                <span class="badge bg-info">{{ strtoupper($role->guard_name) }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div> --}}

        <!-- Web Permissions Card -->
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm rounded-3 h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-desktop me-2"></i>{{ __('Web Permissions') }}</h5>
                </div>
                <div class="card-body">
                    <div class="accordion accordion-flush" id="webPermissionsAccordion" wire:ignore.self>
                        @foreach ($permissionsByGroup ?? [] as $group => $perms)
                            <div class="accordion-item mb-2">
                                <h2 class="accordion-header" id="heading-web-{{ $group }}">
                                    <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse"
                                        data-mdb-target="#collapse-web-{{ $group }}" aria-expanded="false"
                                        aria-controls="collapse-web-{{ $group }}" wire:ignore.self>
                                        {{ __(ucfirst($group ?? 'General')) }}
                                    </button>
                                </h2>
                                <div id="collapse-web-{{ $group }}" class="accordion-collapse collapse"
                                    aria-labelledby="heading-web-{{ $group }}"
                                    data-mdb-parent="#webPermissionsAccordion" wire:ignore.self>
                                    <div class="accordion-body">
                                        @foreach ($perms as $perm)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox"
                                                    id="perm-web-{{ $perm['id'] }}" value="{{ $perm['name'] }}"
                                                    wire:model="selectedPermissions">
                                                <label class="form-check-label" for="perm-web-{{ $perm['id'] }}">
                                                    {{ __($perm['label'] ?? $perm['name']) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid p-4 pb-5">

    <!-- Heading -->
    <div class="pt-5 bg-body-tertiary mb-4">
        <h1>{{ __('Edit User') }}</h1>
        <nav class="d-flex">
            <h6 class="mb-0">
                <a href="{{ route('admin.panel.dashboard', ['lang' => app()->getLocale()]) }}"
                    class="text-reset">{{ __('Home') }}</a>
                <span>/</span>
                <a href="{{ route('admin.panel.users.list', ['lang' => app()->getLocale()]) }}"
                    class="text-reset">{{ __('Users') }}</a>
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
                    <!-- Name -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="name">{{ __('Name') }}</label>
                        <div class="input-group ">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" id="name" wire:model.defer="name" class="form-control"
                                placeholder="{{ __('Enter name') }}" />
                        </div>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="username">{{ __('Username') }}</label>
                        <div class="input-group ">
                            <span class="input-group-text">
                                <i class="fas fa-id-card"></i>
                            </span>
                            <input type="text" id="username" wire:model.defer="username" class="form-control"
                                placeholder="{{ __('Enter username') }}" maxlength="15" dir="ltr" />
                            <span class="input-group-text">
                                <i class="fas fa-at"></i>
                            </span>
                        </div>
                        @error('username')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <div class="row">

                    <!-- Email -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="email">{{ __('Email') }}</label>
                        <div class="input-group ">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" id="email" wire:model.defer="email" class="form-control"
                                placeholder="{{ __('Enter email') }}" />
                            <span class="input-group-text">
                                <i class="fas fa-at"></i>
                            </span>
                        </div>
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="phone">{{ __('Phone') }}</label>
                        <div class="input-group ">
                            <span class="input-group-text">
                                <i class="fas fa-phone"></i>
                            </span>
                            <input type="text" id="phone" wire:model.defer="phone" class="form-control"
                                placeholder="{{ __('Enter phone') }}" maxlength="10" dir="ltr" />
                            <span class="input-group-text">
                                <i class="fas fa-mobile-alt"></i>
                            </span>
                        </div>
                        @error('phone')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row">

                    <!-- Role -->
                    <div class="col-md-6 mb-4">
                        <div wire:ignore>
                            <label class="form-label" for="role">{{ __('Role') }}</label>
                            <select id="role" class="select" wire:model.defer="role">
                                <option value="admin">{{ __('Admin') }}</option>
                                <option value="cashier">{{ __('Cashier') }}</option>
                                <option value="purchaser">{{ __('Purchaser') }}</option>
                                <option value="inventory_manager">{{ __('Inventory Manager') }}</option>
                                <option value="owner">{{ __('Owner') }}</option>
                            </select>
                        </div>

                        @error('role')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-md-6 mb-4">
                        <div wire:ignore>
                            <label class="form-label" for="status">{{ __('Status') }}</label>
                            <select id="status" class="select" wire:model.defer="status">
                                <option value="active">{{ __('Active') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <div class="row">
                    <!-- Password -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="password">{{ __('Password') }}</label>
                        <div class="input-group ">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" id="password" wire:model.defer="password" class="form-control"
                                placeholder="{{ __('Enter password') }}" dir="ltr" />
                            <span class="input-group-text">
                                <i class="fas fa-eye" id="togglePassword"></i>
                            </span>
                        </div>
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="update">
                            <i class="fas fa-save"></i> {{ __('Update') }}
                        </span>
                        <span wire:loading wire:target="update">
                            <span class="fas fa-spinner fa-spin me-2"></span>
                        </span>
                    </button>
                    <a href="{{ route('admin.panel.users.list', ['lang' => app()->getLocale()]) }}"
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
            var $status = "{{ $status }}" + "";
            var $role = "{{ $role }}" + "";
            if ($status) {
                const $statusSelector = document.querySelector("#status");
                const $statusSelectInstance = mdb.Select.getInstance($statusSelector);
                $statusSelectInstance.setValue($status);
            }
            if ($role) {
                const $roleSelector = document.querySelector("#role");
                const $roleSelectInstance = mdb.Select.getInstance($roleSelector);
                $roleSelectInstance.setValue($role);
            }
        });
    </script>
@endpush

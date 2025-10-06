<div class="container p-2 pb-5 px-0">
    <!-- Heading -->
    <div class="pt-5 bg-body-tertiary mb-4">
        <h1>{{ __('Edit Customer') }}</h1>
        <nav class="d-flex">
            <h6 class="mb-0">
                <a href="{{ route('admin.panel.dashboard', ['lang' => app()->getLocale()]) }}"
                    class="text-reset">{{ __('Home') }}</a>
                <span>/</span>
                <a href="{{ route('admin.panel.customers.list', ['lang' => app()->getLocale()]) }}"
                    class="text-reset">{{ __('Customers') }}</a>
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
                        <div class="input-group">
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

                    <!-- Email -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="email">{{ __('Email') }}</label>
                        <div class="input-group">
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
                </div>

                <div class="row">
                    <!-- Phone -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="phone">{{ __('Phone') }}</label>
                        <div class="input-group">
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

                <!-- Actions -->
                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="update">
                            <i class="fas fa-plus"></i> {{ __('Edit') }}
                        </span>
                        <span wire:loading wire:target="update">
                            <span class="fas fa-spinner fa-spin me-2"></span>
                        </span>
                    </button>
                    <a href="{{ route('admin.panel.customers.list', ['lang' => app()->getLocale()]) }}"
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

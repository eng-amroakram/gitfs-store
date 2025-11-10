<div style="width: 30rem;">
    <div>
        <!-- Username input -->
        <div class="input-group input-group-lg mb-3">
            <span class="input-group-text">
                <i class="fas fa-user"></i>
            </span>
            <input type="text" id="username" maxlength="30" class="form-control" wire:model.defer="username"
                placeholder="Username" />
        </div>
        @error('username')
            <div class="form-helper text-danger">{{ $message }}</div>
        @enderror

        <!-- Password input -->
        <div class="input-group input-group-lg mb-3 mt-4">
            <span class="input-group-text">
                <i class="fas fa-lock"></i>
            </span>
            <input type="password" id="passwordID" maxlength="30" class="form-control" wire:model.defer="password"
                placeholder="Password" />
        </div>
        @error('password')
            <div class="form-helper text-danger">{{ $message }}</div>
        @enderror

        <div class="d-flex justify-content-between align-items-center mb-4 mt-4 px-1" wire:ignore>
            <!-- Checkbox -->
            <div class="form-check">
                <input class="form-check-input" type="checkbox" wire:model="remember" id="rememberMe" checked />
                <label class="form-check-label" for="rememberMe">Remember Me</label>
            </div>
            <div>
                <a href="#">
                    Forgot Password?
                </a>
            </div>
        </div>

        <!-- Submit button -->
        <button type="button" class="btn btn-lg btn-block bg-danger text-white" wire:click="login"
            wire:loading.attr="disabled">
            <span wire:loading wire:target="login">
                <i class="fas fa-spinner fa-spin text-white me-2"></i>
            </span>
            <span wire:loading.remove wire:target="login">
                <i class="fas fa-sign-in-alt me-2"></i>
                <span>Login</span>
            </span>
        </button>
    </div>
</div>

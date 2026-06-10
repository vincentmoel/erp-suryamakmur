@extends('layouts.blank', [
    'title' => 'Sign In'
])

@section('content')
    <div style="display:flex; min-height:100svh; align-items:center; justify-content:center; background-color:color-mix(in oklab, var(--muted) 30%, transparent); padding:1.5rem;">
        <div style="width:100%; max-width:24rem;">

            {{-- Card --}}
            <div style="display:flex; flex-direction:column; gap:1.5rem; border-radius:0.75rem; border:1px solid var(--border); background-color:var(--card); color:var(--card-foreground); padding-top:1.5rem; padding-bottom:1.5rem; box-shadow:0 1px 2px 0 rgb(0 0 0 / 0.05);">

                {{-- Card Header --}}
                <div style="display:grid; gap:0.25rem; padding-left:1.5rem; padding-right:1.5rem; text-align:center;">
                    {{-- Logo --}}
                    <div style="display:flex; justify-content:center; margin-bottom:1.25rem; margin-top:0.25rem;">
                        <img src="{{ asset('src/img/logo-dark.png') }}" alt="Logo" class="dark:hidden" style="height:2.25rem; width:auto;">
                        <img src="{{ asset('src/img/logo-light.png') }}" alt="Logo" class="hidden dark:block" style="height:2.25rem; width:auto;">
                    </div>
                    <div style="font-size:1.25rem; line-height:1; font-weight:600; letter-spacing:-0.01em;">Welcome back</div>
                    <div style="font-size:0.875rem; color:var(--muted-foreground);">Sign in to your account to continue</div>
                </div>

                {{-- Card Content --}}
                <div style="padding-left:1.5rem; padding-right:1.5rem;">
                    <form method="POST" action="{{ route('authenticate') }}" id="login-form" novalidate
                          style="display:flex; flex-direction:column; gap:1rem;">
                        @csrf

                        {{-- Error Alert --}}
                        @if(session('error'))
                            <div style="display:flex; align-items:flex-start; gap:0.625rem; border-radius:0.5rem; border:1px solid var(--destructive); background-color:color-mix(in oklab, var(--destructive) 10%, transparent); padding:0.75rem 1rem; font-size:0.875rem; color:var(--destructive);">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;flex-shrink:0;margin-top:0.1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                                </svg>
                                <span>{{ session('error.message') ?? 'Login failed.' }}</span>
                            </div>
                        @endif

                        {{-- Username --}}
                        <div style="display:grid; gap:0.5rem;">
                            <label for="username" style="font-size:0.875rem; font-weight:500; line-height:1; display:flex; align-items:center; gap:0.5rem; user-select:none;">
                                Username
                            </label>
                            <input
                                id="username"
                                name="username"
                                type="text"
                                placeholder="Enter your username"
                                autocomplete="username"
                                value=""
                                class="input"
                                required
                                autofocus
                            >
                        </div>

                        {{-- Password --}}
                        <div style="display:grid; gap:0.5rem;">
                            <div style="display:flex; align-items:center; justify-content:space-between;">
                                <label for="password" style="font-size:0.875rem; font-weight:500; line-height:1; display:flex; align-items:center; gap:0.5rem; user-select:none;">
                                    Password
                                </label>
                            </div>
                            <div style="position:relative;">
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    placeholder="••••••••"
                                    autocomplete="current-password"
                                    class="input"
                                    style="padding-right:2.5rem;"
                                    required
                                >
                                <button type="button" id="toggle-password" aria-label="Toggle password visibility"
                                    style="position:absolute; inset:0; left:auto; width:2.25rem; display:flex; align-items:center; justify-content:center; color:var(--muted-foreground); background:none; border:none; cursor:pointer; transition:color 150ms;"
                                    onmouseover="this.style.color='var(--foreground)'"
                                    onmouseout="this.style.color='var(--muted-foreground)'">
                                    <svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                    </svg>
                                    <svg id="icon-eye-off" xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;display:none;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Remember Me --}}
                        <div style="display:flex; flex-direction:row; align-items:center; gap:0.5rem;">
                            <input type="checkbox" id="remember" name="remember" class="form-checkbox">
                            <label for="remember" style="font-size:0.75rem; font-weight:400; line-height:1; user-select:none; cursor:pointer;">
                                Remember me for 30 days
                            </label>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="btn btn-primary" style="width:100%;" id="btn-submit">
                            <svg id="btn-spinner" style="width:1rem;height:1rem;display:none;" class="animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle style="opacity:0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path style="opacity:0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span id="btn-label">Sign in</span>
                        </button>

                        {{-- Sign up link --}}
                        <p style="text-align:center; font-size:0.875rem; color:var(--muted-foreground); margin:0;">
                            Don't have an account?
                            <a href="#" style="color:var(--foreground); text-underline-offset:4px;"
                               onmouseover="this.style.textDecoration='underline'"
                               onmouseout="this.style.textDecoration='none'">Sign up</a>
                        </p>

                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {

        // Toggle password visibility
        $('#toggle-password').on('click', function () {
            const isPassword = $('#password').attr('type') === 'password';
            $('#password').attr('type', isPassword ? 'text' : 'password');
            $('#icon-eye').toggle(!isPassword);
            $('#icon-eye-off').toggle(isPassword);
        });

        // Loading state on submit
        $('#login-form').on('submit', function () {
            $('#btn-submit').prop('disabled', true);
            $('#btn-spinner').show();
            $('#btn-label').text('Signing in...');
        });

    });
</script>
@endpush

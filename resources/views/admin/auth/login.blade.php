<x-admin.layout.auth-layout>
    <h1 class="text-3xl text-slate-800 font-bold mb-6">{{ __('Welcome back!') }} ✨</h1>
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif
    <x-admin.flash-message />
    <!-- Form -->
    <form method="POST" action="{{ route('admin.auth.login') }}">
        @csrf
        <div class="space-y-4">
            <div>
                <x-admin.form.label for="email" value="{{ __('Email') }}" :required="true" />
                <x-admin.form.new-input id="email" type="email" name="email" max="50" :value="old('email')" :required="true" :showIcon="false" autofocus />
            </div>
            <div>
                <x-admin.form.label for="password" value="{{ __('Password') }}" :required="true" />
                <x-admin.form.new-input id="password" type="password" name="password" :required="true" autocomplete="current-password" />
            </div>
        </div>
        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <div class="mr-1">
                    {{-- <a class="text-sm underline hover:no-underline" href="{{ route('password.request') }}">
                        {{ __('Forgot Password?') }}
                    </a> --}}
                </div>
            @endif
            <x-admin.button type="submit" color="success" class="ml-3">
                {{ __('Sign in') }}
            </x-admin.button>
        </div>
    </form>
    {{-- <x-admin.validation-errors class="mt-4" /> --}}
</x-admin.layout.auth-layout>

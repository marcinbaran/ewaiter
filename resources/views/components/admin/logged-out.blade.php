@props(['class' => ''])
<div class="{{ $class }} flex flex-col justify-center items-center gap-8 overflow-hidden rounded-lg border border-gray-300 bg-gray-200 p-6 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-50 ">
    <div class="flex flex-col justify-center items-center gap-1">
        <p class="text-2xl">{{__('admin.You have been logged out')}}</p>
        <p class="text-base text-center text-gray-500 dark:text-gray-400">{{__('admin.Your session has expired. Click button below to go to the login screen')}}</p>
    </div>
    <x-admin.button type="link" class="flex flex-row justify-center items-center gap-2" color="success" href="{{ route('admin.auth.login') }}">{{ __('admin.Go to login page') }} <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-login-2 w-6 h-6" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M9 8v-2a2 2 0 0 1 2 -2h7a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-2"></path>
            <path d="M3 12h13l-3 -3"></path>
            <path d="M13 15l3 -3"></path>
        </svg></x-admin.button>
</div>
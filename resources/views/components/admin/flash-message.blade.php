<div class="hidden text-green-600 dark:text-green-400 text-red-600 dark:text-red-500 text-yellow-500 dark:text-yellow-400 text-blue-600 dark:text-blue-500 bg-green-600 dark:bg-green-400 bg-red-600 dark:bg-red-500 bg-yellow-500 dark:bg-yellow-400 bg-blue-600 dark:bg-blue-500"></div>
@foreach (['danger', 'warning', 'success', 'info'] as $msg)
    @if (Session::has('alert-' . $msg))
        <div class="mb-4 p-4 flex items-center gap-4 text-sm rounded-lg border dark:border-none {{ $getTextColor($msg) }} bg-gray-100 dark:bg-gray-700 border-gray-300" role="alert">
            <div class="icon rounded-full p-2 text-gray-50 {{ $getBackgroundColor($msg) }}">
                @if ($msg == 'danger')
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x h-5 w-5"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M18 6l-12 12"></path>
                        <path d="M6 6l12 12"></path>
                    </svg>
                @elseif ($msg == 'warning')
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="icon icon-tabler icon-tabler-exclamation-mark h-5 w-5" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M12 19v.01"></path>
                        <path d="M12 15v-10"></path>
                    </svg>
                @elseif ($msg == 'success')
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check h-5 w-5"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M5 12l5 5l10 -10"></path>
                    </svg>
                @elseif ($msg == 'info')
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-question-mark h-5 w-5"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M8 8a3.5 3 0 0 1 3.5 -3h1a3.5 3 0 0 1 3.5 3a3 3 0 0 1 -2 3a3 4 0 0 0 -2 4"></path>
                        <path d="M12 19l0 .01"></path>
                    </svg>
                @endif
            </div>
            <div class="message">
                {!! Session::get('alert-' . $msg) !!}
            </div>
        </div>
    @endif
@endforeach

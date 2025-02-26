@props(['description' => '', 'isActive' => false])

@if ($isActive)
    <span
        class="inline-flex items-center rounded-full bg-green-100 p-1.5 m-1.5 text-sm font-semibold text-green-800 dark:bg-transparent dark:text-green-400">
        <svg aria-hidden="true" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"
             xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"></path>
        </svg>
        <span class="sr-only">{{ $description }}</span>
    </span>
@else
    <span
        class="inline-flex items-center rounded-full bg-red-100 p-1.5 m-1.5 text-sm font-semibold text-red-800 dark:bg-transparent dark:text-red-400">
        <svg aria-hidden="true" class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"
             xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                  clip-rule="evenodd"></path>
        </svg>
        <span class="sr-only">{{ $description }}</span>
    </span>
@endif

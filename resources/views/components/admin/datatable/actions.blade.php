<div class="justify-content-between flex">
    @foreach ($buttons as $button)
            <?php
            if ($button->type == 'delete') {
                $buttonColor = 'delete text-red-600 hover:text-red-900 dark:hover:text-red-400';
            } elseif ($button->type == 'edit') {
                $buttonColor = 'text-blue-600 hover:text-blue-900 dark:hover:text-blue-400';
            } else {
                $buttonColor = 'text-gray-900 dark:text-gray-50 hover:text-primary-900 dark:hover:text-primary-700';
            }
            ?>
        <a
            @if ($button->type == 'delete')
                data-deleteid="{{ $button->id }}" wire:click="delete('{{ $button->id }}')"
            @elseif (($button->type == 'report-modal'))
                data-restaurantId="{{ $button->id }}" wire:click="showReportModal({{ $button->id }})"
            @else
                href="{{ $button->url }}" @endif
            data-url="{{ $button->url }}" title="{{ $button->label }}" data-type="{{ $button->type }}"
            class="{{ $buttonColor }} cursor-pointer p-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-login h-5 w-5"
                 viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                 stroke-linejoin="round">
                @switch($button->type)
                    @case('login')
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path
                            d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"></path>
                        <path d="M20 12h-13l3 -3m0 6l-3 -3"></path>
                        @break
                    @case('edit')
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                        <path d="M16 5l3 3"></path>
                        @break
                    @case('delete')
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M4 7l16 0"></path>
                        <path d="M10 11l0 6"></path>
                        <path d="M14 11l0 6"></path>
                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                        @break
                    @case('show')
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                        <path
                            d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path>
                        @break
                    @case('duplicate')
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path
                            d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z"></path>
                        <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"></path>
                        @break
                    @case('add')
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                        <path d="M9 12h6"></path>
                        <path d="M12 9v6"></path>
                        @break
                    @case('download')
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                        <path d="M7 11l5 5l5 -5" />
                        <path d="M12 4l0 12" />
                        @break
                    @case('send_email')
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M3 7h3" />
                        <path d="M3 11h2" />
                        <path
                            d="M9.02 8.801l-.6 6a2 2 0 0 0 1.99 2.199h7.98a2 2 0 0 0 1.99 -1.801l.6 -6a2 2 0 0 0 -1.99 -2.199h-7.98a2 2 0 0 0 -1.99 1.801z" />
                        <path d="M9.8 7.5l2.982 3.28a3 3 0 0 0 4.238 .202l3.28 -2.982" />
                        @break
                    @case('report-modal')
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path
                            d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                        <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                        <path d="M9 17v-5" />
                        <path d="M12 17v-1" />
                        <path d="M15 17v-3" />
                        @break
                @endswitch
            </svg>
        </a>
    @endforeach
</div>

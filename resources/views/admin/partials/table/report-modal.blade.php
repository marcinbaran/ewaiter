@if($isReportModalOpen)
    <div class="datatable-action-modal fixed inset-0 z-50">
        <div
            class="absolute left-1/2 top-1/2 flex -translate-x-1/2 -translate-y-1/2 flex-col justify-center gap-4 rounded-lg border border-gray-300 bg-gray-200 p-4 text-gray-600 shadow dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
            <div class="flex justify-between items-center gap-6">
                <h3 class="text-lg font-normal">
                    {{ __('admin.Report') }}</h3>
                <button wire:click="closeReportModal" class="flex justify-center items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="icon icon-tabler icons-tabler-outline icon-tabler-x h-5 w-5">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M18 6l-12 12" />
                        <path d="M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div>
                <x-admin.form.label for="duration"
                                    value="{{ __('dashboard.Duration') }}" />
                <select id="duration" name="duration" wire:model="duration">
                    <option
                        value="{{ \App\Enum\Dashboard\ReportDuration::THIS_WEEK }}">{{ __('dashboard.this-week') }}</option>
                    <option
                        value="{{ \App\Enum\Dashboard\ReportDuration::PREVIOUS_WEEK }}">{{ __('dashboard.previous-week') }}</option>
                    <option
                        value="{{ \App\Enum\Dashboard\ReportDuration::THIS_MONTH }}">{{ __('dashboard.this-month') }}</option>
                    <option
                        value="{{ \App\Enum\Dashboard\ReportDuration::PREVIOUS_MONTH }}">{{ __('dashboard.previous-month') }}</option>
                </select>
            </div>
            <div>
                <button wire:click="downloadReport"
                        class="px-4 py-2 bg-red-500 text-white rounded">{{ __('dashboard._datatable.download-report') }}</button>
                <button wire:click="sendReport"
                        class="px-4 py-2 bg-red-500 text-white rounded">{{ __('dashboard._datatable.send-report') }}</button>
            </div>
        </div>
    </div>
@endif

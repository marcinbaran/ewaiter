<ul class="divide-y divide-gray-300 dark:divide-gray-700">
    <x-admin.block.list-element :title="__('admin.Payment type')" :value="$data->payment->type" />
    <x-admin.block.list-element :title="__('admin.P24 amount')" :value="$data->payment->p24_amount" />
    <x-admin.block.list-element :title="__('admin.Currency')" :value="$data->payment->p24_currency" />
    <x-admin.block.list-element :title="__('admin.email')" :value="$data->payment->email" />
</ul>

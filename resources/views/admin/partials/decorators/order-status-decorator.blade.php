@php
    switch ($bill->status) {
        default:
        case \App\Models\Bill::STATUS_NEW:
            $bg = 'bg-purple-400';
            $text = 'text-purple-400';
            break;
        case \App\Models\Bill::STATUS_ACCEPTED:
            $bg = 'bg-indigo-100';
            $text = 'text-indigo-300';
            break;
        case \App\Models\Bill::STATUS_READY:
            $bg = 'bg-green-100';
            $text = 'text-emerald-400';
            break;
        case \App\Models\Bill::STATUS_CANCELED:
            $bg = 'bg-red-100';
            $text = 'text-red-500';
            break;
        case \App\Models\Bill::STATUS_COMPLAINT:
            $bg = 'bg-yellow-100';
            $text = 'text-yellow-400';
            break;
        case \App\Models\Bill::STATUS_RELEASED:
            $bg = 'bg-green-100';
            $text = 'text-lime-500';
            break;
    }
@endphp

<span class=" {{ $text }} text-sm font-semibold inline-flex items-center p-1.5 rounded-full">
    {{ $label }}
</span>

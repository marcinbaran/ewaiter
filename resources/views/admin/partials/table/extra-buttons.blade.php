@foreach($buttons as $button)
    <x-admin.button :type="$button['type']" :color="$button['color']" :href="$button['link']"
                    class="text-white flex justify-center items-center cursor-pointer">
        {{ $button['label'] }}
    </x-admin.button>
@endforeach

{{-- TODO: to be more reusable--}}

@props(['href' => ''])
<button {{$attributes}} href="{{$href}}"
        class="w-full text-dark-grey-2 rounded border border-[#E5E7EB] text-center py-2 px-4 hover:border-[#BABFCA] focus:border-[#EC3F59]">
    {!! $slot !!}
</button>

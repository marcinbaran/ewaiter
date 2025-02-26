@props( ["variants"=>""] )
<div
    class="w-full text-dark-grey-2 rounded border border-[#E5E7EB] text-center hover:border-[#BABFCA] focus:border-[#EC3F59]">
    <a class="w-full inline-block py-2 px-4 text-center" href="{{route('admin.marketplace.checkout')}}">
        <p class="m-auto">{{__('marketplace.go to payments')}}</p>
    </a>
</div>

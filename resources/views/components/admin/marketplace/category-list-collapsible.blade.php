<div id="categories" class="my-auto relative">
    <div id="collapsible"
         class="cursor-pointer relative border border-[#E5E7EB] rounded text-center my-auto py-2 px-4 w-[150px] hover:border-[#BABFCA] focus:border-[#BABFCA]">
        <div class="flex justify-around">
            <p class="text-[#596273] mr-2">{{__('marketplace.categories')}}</p>
            <svg class="my-auto" width="18" height="13" viewBox="0 0 14 9" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <path d="M1 1L7 7L13 1" stroke="#596273" stroke-width="1.5" stroke-linecap="round" />
            </svg>
        </div>
    </div>
    <div id="dropdown"
         class="hidden absolute z-10 bg-white border border-[#E5E7EB] rounded text-center w-full shadow w-full">
        {{-- TODO: categories --}}
        {{-- TODO: category-item zrobić komponent --}}
        <ul>
            <li class="p-4 hover:bg-[#F3F4F6]">Category1</li>
            <li class="p-4 hover:bg-[#F3F4F6]">Category2</li>
            <li class="p-4 hover:bg-[#F3F4F6]">Category3</li>
        </ul>
    </div>
</div>

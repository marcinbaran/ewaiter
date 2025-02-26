@props(['disabled' => false, 'image' => ''])

<div class="w-full h-fit text-center">
    <div class="w-full max-h-xs">
        <img class="max-h-full max-w-full" src="/{{ $image }}" alt="" />
        <div class="file-upload-indicator bg-gray-800 rounded-lg w-full h-24 group hover:bg-gray-200 opacity-60 flex justify-center items-center cursor-pointer transition duration-500">
            <img class="group-hover:block w-12 invert" src="https://www.svgrepo.com/show/33565/upload.svg" alt="" />
        </div>
        <input type="file" {!! $attributes->merge(['class' => 'hidden']) !!}>
    </div>
</div>

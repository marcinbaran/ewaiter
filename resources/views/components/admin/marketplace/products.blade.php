{{--{{dd($products)}}--}}
@props(["products" => []])
<div x-bind:class="{ 'md:grid-cols-2' : isTile, 'grid-cols-1' : !isTile}"
     class="grid grid-cols-1 gap-4 px-4">
    @foreach($products as $product)
        {{--        balde component --}}
        <x-admin.marketplace.product-card
            :id="$product->id"
            :code="$product->code"
            :images="$product->images"
            :name="$product->name"
            :variant="$product->defaultVariant"
            :price="$product->defaultVariant->price"
            :shortDescription="$product->shortDescription"
            :description="$product->description"/>
    @endforeach
</div>

<div
    class="map-initialize z-0"
    style="height: 400px;"
    data-address="{{ $data->address_as_text }}"
    id="order-map"
    data-label="{{ $data->address? $data->address_as_text:__('admin.No data') }}">

</div>

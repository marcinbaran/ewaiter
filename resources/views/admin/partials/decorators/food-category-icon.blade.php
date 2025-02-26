@if ($row->photo)
    <a href="/{{ $row->photo->getPhoto(true) }}" data-toggle="lightbox"
       data-gallery="category-images-{{$row->id}}">
        <img src="{{ Croppa::url($row->photo->getPhoto(false),null,34)}}" class="img-fluid">
    </a>
@endif

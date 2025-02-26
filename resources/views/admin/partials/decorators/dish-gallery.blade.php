@foreach($row->photos as $photo)
    @if(null == $photo->getPhoto())
        @continue
    @endif
    @if($loop->index==0)
        <div style="height:80px; width:100px; overflow:hidden">
            <a href="{!! Croppa::url($photo->getPhoto(true)) !!}" data-toggle="lightbox" data-gallery="dish-images-{{$row->id}}" >

                <img src="{{ Croppa::url($photo->getPhoto(true), 64, 64) }}" class="img-fluid">
            </a>
        </div>
    @else
        <div class="hide" data-toggle="lightbox" data-gallery="dish-images-{{$row->id}}" data-remote="{{ Croppa::url($photo->getPhoto(true),null,null)}}"></div>
    @endif
@endforeach

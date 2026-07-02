<div class="{{ $class ?? '' }}">
    @if(in_array($file_type, array('jpg', 'jpeg', 'png', 'bmp')))
        <a target="_blank" href="{{ asset("assets/$file") }}" class="w-100 h-auto">
            <img src="{{ asset("storage/$file") }}" alt="{{ $name }}" class="img-fluid w-100 h-auto {{ $imageClass }}">
        </a>
    @endif

    @if($file_type == 'pdf')
        <iframe src="{{ asset("storage/$file") }}" style="width: 100%;height: 400px;border: none;"></iframe>
    @endif
</div>

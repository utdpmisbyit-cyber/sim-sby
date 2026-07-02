@php($breadcrumbs = $breadcrumbs ?? [])
<ul class="app-breadcrumb">
    @foreach($breadcrumbs as $breadcrumb)
        <li>
            <a href="{{ $breadcrumb['url'] ?? '' }}">{{ $breadcrumb['caption'] }}</a>
        </li>
        @if($breadcrumb != last($breadcrumbs))
            <li><span class="sep"></span></li>
        @endif
    @endforeach
</ul>

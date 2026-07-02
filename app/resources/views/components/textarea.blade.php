<textarea
    name="{{ $name }}"
    id="{{ $prefix.$name }}"
    rows="{{ $rows }}"
    class="form-control form-control-sm {{ $class }}"
    placeholder="{{ $caption }}"
    {{ $required != '' ? 'required' : '' }}
    {{ $attributes }}
>{{ $value }}</textarea>

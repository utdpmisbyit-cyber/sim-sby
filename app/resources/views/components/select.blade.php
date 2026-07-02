<select
    name="{{ $name }}"
    id="{{ $prefix.$name }}"
    class="form-select form-select-sm {{ $class }}"
    {{ $attributes }}
>
    @if($caption != '')
        <option value="">{{ $caption }}</option>
    @endif
    @foreach($options as $key => $option)
        <option value="{{ $key }}" @if($key === $value) selected @endif>{!! $option === '' ? '&nbsp;' : $option !!}</option>
    @endforeach
</select>
@if($alert === '1')
    <div class="alert alert-danger d-flex align-items-center p-5 mt-5 d-none" id="{{ $prefix.$name }}_error" @error($name) style="display: block!important;" @enderror>
        <div class="d-flex flex-column" id="{{ $prefix.$name }}_error_content">
            @error($name) <span>{{ $message }}</span> @enderror
        </div>
    </div>
@endif

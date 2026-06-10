<input
    type="{{ $type }}"
    class="form-control form-control-sm {{ $class }}"
    name="{{ $name }}"
    id="{{ $prefix.str_replace(['[', ']'], '', $name) }}"
    placeholder="{{ $caption }}"
    value="{{ $value }}"
    autocomplete="off"
    {{ $required != '' ? 'required' : '' }}
    {{ $attributes }}
/>
@if($type === 'file' && $value !== '' && $preview === 1)
    <img src="{{ asset('storage/' . $value) }}" alt="" class="w-100px img-fluid">
@endif
@if($alert === '1')
    <div class="alert alert-danger d-flex align-items-center p-5 mt-5 d-none w-100" @error($name) style="display: block!important;" @enderror id="{{ $prefix.$name }}_error">
        <div class="d-flex flex-column align-items-start" id="{{ $prefix.$name }}_error_content">
            @error($name) <span>{{ $message }}</span> @enderror
        </div>
    </div>
@endif

<div class="form-check {{ $class }}">
    <input
        class="form-check-input {{ $classInput }}"
        name="{{ $name }}"
        id="{{ $prefix.str_replace(array('[',']'), '', $name) }}"
        type="radio"
        value="{{ $value ?? 1 }}"
        {{ $checked == true ? 'checked' : '' }}
        {{ $attributes }}
    />
    <label class="form-check-label" for="{{ $prefix.str_replace(array('[',']'), '', $name) }}">
        {{ $caption }}
    </label>
</div>

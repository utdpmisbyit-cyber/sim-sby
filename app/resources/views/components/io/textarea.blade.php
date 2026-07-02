@if($viewtype === 1)
    <div class="row mb-4">
        <label class="col-lg-3 form-label fs-7 fw-semibold text-gray-700 {{ $required == 1 ? 'required' : '' }}">{{ $caption }}</label>
        <div class="col-lg-9">
            <x-textarea
                :type="$type"
                :prefix="$prefix"
                :name="$name"
                class="mb-3 mb-lg-0 {{ $class }}"
                :caption="$placeholder"
                :value="$value"
                :rows="$rows"
                :required="$required"
                {{ $attributes }}
            />
        </div>
    </div>
@endif
@if($viewtype === 2)
    <div class="form-group mb-4 d-flex flex-column align-items-start">
        <label class="form-label fs-7 fw-semibold text-gray-700 {{ $required == 1 ? 'required' : '' }}">{{ $caption }}</label>
        <x-textarea
            :type="$type"
            :prefix="$prefix"
            :name="$name"
            class="mb-3 mb-lg-0 {{ $class }}"
            :caption="$placeholder"
            :value="$value"
            :rows="$rows"
            :required="$required"
            {{ $attributes }}
        />
    </div>
@endif

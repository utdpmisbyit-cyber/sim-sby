<div class="alert alert-danger d-flex align-items-center p-5 mt-5 d-none" @error($name) style="display: block!important;" @enderror id="{{ $prefix.$name }}_error">
    <div class="d-flex flex-column" id="{{ $prefix.$name }}_error_content">
        @error($name) <span>{{ $message ?? '' }}</span> @enderror
    </div>
</div>

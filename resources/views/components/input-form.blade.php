{{-- <div class="mb-3">
    <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    <input type="{{ $type }}" class="form-control @error($name) is-invalid @enderror" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}" />
    <span class="error invalid-feedback">{{ $errors->first($name) }}</span>
</div> --}}


<div class="mb-3">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if ($attributes->has('required'))
            <span class="text-danger">*</span>
        @endif
    </label>

    <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}"
        {{ $attributes->merge([
            'class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : ''),
        ]) }} />

    <span class="error invalid-feedback">
        {{ $errors->first($name) }}
    </span>
</div>

@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'required' => false])

<div class="form-group @error($name) has-error @enderror">
    <label for="{{ $name }}">
        {{ $label ?? __(str($name)->replace('_', ' ')->title()->toString()) }}
        @if ($required) <span class="text-danger">*</span> @endif
    </label>
    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ $type === 'password' ? '' : old($name, $value) }}"
        @required($required)
        {{ $attributes->merge(['class' => 'form-control']) }}
    >
    @error($name)
        <span class="help-block">{{ $message }}</span>
    @enderror
</div>

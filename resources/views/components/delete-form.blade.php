@props(['action'])

<form method="POST" action="{{ $action }}" style="display: inline;" onsubmit="return confirm('{{ __('Are you sure?') }}');">
    @csrf
    @method('DELETE')
    <button type="submit" {{ $attributes->merge(['class' => 'btn btn-xs btn-danger']) }}>
        <i class="fa fa-trash"></i> {{ $slot->isEmpty() ? __('Delete') : $slot }}
    </button>
</form>

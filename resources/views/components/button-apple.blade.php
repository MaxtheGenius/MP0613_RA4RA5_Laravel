@props([
    'href' => null,
    'type' => 'button',
])

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn-apple']) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => 'btn-apple']) }}>
        {{ $slot }}
    </button>
@endif


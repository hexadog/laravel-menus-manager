<div {{ $attributes->merge($item->attributes) }}>
    @isset($slot)
    {{ $slot }}
    @else
    <i class="{{ $icon }}"></i>
    @endif
</div>

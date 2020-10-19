<div x-show="open" {{ $attributes }}>
    @foreach($items as $item)
        @if ($item->isDivider())
        <x-menus-divider :item="$item" class="py-2 px-16 border-0 bg-gray-500 text-gray-500 h-px" />
        @else
            @if($item->isActive())
            <x-menus-item :item="$item" class="py-2 px-16 block text-sm text-gray-600 hover:bg-blue-500 hover:text-white bg-gray-200 text-gray-700 border-r-4 border-gray-700" />
            @else
            <x-menus-item :item="$item" class="py-2 px-16 block text-sm text-gray-600 hover:bg-blue-500 hover:text-white" />
            @endif
        @endif
    @endforeach
</div>

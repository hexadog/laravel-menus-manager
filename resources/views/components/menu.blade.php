<nav {{ $attributes }}>
@foreach($items as $item)
    @if($item->isDivider())
        <x-menus-divider :item="$item" class="border-0 bg-gray-500 text-gray-500 h-px" />
    @else
        @if($item->isActive())
        <x-menus-item :item="$item" class="w-full flex justify-between items-center py-3 px-6 text-gray-600 cursor-pointer hover:bg-gray-100 hover:text-gray-700 focus:outline-none bg-gray-200 text-gray-700 border-r-4 border-gray-700" />
        @else
        <x-menus-item :item="$item" class="w-full flex justify-between items-center py-3 px-6 text-gray-600 cursor-pointer hover:bg-gray-100 hover:text-gray-700 focus:outline-none" />
        @endif
    @endif
@endforeach
</nav>

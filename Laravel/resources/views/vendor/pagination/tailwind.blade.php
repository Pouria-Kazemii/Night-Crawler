@if ($paginator->hasPages())
    <nav role="navigation" aria-label="ناوبری صفحه" class="mt-8 w-full">
        {{-- Pagination Info --}}
        <div class="text-center mb-4 text-sm text-black w-full">
            نمایش
            @if ($paginator->firstItem())
                <span class="font-bold">{{ $paginator->firstItem() }}</span>
                تا
                <span class="font-bold">{{ $paginator->lastItem() }}</span>
                از
            @endif
            <span class="font-bold">{{ $paginator->total() }}</span>
            نتیجه
        </div>

        {{-- Paginator --}}
        <div class="w-full flex justify-center">
            <div class="inline-flex rtl:flex-row-reverse flex-wrap gap-1">
                {{-- Previous --}}
                @if ($paginator->onFirstPage())
                    <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 border border-gray-300 rounded-md">
                        قبلی
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}"
                       class="px-3 py-2 text-sm text-white bg-red-600 border border-red-600 rounded-md hover:bg-red-700">
                        قبلی
                    </a>
                @endif

                {{-- Page Numbers --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-3 py-2 text-sm text-gray-500 bg-white border border-gray-300 rounded-md">
                            {{ $element }}
                        </span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="px-3 py-2 text-sm font-bold text-white bg-gray-800 border border-gray-800 rounded-md">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                   class="px-3 py-2 text-sm text-white bg-red-600 border border-red-600 rounded-md hover:bg-red-700">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}"
                       class="px-3 py-2 text-sm text-white bg-red-600 border border-red-600 rounded-md hover:bg-red-700">
                        بعدی
                    </a>
                @else
                    <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 border border-gray-300 rounded-md">
                        بعدی
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif

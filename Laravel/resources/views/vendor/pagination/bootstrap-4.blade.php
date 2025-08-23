@if ($paginator->hasPages())
    <nav class="flex items-center justify-center space-x-1 rtl:space-x-reverse mt-4">
        <ul class="inline-flex items-center gap-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span class="px-3 py-1 bg-gray-200 text-gray-500 rounded cursor-not-allowed">
                        &lsaquo;
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                       class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition">
                        &lsaquo;
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- Dots --}}
                @if (is_string($element))
                    <li>
                        <span class="px-3 py-1 bg-gray-200 text-gray-500 rounded">
                            {{ $element }}
                        </span>
                    </li>
                @endif

                {{-- Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span class="px-3 py-1 bg-black text-white rounded font-bold">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}"
                                   class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                       class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition">
                        &rsaquo;
                    </a>
                </li>
            @else
                <li>
                    <span class="px-3 py-1 bg-gray-200 text-gray-500 rounded cursor-not-allowed">
                        &rsaquo;
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
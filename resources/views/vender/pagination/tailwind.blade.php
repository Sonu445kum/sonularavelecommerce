@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center mt-6">
        <ul class="inline-flex items-center space-x-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="px-3 py-2 text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">Prev</li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                        class="px-3 py-2 bg-white border rounded-md hover:bg-blue-50 text-blue-600">Prev</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="px-3 py-2 text-gray-500">{{ $element }}</li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="px-3 py-2 bg-blue-600 text-white rounded-md">{{ $page }}</li>
                        @else
                            <li>
                                <a href="{{ $url }}" class="px-3 py-2 bg-white border rounded-md hover:bg-blue-50 text-blue-600">
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
                        class="px-3 py-2 bg-white border rounded-md hover:bg-blue-50 text-blue-600">Next</a>
                </li>
            @else
                <li class="px-3 py-2 text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">Next</li>
            @endif
        </ul>
    </nav>
@endif

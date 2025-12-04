@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center gap-1 select-none">
        
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span aria-disabled="true" aria-label="@lang('pagination.previous')">
                <span class="relative inline-flex items-center justify-center px-3 py-2 text-sm font-bold border rounded-md cursor-default leading-5 bg-white text-gray-300 border-gray-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center justify-center px-3 py-2 text-sm font-bold border rounded-md leading-5 bg-white text-gray-700 hover:text-gray-500 border-gray-300 transition duration-150 ease-in-out" aria-label="@lang('pagination.previous')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
        @endif

        @php
            $start = 1;
            $end = $paginator->lastPage();
            $current = $paginator->currentPage();
            $window = 1; // Shows 1 page before and 1 page after current
            $lastPrinted = 0;
        @endphp

        @for ($i = $start; $i <= $end; $i++)
            {{-- Logic: Always print First (1), Last ($end), and pages within Window of Current ($current +/- 1) --}}
            @if ($i == $start || $i == $end || ($i >= $current - $window && $i <= $current + $window))
                
                {{-- Print Ellipsis if there is a gap > 1 from the last printed number --}}
                @if ($lastPrinted > 0 && $i - $lastPrinted > 1)
                    <span aria-disabled="true">
                        <span class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-bold text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">...</span>
                    </span>
                @endif

                {{-- Print Number --}}
                @if ($i == $current)
                    <span aria-current="page">
                        <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-bold text-white bg-red-600 border border-red-600 cursor-default leading-5 rounded-md">{{ $i }}</span>
                    </span>
                @else
                    <a href="{{ $paginator->url($i) }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-bold text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 hover:bg-gray-50 focus:z-10 focus:outline-none active:bg-gray-100 transition ease-in-out duration-150 rounded-md">
                        {{ $i }}
                    </a>
                @endif

                @php $lastPrinted = $i; @endphp
            @endif
        @endfor

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center justify-center px-3 py-2 text-sm font-bold border rounded-md leading-5 bg-white text-gray-700 hover:text-gray-500 border-gray-300 transition duration-150 ease-in-out" aria-label="@lang('pagination.next')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        @else
            <span aria-disabled="true" aria-label="@lang('pagination.next')">
                <span class="relative inline-flex items-center justify-center px-3 py-2 text-sm font-bold border rounded-md cursor-default leading-5 bg-white text-gray-300 border-gray-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </span>
            </span>
        @endif
    </nav>
@endif
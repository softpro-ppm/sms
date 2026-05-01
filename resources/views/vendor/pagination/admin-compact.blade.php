@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $hasLastPage = method_exists($paginator, 'lastPage');
        $lastPage = $hasLastPage ? $paginator->lastPage() : $currentPage + 2;
        $startPage = max(1, min($currentPage - 1, max(1, $lastPage - 2)));
        $endPage = min($lastPage, $startPage + 2);
    @endphp

    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1">
        @for ($page = $startPage; $page <= $endPage; $page++)
            @if ($page == $currentPage)
                <span aria-current="page"
                      class="px-3 py-1.5 text-sm rounded-md border bg-primary-600 text-white border-primary-600">
                    {{ $page }}
                </span>
            @else
                <a href="{{ $paginator->url($page) }}"
                   class="px-3 py-1.5 text-sm rounded-md border bg-white text-gray-700 border-gray-300 hover:bg-gray-50">
                    {{ $page }}
                </a>
            @endif
        @endfor

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               rel="next"
               class="px-3 py-1.5 text-sm rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                Next
            </a>
        @endif
    </nav>
@endif

@if ($paginator->hasPages())
    <nav class="pg-nav" role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        <div class="pg-bar">
            @if ($paginator->onFirstPage())
                <span class="pg-btn pg-btn--disabled" aria-disabled="true">
                    <span class="pg-btn__icon" aria-hidden="true">‹</span>
                    <span class="pg-btn__label">Previous</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="pg-btn pg-btn--ghost" rel="prev">
                    <span class="pg-btn__icon" aria-hidden="true">‹</span>
                    <span class="pg-btn__label">Previous</span>
                </a>
            @endif

            <span class="pg-status">
                Page <strong>{{ $paginator->currentPage() }}</strong> of <strong>{{ $paginator->lastPage() }}</strong>
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pg-btn pg-btn--primary" rel="next">
                    <span class="pg-btn__label">Next</span>
                    <span class="pg-btn__icon" aria-hidden="true">›</span>
                </a>
            @else
                <span class="pg-btn pg-btn--disabled" aria-disabled="true">
                    <span class="pg-btn__label">Next</span>
                    <span class="pg-btn__icon" aria-hidden="true">›</span>
                </span>
            @endif
        </div>

        <p class="pg-meta">
            @if ($paginator->firstItem())
                Showing <strong>{{ $paginator->firstItem() }}</strong> to <strong>{{ $paginator->lastItem() }}</strong> of <strong>{{ $paginator->total() }}</strong> entries
            @else
                <strong>{{ $paginator->total() }}</strong> entries
            @endif
        </p>

        <div class="pg-chips" role="list">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pg-chip pg-chip--ellipsis" role="presentation">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pg-chip pg-chip--current" role="listitem" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pg-chip" role="listitem" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>
    </nav>
@endif

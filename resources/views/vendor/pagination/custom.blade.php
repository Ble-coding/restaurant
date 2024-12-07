@if ($paginator->hasPages())
    <div class="pagination-container">
        <ul class="pagination">
            {{-- Page précédente --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <a class="page-link" href="#">←</a>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">←</a>
                </li>
            @endif

            {{-- Liens vers les pages --}}
            @foreach ($elements as $element)
                {{-- Séparateurs de pages --}}
                @if (is_string($element))
                    <li class="page-item disabled">
                        <span class="page-link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Pages numérotées --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Page suivante --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">→</a>
                </li>
            @else
                <li class="page-item disabled">
                    <a class="page-link" href="#">→</a>
                </li>
            @endif
        </ul>
    </div>
@endif

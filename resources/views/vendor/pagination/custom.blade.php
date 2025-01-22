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

                {{-- Pages numérotées avec ellipses --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        {{-- Toujours afficher la première et la dernière page --}}
                        @if ($page == 1 || $page == $paginator->lastPage())
                            <li class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}">
                                @if ($page == $paginator->currentPage())
                                    <span class="page-link">{{ $page }}</span>
                                @else
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                @endif
                            </li>
                        {{-- Afficher les pages autour de la page active --}}
                        @elseif (
                            $page >= $paginator->currentPage() - 1 &&
                            $page <= $paginator->currentPage() + 1
                        )
                            <li class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}">
                                @if ($page == $paginator->currentPage())
                                    <span class="page-link">{{ $page }}</span>
                                @else
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                @endif
                            </li>
                        {{-- Ajouter des ellipses si nécessaire --}}
                        @elseif ($page == 2 || $page == $paginator->lastPage() - 1)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
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

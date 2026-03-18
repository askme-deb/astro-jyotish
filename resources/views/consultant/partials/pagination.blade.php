@php
    $currentPage = max(1, (int) ($pagination['current_page'] ?? 1));
    $lastPage = max(1, (int) ($pagination['last_page'] ?? 1));
    $startPage = max(1, $currentPage - 2);
    $endPage = min($lastPage, $currentPage + 2);
@endphp

@if($lastPage > 1)
    <div class="container py-5 text-center">
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item {{ $currentPage === 1 ? 'disabled' : '' }}">
                    <button
                        class="page-link consultant-pagination-link"
                        type="button"
                        data-page="{{ max(1, $currentPage - 1) }}"
                        {{ $currentPage === 1 ? 'disabled' : '' }}
                    >
                        Previous
                    </button>
                </li>

                @for($page = $startPage; $page <= $endPage; $page++)
                    <li class="page-item {{ $page === $currentPage ? 'active' : '' }}">
                        <button
                            class="page-link consultant-pagination-link"
                            type="button"
                            data-page="{{ $page }}"
                            {{ $page === $currentPage ? 'disabled' : '' }}
                        >
                            {{ $page }}
                        </button>
                    </li>
                @endfor

                <li class="page-item {{ $currentPage === $lastPage ? 'disabled' : '' }}">
                    <button
                        class="page-link consultant-pagination-link"
                        type="button"
                        data-page="{{ min($lastPage, $currentPage + 1) }}"
                        {{ $currentPage === $lastPage ? 'disabled' : '' }}
                    >
                        Next
                    </button>
                </li>
            </ul>
        </nav>
    </div>
@endif

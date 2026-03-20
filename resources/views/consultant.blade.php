@extends('layouts.app')

@section('title', 'Consultant')

@push('head')
<style>
    .consultant-skeleton-grid {
        display: none;
    }

    .consultant-skeleton-grid.is-visible {
        display: flex;
    }

    .consultant-skeleton-card {
        background: #fff;
        border-radius: 18px;
        padding: 1.25rem;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        min-height: 272px;
    }

    .consultant-skeleton-line,
    .consultant-skeleton-avatar,
    .consultant-skeleton-badge,
    .consultant-skeleton-button {
        position: relative;
        overflow: hidden;
        background: #eceff3;
    }

    .consultant-skeleton-line::after,
    .consultant-skeleton-avatar::after,
    .consultant-skeleton-badge::after,
    .consultant-skeleton-button::after {
        content: '';
        position: absolute;
        inset: 0;
        transform: translateX(-100%);
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.9), transparent);
        animation: consultantSkeletonShimmer 1.2s ease-in-out infinite;
    }

    .consultant-skeleton-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .consultant-skeleton-line {
        height: 12px;
        border-radius: 999px;
        margin-bottom: 0.7rem;
    }

    .consultant-skeleton-line.title {
        width: 70%;
        height: 16px;
    }

    .consultant-skeleton-line.short {
        width: 42%;
    }

    .consultant-skeleton-line.medium {
        width: 58%;
    }

    .consultant-skeleton-badges {
        display: flex;
        gap: 0.45rem;
        margin: 0.85rem 0;
        flex-wrap: wrap;
    }

    .consultant-skeleton-badge {
        width: 68px;
        height: 24px;
        border-radius: 999px;
    }

    .consultant-skeleton-divider {
        height: 1px;
        background: #edf1f4;
        margin: 1rem 0;
    }

    .consultant-skeleton-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .consultant-skeleton-button {
        width: 140px;
        height: 38px;
        border-radius: 999px;
    }

    @keyframes consultantSkeletonShimmer {
        100% {
            transform: translateX(100%);
        }
    }
</style>
@endpush

@section('content')

@php
    $consultantPageData = [
        'filterUrl' => route('consultant.filter'),
        'perPage' => (int) ($pagination['per_page'] ?? 15),
    ];
@endphp


<div class="container mt-4 inner_back">

    <div class="banner">

        <!-- Background Image -->
        <img src="{{ asset('assets/images/consultation_banner.jpg') }}" alt="Astrology Banner">

        <!-- Overlay -->
        <div class="banner-overlay">

            <div class="banner-content">
                <h1>
                    Discover Your Destiny with <br>
                    Trusted <span>Astrologers</span>
                </h1>

                @php $isLoggedIn = session('auth.user') ? true : false; @endphp
                <button class="appointment-btn" onclick="@if(!$isLoggedIn) showAuthModal(); @else window.location.href='/consultation'; @endif">
                    Get an Appointment
                </button>
            </div>

        </div>

    </div>

</div>

<section class="sear_pat">
    <div class="container">
        <form id="consultantFilterForm">
            <div class="row">

                <div class="col-lg-10 col-md-6">
                    <div class="search_warp">
                        <div class="input-group">
                            <input type="text" name="search" id="searchInput" class="form-control search-input" placeholder="Search Astrologer...">
                            <button class="btn btn-primary search-btn" type="button" id="searchButton">Search</button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="filter_warp">
                        <button class="btn btn-warning" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
                            Filters
                        </button>


                        <div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">

                                    <div class="modal-body position-relative">

                                        <button class="close-btn" type="button" data-bs-dismiss="modal">&times;</button>

                                        <div class="filter-title">Filters</div>

                                        <div class="filter-wrapper">

                                            <div class="left-tab">
                                                @foreach($filters as $index => $section)
                                                    <button
                                                        class="tab-btn {{ $index === 0 ? 'active' : '' }}"
                                                        type="button"
                                                        data-tab="{{ $section['key'] }}"
                                                    >
                                                        {{ $section['label'] }}
                                                    </button>
                                                @endforeach
                                            </div>

                                            <div class="right-tab">
                                                @foreach($filters as $index => $section)
                                                    <div
                                                        class="tab-content {{ $index === 0 ? 'active' : 'd-none' }}"
                                                        id="{{ $section['key'] }}"
                                                        data-single-select="{{ !empty($section['single_select']) ? 'true' : 'false' }}"
                                                    >
                                                        @forelse($section['options'] as $option)
                                                            @php
                                                                $inputId = $section['key'] . '-' . \Illuminate\Support\Str::slug($option['value'] . '-' . $loop->index);
                                                            @endphp
                                                            <div class="form-check">
                                                                <input
                                                                    class="form-check-input"
                                                                    id="{{ $inputId }}"
                                                                    type="checkbox"
                                                                    value="{{ $option['value'] }}"
                                                                    @if(isset($option['sort_by'])) data-sort-by="{{ $option['sort_by'] }}" @endif
                                                                    @if(isset($option['sort_order'])) data-sort-order="{{ $option['sort_order'] }}" @endif
                                                                    @if(isset($option['min_rate'])) data-min-rate="{{ $option['min_rate'] }}" @endif
                                                                    @if(isset($option['max_rate'])) data-max-rate="{{ $option['max_rate'] }}" @endif
                                                                >
                                                                <label class="form-check-label" for="{{ $inputId }}">{{ $option['label'] }}</label>
                                                            </div>
                                                        @empty
                                                            <div class="text-muted small">No options available.</div>
                                                        @endforelse
                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>

                                    </div>

                                    <div class="modal-footer-custom">
                                        <button class="btn btn-clear" type="button" id="clearFilters">Clear Filters</button>
                                        <button class="btn btn-apply" type="button" id="applyFilters">Apply</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</section>




<div class="container">

    <!-- Top Online Astrologers -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="section-title">Top Online Astrologers</div>
    </div>

    <div id="consultantLoading" class="row g-4 consultant-skeleton-grid" aria-hidden="true">
        @include('consultant.partials.skeletons', ['count' => 6])
    </div>
    <div id="consultantResults" class="row g-4">
        @include('consultant.partials.results', ['astrologers' => $astrologers])
    </div>
</div>

<div id="consultantPagination">
    @include('consultant.partials.pagination', ['pagination' => $pagination])
</div>




@push('scripts')
<script id="consultant-page-data" type="application/json">{!! json_encode($consultantPageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('consultantFilterForm');
    const resultsContainer = document.getElementById('consultantResults');
    const paginationContainer = document.getElementById('consultantPagination');
    const loadingIndicator = document.getElementById('consultantLoading');
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    const applyFiltersButton = document.getElementById('applyFilters');
    const clearFiltersButton = document.getElementById('clearFilters');
    const filterModalElement = document.getElementById('filterModal');
    const pageDataElement = document.getElementById('consultant-page-data');

    if (!form || !resultsContainer || !paginationContainer || !loadingIndicator || !pageDataElement) {
        return;
    }

    const pageData = JSON.parse(pageDataElement.textContent || '{}');
    const filterModal = filterModalElement ? bootstrap.Modal.getOrCreateInstance(filterModalElement) : null;

    document.querySelectorAll('.tab-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            const targetTab = this.dataset.tab;

            document.querySelectorAll('.tab-btn').forEach(function (tabButton) {
                tabButton.classList.toggle('active', tabButton === button);
            });

            document.querySelectorAll('#filterModal .tab-content').forEach(function (content) {
                const isActive = content.id === targetTab;
                content.classList.toggle('active', isActive);
                content.classList.toggle('d-none', !isActive);
            });
        });
    });

    form.querySelectorAll('.tab-content[data-single-select="true"] .form-check-input').forEach(function (input) {
        input.addEventListener('change', function () {
            if (!this.checked) {
                return;
            }

            const section = this.closest('.tab-content');
            if (!section) {
                return;
            }

            section.querySelectorAll('.form-check-input').forEach(function (checkbox) {
                if (checkbox !== input) {
                    checkbox.checked = false;
                }
            });

            if (input.dataset.sortBy) {
                form.querySelectorAll('.form-check-input[data-sort-by]').forEach(function (sortInput) {
                    if (sortInput !== input) {
                        sortInput.checked = false;
                    }
                });
            }
        });
    });

    function getCheckedValues(sectionId) {
        const section = document.getElementById(sectionId);
        if (!section) {
            return [];
        }

        return Array.from(section.querySelectorAll('.form-check-input:checked')).map(function (input) {
            return input.value;
        });
    }

    function getCheckedInput(sectionId) {
        const section = document.getElementById(sectionId);
        return section ? section.querySelector('.form-check-input:checked') : null;
    }

    function buildQuery(page) {
        const params = new URLSearchParams();
        const search = searchInput.value.trim();

        if (search !== '') {
            params.append('search', search);
        }

        getCheckedValues('languages').forEach(function (language) {
            params.append('languages[]', language);
        });

        getCheckedValues('expertise').forEach(function (expertise) {
            params.append('expertise[]', expertise);
        });

        const selectedPrice = getCheckedInput('price');
        if (selectedPrice) {
            if (selectedPrice.dataset.minRate) {
                params.append('min_rate', selectedPrice.dataset.minRate);
            }

            if (selectedPrice.dataset.maxRate) {
                params.append('max_rate', selectedPrice.dataset.maxRate);
            }
        }

        const selectedSort = getCheckedInput('rating') || getCheckedInput('experience');
        if (selectedSort && selectedSort.dataset.sortBy) {
            params.append('sort_by', selectedSort.dataset.sortBy);
            params.append('sort_order', selectedSort.dataset.sortOrder || 'desc');
        }

        params.append('per_page', pageData.perPage || 15);
        params.append('page', page || 1);

        return params;
    }

    async function loadConsultants(page, options) {
        const requestOptions = options || {};
        const query = buildQuery(page || 1).toString();

        loadingIndicator.classList.add('is-visible');
        resultsContainer.classList.add('d-none');
        paginationContainer.classList.add('d-none');

        try {
            const response = await window.axios.get(pageData.filterUrl + '?' + query);

            resultsContainer.innerHTML = response.data.html || '';
            paginationContainer.innerHTML = response.data.pagination || '';
            resultsContainer.classList.remove('d-none');
            paginationContainer.classList.remove('d-none');

            if (requestOptions.closeModal && filterModal) {
                filterModal.hide();
            }
        } catch (error) {
            resultsContainer.innerHTML = '<div class="col-12"><div class="alert alert-danger text-center">Unable to load astrologers right now.</div></div>';
            paginationContainer.innerHTML = '';
            resultsContainer.classList.remove('d-none');
            paginationContainer.classList.remove('d-none');
        } finally {
            loadingIndicator.classList.remove('is-visible');
        }
    }

    searchButton.addEventListener('click', function () {
        loadConsultants(1);
    });

    searchInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            loadConsultants(1);
        }
    });

    applyFiltersButton.addEventListener('click', function () {
        loadConsultants(1, { closeModal: true });
    });

    clearFiltersButton.addEventListener('click', function () {
        form.reset();
        loadConsultants(1, { closeModal: true });
    });

    paginationContainer.addEventListener('click', function (event) {
        const target = event.target.closest('.consultant-pagination-link');
        if (!target || target.hasAttribute('disabled')) {
            return;
        }

        event.preventDefault();
        const page = Number(target.dataset.page || 1);
        loadConsultants(page);
    });
});
</script>
@endpush
@endsection

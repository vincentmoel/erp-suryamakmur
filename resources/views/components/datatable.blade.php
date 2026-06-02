@props([
    'id',
    'searchPlaceholder' => 'Search...',
])

<div class="dt-card">

    {{-- Toolbar --}}
    <div class="dt-toolbar">
        <div class="dt-search-wrapper">
            <x-icon name="search" class="dt-search-icon" />
            <input type="text"
                   id="{{ $id }}-search"
                   placeholder="{{ $searchPlaceholder }}"
                   class="input dt-search-input dt-search-input--with-icon">
        </div>

        @isset($actions)
            <div class="dt-toolbar-actions">
                {{ $actions }}
            </div>
        @endisset
    </div>

    {{-- Table --}}
    <div class="dt-wrapper">
        <table id="{{ $id }}" class="dt-table">
            <thead>
                <tr>{{ $head }}</tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="dt-footer">
        <p id="{{ $id }}-info" class="dt-info-text">Loading...</p>

        <div class="dt-footer-controls">

            {{-- Per-page select --}}
            <div class="dt-per-page">
                <span class="dt-per-page-label">Rows</span>
                <div class="dt-per-page-select">
                    <button type="button"
                            id="{{ $id }}-per-page-trigger"
                            class="select-trigger"
                            data-size="xs"
                            aria-expanded="false"
                            aria-haspopup="listbox">
                        <span id="{{ $id }}-per-page-value">10</span>
                        <x-icon name="chevron-down" class="size-3.5" />
                    </button>
                    <div id="{{ $id }}-per-page-content"
                         class="select-content hidden"
                         role="listbox">
                        <div class="select-item" role="option" aria-selected="true"  data-value="10">10</div>
                        <div class="select-item" role="option" aria-selected="false" data-value="25">25</div>
                        <div class="select-item" role="option" aria-selected="false" data-value="50">50</div>
                        <div class="select-item" role="option" aria-selected="false" data-value="100">100</div>
                    </div>
                </div>
            </div>

            {{-- Page info --}}
            <span id="{{ $id }}-page-info" class="dt-page-info" style="display:none;">
                Page 1 of 1
            </span>

            {{-- Pagination --}}
            <div id="{{ $id }}-pagination" class="dt-pagination"></div>

        </div>
    </div>

</div>

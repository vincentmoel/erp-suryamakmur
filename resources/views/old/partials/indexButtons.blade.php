@if (Request::segment(2) == 'trashed')
    @if (isset($indexRoute) && (Session::get('permissions')[$module]['read'] ?? false))
        <a href="{{ $indexRoute }}" class="btn btn-danger me-1"><i class="ti ti-arrow-left me-1"></i>All Data</a>
    @endif
@else
    @if (isset($createRoute) && (Session::get('permissions')[$module]['create'] ?? false))
        <a href="{{ $createRoute }}" class="btn btn-primary me-1"><i class="ti ti-plus me-1"></i>Create Data</a>
    @endif

    @if (isset($importFeature) && (Session::get('permissions')[$module]['create'] ?? false))
        <button class="btn btn-dark me-1" data-bs-toggle="modal" data-bs-target="#import-modal"><i class="ti ti-file-import me-1"></i>Import</button>
    @endif

    @if (isset($exportFeature) && (Session::get('permissions')[$module]['read'] ?? false))
        <button class="btn btn-secondary me-1" data-bs-toggle="modal" data-bs-target="#export-modal"><i class="ti ti-file-export me-1"></i>Export</button>
    @endif

    @if (isset($restoreRoute) && (Session::get('permissions')[$module]['restore'] ?? false))
        <a href="{{ $restoreRoute }}" class="btn btn-danger me-1"><i class="ti ti-history me-1"></i>Restore</a>
    @endif
@endif
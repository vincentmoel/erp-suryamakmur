@extends('layouts.main', ['title' => 'Users | ' . config('app.name')])

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
@endpush

@section('content')
<div style="display:flex;flex-direction:column;gap:1.5rem;">

    {{-- Page Header --}}
    <div style="border-bottom:1px solid var(--border);padding-bottom:0.75rem;display:flex;flex-direction:column;gap:0.25rem;">
        <h1 style="font-size:1.125rem;font-weight:600;letter-spacing:-0.015em;line-height:1.3;">Users</h1>
        <p style="font-size:0.75rem;color:var(--muted-foreground);">Manage user accounts and permissions.</p>
    </div>

    {{-- DataTable Card --}}
    <div style="display:flex;flex-direction:column;border-radius:var(--radius);border:1px solid var(--border);
                background-color:var(--card);color:var(--card-foreground);
                box-shadow:0 1px 2px 0 rgb(0 0 0/.05);overflow:hidden;">

        {{-- Toolbar --}}
        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:0.5rem;
                    border-bottom:1px solid var(--border);padding:0.75rem;">
            <input type="text" id="dt-search" placeholder="Search name or username..."
                   class="input" style="height:2rem;max-width:20rem;font-size:0.875rem;">

            <div style="margin-left:auto;display:flex;align-items:center;gap:0.5rem;">
                <a href="{{ route('users.create') }}" class="btn btn-primary"
                   style="height:2rem;font-size:0.875rem;padding-inline:0.75rem;gap:0.375rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.5" style="width:0.875rem;height:0.875rem;flex-shrink:0;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Add User
                </a>
            </div>
        </div>

        {{-- Table --}}
        <div class="dt-wrapper" style="overflow-x:auto;">
            <table id="users-table" class="dt-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Last Seen</th>
                        <th>Created At</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;
                    gap:0.75rem;border-top:1px solid var(--border);padding:0.75rem;">

            <p id="dt-info" style="font-size:0.75rem;color:var(--muted-foreground);margin:0;">Loading...</p>

            <div style="display:flex;align-items:center;gap:0.75rem;">

                {{-- Rows per page (Custom Select) --}}
                <div style="display:flex;align-items:center;gap:0.5rem;">
                    <span style="font-size:0.75rem;color:var(--muted-foreground);">Rows</span>
                    <div style="position:relative;width:4rem;">
                        <button type="button" id="dt-per-page-trigger" class="select-trigger" data-size="xs"
                                aria-expanded="false" aria-haspopup="listbox">
                            <span id="dt-per-page-value">10</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" style="width:0.875rem;height:0.875rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                            </svg>
                        </button>
                        <div id="dt-per-page-content" class="select-content hidden" role="listbox">
                            <div class="select-item" role="option" aria-selected="true"  data-value="10">10</div>
                            <div class="select-item" role="option" aria-selected="false" data-value="25">25</div>
                            <div class="select-item" role="option" aria-selected="false" data-value="50">50</div>
                            <div class="select-item" role="option" aria-selected="false" data-value="100">100</div>
                        </div>
                    </div>
                </div>

                {{-- Page info --}}
                <span id="dt-page-info"
                      style="font-size:0.75rem;color:var(--muted-foreground);white-space:nowrap;display:none;">
                    Page 1 of 1
                </span>

                {{-- Pagination --}}
                <div id="dt-pagination" style="display:flex;align-items:center;gap:0.25rem;"></div>

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script>
$(function () {

    // ── Init DataTable ───────────────────────────────────────
    const dt = $('#users-table').DataTable({
        processing : true,
        serverSide : true,
        ajax       : { url: '{{ route('users.index') }}', type: 'GET' },
        dom        : 'rt',
        pageLength : 10,
        columns: [
            {
                data: 'DT_RowIndex', name: 'DT_RowIndex',
                orderable: false, searchable: false,
                render: (d) => `<span style="font-size:.75rem;color:var(--muted-foreground);">${d}</span>`,
            },
            {
                data: 'name', name: 'name',
                render: (d) => `<span style="font-size:.875rem;font-weight:450;">${d}</span>`,
            },
            {
                data: 'username', name: 'username',
                render: (d) => `<span style="font-size:.875rem;color:var(--muted-foreground);">${d}</span>`,
            },
            { data: 'last_seen',  name: 'last_seen',  orderable: false, searchable: false },
            {
                data: 'created_at', name: 'created_at',
                render: (d) => `<span style="font-size:.875rem;color:var(--muted-foreground);">${d}</span>`,
            },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        language: {
            processing  : 'Loading...',
            emptyTable  : `<span style="color:var(--muted-foreground);">No users found.</span>`,
            zeroRecords : `<span style="color:var(--muted-foreground);">No matching users found.</span>`,
        },
        drawCallback() { updateFooter(this.api()); },
    });

    // ── Search (debounced) ───────────────────────────────────
    let searchTimer;
    $('#dt-search').on('input', function () {
        clearTimeout(searchTimer);
        const q = this.value;
        searchTimer = setTimeout(() => dt.search(q).draw(), 350);
    });

    // ── Per page: custom select ──────────────────────────────
    const $trigger = $('#dt-per-page-trigger');
    const $content = $('#dt-per-page-content');
    const $label   = $('#dt-per-page-value');

    $trigger.on('click', function (e) {
        e.stopPropagation();
        const opening = $content.hasClass('hidden');
        $content.toggleClass('hidden', !opening);
        $trigger.attr('aria-expanded', opening);
        if (opening) {
            const r = this.getBoundingClientRect();
            $content.css({ top: r.bottom + 4, left: r.left, width: Math.max(r.width, 80) });
        }
    });

    $content.on('click', '.select-item', function () {
        const val = $(this).data('value');
        $content.find('.select-item').attr('aria-selected', 'false');
        $(this).attr('aria-selected', 'true');
        $label.text(val);
        $content.addClass('hidden');
        $trigger.attr('aria-expanded', 'false');
        dt.page.len(Number(val)).draw();
    });

    $(document).on('click.dt-perpage', () => {
        $content.addClass('hidden');
        $trigger.attr('aria-expanded', 'false');
    });

    // ── Footer ───────────────────────────────────────────────
    function updateFooter(api) {
        const info = api.page.info();
        $('#dt-info').text(`${info.recordsDisplay} row(s) total`);
        $('#dt-page-info')
            .text(`Page ${info.page + 1} of ${info.pages || 1}`)
            .css('display', window.innerWidth >= 640 ? 'inline' : 'none');
        renderPagination(api, info);
    }

    function renderPagination(api, info) {
        const $pag    = $('#dt-pagination').empty();
        const current = info.page;
        const total   = info.pages || 1;
        const ellipsis = '<span style="display:inline-flex;align-items:center;justify-content:center;width:1.75rem;height:1.75rem;font-size:.75rem;color:var(--muted-foreground);">…</span>';

        // Prev
        const $prev = $('<button class="dt-page-btn">&lsaquo;</button>');
        if (current === 0) $prev.prop('disabled', true);
        else $prev.on('click', () => api.page('previous').draw('page'));
        $pag.append($prev);

        // Pages
        pageRange(current + 1, total, 5).forEach(p => {
            if (p === '...') { $pag.append(ellipsis); return; }
            const $b = $(`<button class="dt-page-btn">${p}</button>`);
            if (p === current + 1) $b.addClass('active');
            $b.on('click', () => api.page(p - 1).draw('page'));
            $pag.append($b);
        });

        // Next
        const $next = $('<button class="dt-page-btn">&rsaquo;</button>');
        if (current >= total - 1) $next.prop('disabled', true);
        else $next.on('click', () => api.page('next').draw('page'));
        $pag.append($next);
    }

    function pageRange(current, total, max) {
        if (total <= max) return Array.from({ length: total }, (_, i) => i + 1);
        const half  = Math.floor(max / 2);
        let   start = Math.max(1, current - half);
        let   end   = Math.min(total, start + max - 1);
        if (end - start < max - 1) start = Math.max(1, end - max + 1);
        const pages = [];
        if (start > 1)    { pages.push(1); if (start > 2) pages.push('...'); }
        for (let i = start; i <= end; i++) pages.push(i);
        if (end < total)  { if (end < total - 1) pages.push('...'); pages.push(total); }
        return pages;
    }

});
</script>
@endpush

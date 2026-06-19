@extends('layouts.main', ['title' => __('general.invoices')])

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
@endpush

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>@lang('general.invoices')</h1>
            <p>@lang('general.invoices_desc')</p>
        </div>

        <x-datatable id="invoices-table" :search-placeholder="__('general.search')">

            <x-slot name="filters">
                <x-form.single-select
                    name="filter_customer"
                    placeholder="All Customers"
                    :searchable="true"
                    :options="$customers->map(fn($c) => ['value' => $c->id, 'label' => $c->name, 'badge' => $c->trashed() ? 'Deleted' : null])->toArray()" />
                <x-form.single-select
                    name="filter_salesperson"
                    placeholder="All Salespersons"
                    :searchable="true"
                    :options="$salespersons->map(fn($s) => ['value' => $s->id, 'label' => $s->name, 'badge' => $s->trashed() ? 'Deleted' : null])->toArray()" />
                <x-form.daterange
                    name-from="filter_date_from"
                    name-to="filter_date_to"
                    placeholder="All Invoice Dates" />
                <x-form.single-select
                    name="filter_status"
                    placeholder="All Statuses"
                    :searchable="false"
                    :options="collect($statuses)->map(fn($s) => ['value' => $s->value, 'label' => $s->label()])->toArray()" />
            </x-slot>

            <x-slot name="actions">
                @if(app(\App\Services\PermissionService::class)->has('Invoice', 'create'))
                <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">
                    <x-icon name="plus" /> @lang('general.add_invoice')
                </a>
                @endif
            </x-slot>

            <x-slot name="head">
                <th>#</th>
                <th>@lang('general.code')</th>
                <th>@lang('general.customer')</th>
                <th>@lang('general.salesperson')</th>
                <th>@lang('general.invoice_date')</th>
                <th>@lang('general.total')</th>
                <th>@lang('general.paid_amount')</th>
                <th>@lang('general.status')</th>
                <th>@lang('general.created_at')</th>
                <th></th>
            </x-slot>

        </x-datatable>

    </div>

    <iframe id="invoice-print-frame"
            style="position:fixed;left:-9999px;top:-9999px;width:0;height:0;border:0;"
            tabindex="-1"
            aria-hidden="true"></iframe>

    {{-- Invoice Preview Drawer --}}
    <div id="invoice-drawer-overlay"
     class="fixed inset-0 bg-black/40 z-40 hidden transition-opacity duration-200 opacity-0"
     onclick="closeInvoiceDrawer()"></div>

<div id="invoice-drawer"
     style="display:none; transform: translateX(100%);"
     class="fixed top-0 right-0 h-full w-full max-w-3xl z-50 bg-background border-l border-border shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">

    {{-- Drawer Header --}}
    <div class="flex items-center justify-between px-6 py-4 border-b border-border shrink-0">
        <div>
            <p class="text-xs text-muted-foreground uppercase tracking-wider">{{ __('general.invoice') }}</p>
            <div class="flex items-center gap-2 mt-0.5">
                <h3 id="drawer-invoice-code" class="text-lg font-bold font-mono">—</h3>
                <span id="drawer-status-badge"></span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a id="drawer-show-link" href="#" class="btn btn-ghost btn-sm" title="Buka halaman detail">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                </svg>
            </a>
            <button onclick="closeInvoiceDrawer()" class="btn btn-ghost btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Drawer Body --}}
    <div id="drawer-body" class="flex-1 overflow-y-auto px-6 py-5">
        <div id="drawer-content">
            {{-- diisi via AJAX --}}
        </div>
        <div id="drawer-loading" style="display:none;" class="items-center justify-center h-40">
            <svg class="animate-spin size-6 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
        </div>
    </div>

    {{-- Drawer Footer: action buttons --}}
    <div class="shrink-0 border-t border-border px-6 py-4 flex items-center gap-2">
        <a id="drawer-btn-download" href="#" target="_blank" class="btn btn-primary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
            </svg>
            @lang('general.download_pdf')
        </a>
        <button id="drawer-btn-print" type="button" class="btn btn-secondary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
            </svg>
            @lang('general.print')
        </button>
        <a id="drawer-btn-send" href="#" class="btn btn-secondary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
            </svg>
            @lang('general.send')
        </a>
    </div>

</div>

@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="{{ asset('src/js/datatable.js') }}"></script>
    <script>
        const dt = initDataTable({
            tableId: 'invoices-table',
            ajaxUrl: '{{ route('invoices.index') }}',
            order: [{ name: 'created_at', dir: 'desc' }],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'dt-cell-index dt-center' },
                { data: 'code', name: 'code' },
                { data: 'customer_id', name: 'customer_id', orderable: false },
                { data: 'salesperson_id', name: 'salesperson_id', orderable: false, className: 'dt-cell-muted' },
                { data: 'invoice_date', name: 'invoice_date', className: 'dt-cell-muted' },
                { data: 'amount', name: 'amount' },
                { data: 'paid_amount', name: 'paid_amount', orderable: false, searchable: false },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at', className: 'dt-cell-muted' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });

        dt.settings()[0].ajax.data = function (d) {
            d.filter_customer    = document.querySelector('[name="filter_customer"]').value;
            d.filter_salesperson = document.querySelector('[name="filter_salesperson"]').value;
            d.filter_date_from   = document.querySelector('[name="filter_date_from"]').value;
            d.filter_date_to     = document.querySelector('[name="filter_date_to"]').value;
            d.filter_status      = document.querySelector('[name="filter_status"]').value;
        };

        document.querySelectorAll('[name="filter_customer"], [name="filter_salesperson"], [name="filter_status"], [name="filter_date_from"]')
            .forEach(function (el) { el.addEventListener('change', function () { dt.ajax.reload(); }); });

        document.querySelector('#invoices-table').addEventListener('click', function (e) {
            if (e.target.closest('a, button')) return;
            const row = e.target.closest('tr[data-show-url]');
            if (row) openInvoiceDrawer(row.dataset.showUrl, row.dataset.previewUrl);
        });

        function openInvoiceDrawer(showUrl, previewUrl) {
            const overlay  = document.getElementById('invoice-drawer-overlay');
            const drawer   = document.getElementById('invoice-drawer');
            const content  = document.getElementById('drawer-content');
            const loading  = document.getElementById('drawer-loading');
            const codeEl   = document.getElementById('drawer-invoice-code');

            content.innerHTML = '';
            codeEl.textContent = '—';
            document.getElementById('drawer-status-badge').textContent = '';
            document.getElementById('drawer-status-badge').className = '';
            loading.style.display = 'flex';

            overlay.classList.remove('hidden');
            drawer.style.display = 'flex';
            requestAnimationFrame(() => requestAnimationFrame(() => {
                overlay.style.opacity = '1';
                drawer.style.transform = 'translateX(0)';
            }));

            document.addEventListener('keydown', onEscKey);

            fetch(previewUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    loading.style.display = 'none';
                    content.innerHTML = data.html;
                    codeEl.textContent = data.code;
                    const badge = document.getElementById('drawer-status-badge');
                    badge.className = 'badge ' + data.status_badge_class;
                    badge.textContent = data.status_label;
                    document.getElementById('drawer-show-link').href    = data.show_url;
                    document.getElementById('drawer-btn-download').href = data.pdf_url;
                    const printFrame = document.getElementById('invoice-print-frame');
                    printFrame.src = data.print_url;
                    document.getElementById('drawer-btn-print').onclick = function () {
                        if (printFrame.contentDocument && printFrame.contentDocument.readyState === 'complete') {
                            printFrame.contentWindow.print();
                        } else {
                            printFrame.onload = () => printFrame.contentWindow.print();
                        }
                    };
                    document.getElementById('drawer-btn-send').href     = 'mailto:' + data.email + '?subject=' + data.send_subject;
                })
                .catch(() => {
                    loading.style.display = 'none';
                    content.innerHTML = '<p class="text-sm text-destructive">Gagal memuat invoice.</p>';
                });
        }

        function closeInvoiceDrawer() {
            const overlay = document.getElementById('invoice-drawer-overlay');
            const drawer  = document.getElementById('invoice-drawer');
            overlay.style.opacity = '0';
            drawer.style.transform = 'translateX(100%)';
            setTimeout(() => {
                overlay.classList.add('hidden');
                drawer.style.display = 'none';
            }, 300);
            document.removeEventListener('keydown', onEscKey);
        }

        function onEscKey(e) { if (e.key === 'Escape') closeInvoiceDrawer(); }
    </script>
@endpush

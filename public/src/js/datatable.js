/**
 * DataTable Helper
 * Reusable wrapper di atas DataTables.js yang menyambungkan
 * custom toolbar, per-page select, footer, dan pagination
 * ke design system yang sudah ada.
 *
 * Dependency: jQuery, DataTables.js
 *
 * @param {Object} config
 * @param {string}   config.tableId          - ID element <table> tanpa #
 * @param {string}   config.ajaxUrl          - URL endpoint server-side
 * @param {Array}    config.columns          - Definisi kolom DataTables
 * @param {number}  [config.pageLength=10]   - Jumlah baris default per halaman
 * @param {Object}  [config.language={}]     - Override language DataTables
 * @returns {DataTables.Api}
 */
window.initDataTable = function (config) {
    const {
        tableId,
        ajaxUrl,
        columns,
        pageLength = 10,
        language   = {},
    } = config;

    // ── Init ──────────────────────────────────────────────────
    const dt = $(`#${tableId}`).DataTable({
        processing : true,
        serverSide : true,
        ajax       : { url: ajaxUrl, type: 'GET' },
        dom        : 'rt',
        pageLength,
        columns,
        language: {
            processing  : 'Loading...',
            emptyTable  : '<span class="dt-cell-muted">No data found.</span>',
            zeroRecords : '<span class="dt-cell-muted">No matching records found.</span>',
            ...language,
        },
        drawCallback() { _updateFooter(this.api()); },
    });

    // ── Search (debounced 350ms) ───────────────────────────────
    let _searchTimer;
    $(`#${tableId}-search`).on('input', function () {
        clearTimeout(_searchTimer);
        const q = this.value;
        _searchTimer = setTimeout(() => dt.search(q).draw(), 350);
    });

    // ── Per-page custom select ────────────────────────────────
    const $trigger = $(`#${tableId}-per-page-trigger`);
    const $content = $(`#${tableId}-per-page-content`);
    const $label   = $(`#${tableId}-per-page-value`);

    $trigger.on('click', function (e) {
        e.stopPropagation();
        const opening = $content.hasClass('hidden');
        $content.toggleClass('hidden', !opening);
        $trigger.attr('aria-expanded', String(opening));
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

    // Namespace event agar tidak konflik antar tabel
    $(document).on(`click.dt-perpage-${tableId}`, () => {
        $content.addClass('hidden');
        $trigger.attr('aria-expanded', 'false');
    });

    // ── Footer ────────────────────────────────────────────────
    function _updateFooter(api) {
        const info = api.page.info();

        $(`#${tableId}-info`).text(`${info.recordsDisplay} row(s) total`);

        $(`#${tableId}-page-info`)
            .text(`Page ${info.page + 1} of ${info.pages || 1}`)
            .toggle(window.innerWidth >= 640);

        _renderPagination(api, info);
    }

    function _renderPagination(api, info) {
        const $pag    = $(`#${tableId}-pagination`).empty();
        const current = info.page;
        const total   = info.pages || 1;

        const $prev = $('<button class="dt-page-btn">&lsaquo;</button>');
        if (current === 0) $prev.prop('disabled', true);
        else $prev.on('click', () => api.page('previous').draw('page'));
        $pag.append($prev);

        _pageRange(current + 1, total, 5).forEach(p => {
            if (p === '...') {
                $pag.append('<span class="dt-ellipsis">…</span>');
                return;
            }
            const $b = $(`<button class="dt-page-btn">${p}</button>`);
            if (p === current + 1) $b.addClass('active');
            $b.on('click', () => api.page(p - 1).draw('page'));
            $pag.append($b);
        });

        const $next = $('<button class="dt-page-btn">&rsaquo;</button>');
        if (current >= total - 1) $next.prop('disabled', true);
        else $next.on('click', () => api.page('next').draw('page'));
        $pag.append($next);
    }

    function _pageRange(current, total, max) {
        if (total <= max) return Array.from({ length: total }, (_, i) => i + 1);
        const half  = Math.floor(max / 2);
        let   start = Math.max(1, current - half);
        let   end   = Math.min(total, start + max - 1);
        if (end - start < max - 1) start = Math.max(1, end - max + 1);
        const pages = [];
        if (start > 1)   { pages.push(1); if (start > 2) pages.push('...'); }
        for (let i = start; i <= end; i++) pages.push(i);
        if (end < total) { if (end < total - 1) pages.push('...'); pages.push(total); }
        return pages;
    }

    return dt;
};

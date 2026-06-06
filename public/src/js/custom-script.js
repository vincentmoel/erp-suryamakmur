// ── Money helpers ─────────────────────────────────────────────────────────────

function formatRupiah(n) {
    return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
}

function parseMoney(str) {
    return parseInt((str || '').replace(/\D/g, ''), 10) || 0;
}

/**
 * Bind a visible display input to a hidden raw-value input.
 * The display input shows the formatted number (e.g. "35.000"),
 * the hidden input stores the raw integer for form submission.
 * Pass an optional onChange callback for recalculation hooks.
 */
function bindMoneyInput(displayEl, hiddenEl, onChange) {
    var init = parseInt(hiddenEl.value, 10);
    if (init) displayEl.value = init.toLocaleString('id-ID');

    displayEl.addEventListener('input', function () {
        var raw  = parseMoney(this.value);
        var cur  = this.selectionStart;
        var prev = this.value.length;
        this.value = raw ? raw.toLocaleString('id-ID') : '';
        var diff = this.value.length - prev;
        this.setSelectionRange(cur + diff, cur + diff);
        hiddenEl.value = raw || '';
        if (typeof onChange === 'function') onChange(raw);
    });
}

/**
 * Bind a shadcn-style switch button (data-state="checked|unchecked") to a
 * hidden input that holds "1" or "0" for form submission.
 */
function bindToggle(buttonEl, hiddenEl) {
    function setState(checked) {
        var state = checked ? 'checked' : 'unchecked';
        buttonEl.setAttribute('data-state', state);
        buttonEl.setAttribute('aria-checked', checked ? 'true' : 'false');
        var thumb = buttonEl.querySelector('[data-slot="switch-thumb"]');
        if (thumb) thumb.setAttribute('data-state', state);
        hiddenEl.value = checked ? '1' : '0';
    }

    // Sync initial state from hidden input
    setState(hiddenEl.value === '1');

    buttonEl.addEventListener('click', function () {
        setState(this.getAttribute('data-state') !== 'checked');
    });
}

// ── Auto-init on DOMContentLoaded ─────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', function () {
    // Money inputs: <input data-money-display="hidden_input_id" ...>
    document.querySelectorAll('[data-money-display]').forEach(function (displayEl) {
        var hiddenEl = document.getElementById(displayEl.dataset.moneyDisplay);
        if (hiddenEl) bindMoneyInput(displayEl, hiddenEl);
    });

    // Toggle switches: <button data-toggle-input="hidden_input_id" ...>
    document.querySelectorAll('[data-toggle-input]').forEach(function (btn) {
        var hiddenEl = document.getElementById(btn.dataset.toggleInput);
        if (hiddenEl) bindToggle(btn, hiddenEl);
    });
});

// ── Skeleton helpers ───────────────────────────────────────────────────────────

function skeletonRows(cols, n) {
    return Array.from({length: n}, () => {
        const cells = cols.map((w, i) =>
            `<td${i > 0 ? ' style="text-align:right;"' : ''}><span class="skeleton" style="width:${w}px;height:0.875rem;"></span></td>`
        ).join('');
        return `<tr>${cells}</tr>`;
    }).join('');
}

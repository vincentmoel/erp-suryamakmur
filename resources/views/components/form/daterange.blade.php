@props([
    'nameFrom',
    'nameTo',
    'placeholder'  => 'Select date range...',
    'selectedFrom' => null,
    'selectedTo'   => null,
])

@php $uid = 'dr-' . Str::random(8); @endphp

<style>
.dr-dropdown {
    position: fixed;
    z-index: 9999;
    border-radius: calc(var(--radius) - 2px);
    border: 1px solid var(--border);
    background-color: var(--popover);
    color: var(--popover-foreground);
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    padding: 0.75rem;
    min-width: 17rem;
    user-select: none;
}
.dr-dropdown.hidden { display: none; }

.dr-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.75rem;
}
.dr-nav-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 1.75rem;
    height: 1.75rem;
    border-radius: calc(var(--radius) - 4px);
    border: none;
    background: transparent;
    color: var(--foreground);
    cursor: pointer;
    transition: background-color 150ms;
}
.dr-nav-btn:hover { background-color: var(--accent); color: var(--accent-foreground); }

.dr-month-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--foreground);
}

.dr-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    margin-bottom: 0.25rem;
}
.dr-weekday {
    text-align: center;
    font-size: 0.75rem;
    font-weight: 500;
    color: var(--muted-foreground);
    padding: 0.25rem 0;
}

.dr-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
}
.dr-day {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 2rem;
    width: 100%;
    border-radius: calc(var(--radius) - 4px);
    border: none;
    background: transparent;
    color: var(--foreground);
    font-size: 0.8125rem;
    cursor: pointer;
    transition: background-color 150ms, color 150ms;
    outline: none;
}
.dr-day:hover { background-color: var(--accent); color: var(--accent-foreground); }
.dr-day.dr-today { font-weight: 600; text-decoration: underline; text-underline-offset: 3px; }
.dr-day.dr-selected { background-color: var(--primary); color: var(--primary-foreground); font-weight: 600; }
.dr-day.dr-in-range { background-color: var(--accent); color: var(--accent-foreground); border-radius: 0; }
.dr-day.dr-range-start { background-color: var(--primary); color: var(--primary-foreground); font-weight: 600; border-radius: calc(var(--radius) - 4px) 0 0 calc(var(--radius) - 4px); }
.dr-day.dr-range-end   { background-color: var(--primary); color: var(--primary-foreground); font-weight: 600; border-radius: 0 calc(var(--radius) - 4px) calc(var(--radius) - 4px) 0; }
.dr-day.dr-range-single { background-color: var(--primary); color: var(--primary-foreground); font-weight: 600; border-radius: calc(var(--radius) - 4px); }
.dr-day.dr-hover-range { background-color: color-mix(in oklab, var(--accent) 60%, transparent); color: var(--accent-foreground); border-radius: 0; }
.dr-day.dr-hover-end   { background-color: var(--primary); color: var(--primary-foreground); opacity: 0.7; font-weight: 600; border-radius: 0 calc(var(--radius) - 4px) calc(var(--radius) - 4px) 0; }
</style>

<div data-daterange="{{ $uid }}" class="relative">

    {{-- Trigger --}}
    <button type="button"
            aria-expanded="false"
            aria-haspopup="dialog"
            class="select-trigger w-full">
        <span data-dr-label class="text-sm {{ ($selectedFrom || $selectedTo) ? '' : 'text-muted-foreground' }}">
            {{ $placeholder }}
        </span>
        <span class="flex items-center gap-1 shrink-0">
            <span data-dr-clear
                  role="button"
                  tabindex="0"
                  title="Clear"
                  class="{{ ($selectedFrom || $selectedTo) ? '' : 'hidden' }} flex items-center justify-center rounded hover:text-destructive opacity-50 hover:opacity-100 transition-opacity">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="size-3.5">
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
            </span>
            <svg data-dr-chevron xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="size-4 opacity-50 transition-transform duration-150">
                <path d="m6 9 6 6 6-6"/>
            </svg>
        </span>
    </button>

    {{-- Dropdown --}}
    <div data-dr-dropdown class="dr-dropdown hidden">

        {{-- Month Navigation --}}
        <div class="dr-nav">
            <button type="button" data-dr-prev class="dr-nav-btn" title="Previous month">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;">
                    <path d="m15 18-6-6 6-6"/>
                </svg>
            </button>
            <span data-dr-month-label class="dr-month-label"></span>
            <button type="button" data-dr-next class="dr-nav-btn" title="Next month">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;">
                    <path d="m9 18 6-6-6-6"/>
                </svg>
            </button>
        </div>

        {{-- Weekday Headers --}}
        <div class="dr-weekdays">
            @foreach(['Su','Mo','Tu','We','Th','Fr','Sa'] as $d)
                <div class="dr-weekday">{{ $d }}</div>
            @endforeach
        </div>

        {{-- Day Grid --}}
        <div data-dr-grid class="dr-grid"></div>

    </div>

    {{-- Hidden Inputs --}}
    <input type="hidden" name="{{ $nameFrom }}" data-dr-input-from value="{{ $selectedFrom ?? '' }}">
    <input type="hidden" name="{{ $nameTo }}"   data-dr-input-to   value="{{ $selectedTo ?? '' }}">

</div>

<script>
(function () {
    var root       = document.querySelector('[data-daterange="{{ $uid }}"]');
    var trigger    = root.querySelector('button[aria-haspopup="dialog"]');
    var dropdown   = root.querySelector('[data-dr-dropdown]');
    var labelEl    = root.querySelector('[data-dr-label]');
    var clearBtn   = root.querySelector('[data-dr-clear]');
    var chevron    = root.querySelector('[data-dr-chevron]');
    var prevBtn    = root.querySelector('[data-dr-prev]');
    var nextBtn    = root.querySelector('[data-dr-next]');
    var monthLabel = root.querySelector('[data-dr-month-label]');
    var grid       = root.querySelector('[data-dr-grid]');
    var inputFrom  = root.querySelector('[data-dr-input-from]');
    var inputTo    = root.querySelector('[data-dr-input-to]');
    var placeholder = {{ Js::from($placeholder) }};

    var MONTHS = ['January','February','March','April','May','June',
                  'July','August','September','October','November','December'];
    var SHORT_MONTHS = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    var fromVal  = inputFrom.value || null;
    var toVal    = inputTo.value   || null;
    var hoverVal = null;
    var selecting = !!(fromVal && !toVal);

    var now   = new Date();
    var today = fmt(now);
    var viewY = now.getFullYear();
    var viewM = now.getMonth();

    if (fromVal) { var d0 = parse(fromVal); viewY = d0.getFullYear(); viewM = d0.getMonth(); }

    function fmt(d) {
        return d.getFullYear() + '-' + pad(d.getMonth()+1) + '-' + pad(d.getDate());
    }
    function pad(n) { return String(n).padStart(2,'0'); }
    function parse(s) { var p = s.split('-'); return new Date(+p[0], +p[1]-1, +p[2]); }
    function display(s) { var d = parse(s); return d.getDate() + ' ' + MONTHS[d.getMonth()] + ' ' + d.getFullYear(); }

    function updateLabel() {
        var text = null;
        if (fromVal && toVal)   text = display(fromVal) + ' – ' + display(toVal);
        else if (fromVal)       text = display(fromVal) + ' – ...';

        if (text) {
            labelEl.textContent = text;
            labelEl.classList.remove('text-muted-foreground');
            clearBtn.classList.remove('hidden');
        } else {
            labelEl.textContent = placeholder;
            labelEl.classList.add('text-muted-foreground');
            clearBtn.classList.add('hidden');
        }
    }

    function renderCalendar() {
        monthLabel.textContent = MONTHS[viewM] + ' ' + viewY;
        grid.innerHTML = '';

        var firstDay     = new Date(viewY, viewM, 1).getDay();
        var daysInMonth  = new Date(viewY, viewM + 1, 0).getDate();

        // Empty leading cells
        for (var i = 0; i < firstDay; i++) {
            var empty = document.createElement('div');
            grid.appendChild(empty);
        }

        for (var d = 1; d <= daysInMonth; d++) {
            var dateStr = viewY + '-' + pad(viewM+1) + '-' + pad(d);
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.dataset.date = dateStr;
            btn.textContent = d;
            btn.className = 'dr-day';
            styleDay(btn, dateStr);
            grid.appendChild(btn);
        }
    }

    function styleDay(btn, dateStr) {
        btn.className = 'dr-day';
        if (dateStr === today) btn.classList.add('dr-today');

        var isFrom = dateStr === fromVal;
        var isTo   = dateStr === toVal;

        if (fromVal && toVal) {
            if (isFrom && isTo) {
                btn.classList.add('dr-range-single');
            } else if (isFrom) {
                btn.classList.add('dr-range-start');
            } else if (isTo) {
                btn.classList.add('dr-range-end');
            } else if (dateStr > fromVal && dateStr < toVal) {
                btn.classList.add('dr-in-range');
            }
        } else if (fromVal && !toVal) {
            if (isFrom) {
                btn.classList.add('dr-range-single');
            } else if (hoverVal) {
                var lo = fromVal < hoverVal ? fromVal : hoverVal;
                var hi = fromVal < hoverVal ? hoverVal : fromVal;
                if (dateStr === lo && lo === fromVal)   btn.classList.add('dr-range-single');
                else if (dateStr === hi)                btn.classList.add('dr-hover-end');
                else if (dateStr > lo && dateStr < hi)  btn.classList.add('dr-hover-range');
            }
        }
    }

    function rerender() {
        grid.querySelectorAll('[data-date]').forEach(function (btn) {
            styleDay(btn, btn.dataset.date);
        });
    }

    // ── Open / Close ─────────────────────────────────────────────

    function openDropdown() {
        document.dispatchEvent(new CustomEvent('dr:close-all', { detail: { except: root } }));
        var r = trigger.getBoundingClientRect();
        dropdown.style.top   = (r.bottom + 4) + 'px';
        dropdown.style.left  = r.left + 'px';
        dropdown.style.width = Math.max(r.width, 272) + 'px';
        dropdown.classList.remove('hidden');
        trigger.setAttribute('aria-expanded', 'true');
        chevron.style.transform = 'rotate(180deg)';
        renderCalendar();
    }

    function closeDropdown() {
        dropdown.classList.add('hidden');
        trigger.setAttribute('aria-expanded', 'false');
        chevron.style.transform = '';
        hoverVal = null;
    }

    trigger.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.classList.contains('hidden') ? openDropdown() : closeDropdown();
    });

    document.addEventListener('click', function (e) {
        if (!root.contains(e.target) && !dropdown.contains(e.target)) closeDropdown();
    });

    window.addEventListener('scroll', function () {
        if (!dropdown.classList.contains('hidden')) openDropdown();
    }, true);

    window.addEventListener('resize', function () {
        if (!dropdown.classList.contains('hidden')) closeDropdown();
    });

    document.addEventListener('dr:close-all', function (e) {
        if (e.detail.except !== root) closeDropdown();
    });

    // ── Day Click ────────────────────────────────────────────────

    grid.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-date]');
        if (!btn) return;
        var dateStr = btn.dataset.date;

        if (!fromVal || (fromVal && toVal)) {
            fromVal = dateStr; toVal = null; selecting = true;
        } else {
            if (dateStr <= fromVal) { toVal = fromVal; fromVal = dateStr; }
            else                    { toVal = dateStr; }
            selecting = false;
            setTimeout(closeDropdown, 120);
        }

        inputFrom.value = fromVal || '';
        inputTo.value   = toVal   || '';
        updateLabel();
        rerender();
        inputFrom.dispatchEvent(new Event('change'));
    });

    // ── Hover Preview ────────────────────────────────────────────

    grid.addEventListener('mouseover', function (e) {
        if (!selecting) return;
        var btn = e.target.closest('[data-date]');
        if (!btn) return;
        hoverVal = btn.dataset.date;
        rerender();
    });

    grid.addEventListener('mouseleave', function () {
        if (!selecting) return;
        hoverVal = null;
        rerender();
    });

    // ── Month Nav ────────────────────────────────────────────────

    prevBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        if (--viewM < 0) { viewM = 11; viewY--; }
        renderCalendar();
    });

    nextBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        if (++viewM > 11) { viewM = 0; viewY++; }
        renderCalendar();
    });

    // ── Clear ────────────────────────────────────────────────────

    clearBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        fromVal = null; toVal = null; selecting = false; hoverVal = null;
        inputFrom.value = ''; inputTo.value = '';
        updateLabel();
        if (!dropdown.classList.contains('hidden')) rerender();
        inputFrom.dispatchEvent(new Event('change'));
    });

    clearBtn.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); clearBtn.click(); }
    });

    updateLabel();
})();
</script>

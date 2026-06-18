@php
    $format  = $data["{$doc}_format"]->value  ?? ($doc === 'invoice' ? 'INV-{Y}{m}-{seq}' : 'BILL-{Y}{m}-{seq}');
    $padding = $data["{$doc}_padding"]->value ?? '4';

    $tokens = [
        ['token' => '{Y}',   'label' => 'Tahun 4 digit', 'example' => now()->format('Y'),  'color' => 'blue'],
        ['token' => '{y}',   'label' => 'Tahun 2 digit', 'example' => now()->format('y'),  'color' => 'blue'],
        ['token' => '{m}',   'label' => 'Bulan',         'example' => now()->format('m'),  'color' => 'green'],
        ['token' => '{d}',   'label' => 'Hari',          'example' => now()->format('d'),  'color' => 'amber'],
        ['token' => '{seq}', 'label' => 'Nomor Urut',    'example' => '0001',              'color' => 'blue'],
    ];
@endphp

@once
<style>
/* ── Composer (input field) ─────────────────────── */
.format-composer {
    min-height: 48px;
    padding: 8px 12px;
    width: 100%;
    border-radius: calc(var(--radius) - 2px);
    border-width: 1px;
    border-style: solid;
    border-color: var(--input);
    background: transparent;
    color: var(--color-text-primary);
    font-size: var(--text-sm);
    line-height: 1;
    cursor: text;
    outline: none;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    transition: border-color 0.15s, box-shadow 0.15s;
}
.format-composer:focus-within {
    border-color: hsl(var(--ring));
    box-shadow: 0 0 0 3px hsl(var(--ring) / 0.2);
}
.format-composer.drag-over {
    border-color: var(--color-border-info);
    background: var(--color-background-info);
}
/* drop indicator line between nodes */
.format-composer .drop-indicator {
    display: inline-block;
    width: 2px;
    height: 1.2em;
    background: hsl(var(--ring));
    border-radius: 1px;
    vertical-align: middle;
    pointer-events: none;
}

/* ── Pills inside composer ──────────────────────── */
.token-pill {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 1px 8px 1px 10px;
    border-radius: 999px;
    border: 1.5px solid;
    font-size: 12px;
    font-weight: 600;
    font-family: var(--font-mono, monospace);
    line-height: 1.4;
    user-select: none;
    cursor: default;
    white-space: nowrap;
}
.token-pill[data-color="blue"]   { background: var(--color-background-info);    color: var(--color-text-info);    border-color: var(--color-border-info);    }
.token-pill[data-color="green"]  { background: var(--color-background-success);  color: var(--color-text-success);  border-color: var(--color-border-success);  }
.token-pill[data-color="amber"]  { background: var(--color-background-warning);  color: var(--color-text-warning);  border-color: var(--color-border-warning);  }
.token-pill[data-color="purple"] { background: color-mix(in srgb, purple 12%, transparent); color: purple; border-color: color-mix(in srgb, purple 45%, transparent); }
.token-pill .pill-remove {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 11px;
    line-height: 1;
    opacity: 0.55;
    transition: opacity 0.1s;
}
.token-pill .pill-remove:hover { opacity: 1; }

/* ── Token source buttons (palette) ─────────────── */
.token-source {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 999px;
    border: 1.5px solid;
    font-size: 12px;
    font-weight: 500;
    cursor: grab;
    transition: filter 0.15s, transform 0.1s;
}
.token-source:hover  { filter: brightness(0.92); }
.token-source:active { cursor: grabbing; transform: scale(0.97); }
.token-source[data-color="blue"]   { background: var(--color-background-info);    color: var(--color-text-info);    border-color: var(--color-border-info);    }
.token-source[data-color="green"]  { background: var(--color-background-success);  color: var(--color-text-success);  border-color: var(--color-border-success);  }
.token-source[data-color="amber"]  { background: var(--color-background-warning);  color: var(--color-text-warning);  border-color: var(--color-border-warning);  }
.token-source[data-color="purple"] { background: color-mix(in srgb, purple 12%, transparent); color: purple; border-color: color-mix(in srgb, purple 45%, transparent); }
.token-source .token-code {
    font-family: var(--font-mono, monospace);
    font-weight: 700;
    font-size: 11px;
}
.token-source .token-arrow {
    opacity: 0.5;
    font-size: 11px;
}
</style>
@endonce

<div class="rounded-lg border bg-card text-card-foreground shadow-xs" data-section="{{ $sectionKey }}">
    <div class="flex items-center gap-3 border-b px-6 py-5">
        <x-icon :name="$icon" class="size-5 text-primary" />
        <h3 class="text-sm font-semibold">@lang($titleKey)</h3>
    </div>

    <div class="flex flex-col gap-6 p-6">

        {{-- Token palette --}}
        <div>
            <p class="mb-2 text-xs font-medium text-muted-foreground">Token — klik atau drag ke kotak format</p>
            <div class="flex flex-wrap gap-2">
                @foreach($tokens as $t)
                    <button type="button"
                        class="token-source"
                        draggable="true"
                        data-token="{{ $t['token'] }}"
                        data-color="{{ $t['color'] }}"
                        data-doc="{{ $doc }}"
                        title="Contoh: {{ $t['example'] }}">
                        <span class="token-code">{{ $t['token'] }}</span>
                        <span>{{ $t['label'] }}</span>
                        <span class="token-arrow">→ {{ $t['example'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Format composer --}}
        <x-form.field :name="$doc . '_format'" :label="__('general.numbering_format')" :required="true">
            <div class="format-composer"
                id="{{ $doc }}_composer"
                contenteditable="true"
                data-doc="{{ $doc }}"
                spellcheck="false"></div>
            <input type="hidden" name="{{ $doc }}_format" id="{{ $doc }}_format" value="{{ $format }}">
            <p class="mt-1 text-xs text-muted-foreground">Ketik teks statis langsung di kotak, atau drag/klik token di atas. Token <span class="font-mono font-semibold">{seq}</span> wajib ada.</p>
        </x-form.field>

        {{-- Padding --}}
        <x-form.field :name="$doc . '_padding'" :label="__('general.numbering_padding')">
            <input type="number" name="{{ $doc }}_padding" id="{{ $doc }}_padding"
                value="{{ $padding }}" class="input" min="1" max="10" style="max-width:120px">
            <p class="mt-1 text-xs text-muted-foreground">@lang('general.numbering_padding_hint')</p>
        </x-form.field>

        {{-- Live preview --}}
        <div class="flex items-center gap-3 rounded-md border bg-muted/40 px-4 py-3">
            <span class="text-sm text-muted-foreground">@lang('general.numbering_preview')</span>
            <span id="{{ $doc }}_preview" class="font-mono text-sm font-semibold tracking-wide text-primary">
                {{ \App\Helpers\CodeGenerator::preview($format, (int)$padding) }}
            </span>
        </div>

    </div>

    <div class="flex items-center justify-end gap-2 border-t px-6 py-4">
        <button type="button" class="btn btn-primary btn-save" data-section="{{ $sectionKey }}">
            <x-icon name="check" class="size-3.5" />
            @lang('general.save')
        </button>
    </div>
</div>

<script>
(function () {
    const doc      = {{ Js::from($doc) }};
    const tokens   = @json($tokens);
    const composer = document.getElementById(doc + '_composer');
    const hidden   = document.getElementById(doc + '_format');
    const initial  = {{ Js::from($format) }};

    const colorMap = {};
    tokens.forEach(t => { colorMap[t.token] = t.color; });

    // Pill yang sedang di-drag untuk reorder
    let draggingPill = null;
    let indicator    = null; // elemen garis drop indicator

    // ── Buat pill ────────────────────────────────────────────────
    function makePill(token) {
        const color = colorMap[token] ?? 'blue';
        const span  = document.createElement('span');
        span.className       = 'token-pill';
        span.dataset.token   = token;
        span.dataset.color   = color;
        span.contentEditable = 'false';
        span.draggable       = true;

        const label = document.createElement('span');
        label.textContent = token;

        const rm = document.createElement('span');
        rm.className   = 'pill-remove';
        rm.textContent = '×';
        rm.title       = 'Hapus';
        rm.addEventListener('mousedown', e => {
            e.preventDefault();
            span.remove();
            syncAndPreview();
        });

        span.appendChild(label);
        span.appendChild(rm);

        // ── Drag pill untuk reorder ──────────────────────────────
        span.addEventListener('dragstart', e => {
            e.stopPropagation();
            draggingPill = span;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', ''); // Firefox wajib ada data
            setTimeout(() => { span.style.opacity = '0.35'; }, 0);
        });
        span.addEventListener('dragend', () => {
            span.style.opacity = '';
            draggingPill = null;
            removeIndicator();
        });

        return span;
    }

    // ── Drop indicator (garis vertikal) ──────────────────────────
    function removeIndicator() {
        if (indicator) { indicator.remove(); indicator = null; }
    }

    function showIndicatorBefore(node) {
        removeIndicator();
        indicator = document.createElement('span');
        indicator.className = 'drop-indicator';
        composer.insertBefore(indicator, node);
    }

    function showIndicatorAtEnd() {
        removeIndicator();
        indicator = document.createElement('span');
        indicator.className = 'drop-indicator';
        composer.appendChild(indicator);
    }

    // Cari node child composer yang ada di sebelah kanan x
    function getNodeAfter(x) {
        const children = [...composer.childNodes].filter(n => n !== indicator && n !== draggingPill);
        for (const node of children) {
            if (node.nodeType === Node.ELEMENT_NODE) {
                const rect = node.getBoundingClientRect();
                if (x < rect.left + rect.width / 2) return node;
            }
        }
        return null; // → append to end
    }

    // ── Pastikan selalu ada text node di awal dan akhir ──────────
    // Tanpa ini, browser tidak tahu di mana meletakkan karakter
    // yang diketik sebelum/sesudah pill di flex container.
    const ZWS = '​'; // zero-width space — invisible but gives browser a cursor position

    function ensureTextNodes() {
        if (!composer.firstChild || composer.firstChild.nodeType !== Node.TEXT_NODE) {
            composer.insertBefore(document.createTextNode(ZWS), composer.firstChild);
        }
        if (!composer.lastChild || composer.lastChild.nodeType !== Node.TEXT_NODE) {
            composer.appendChild(document.createTextNode(ZWS));
        }
        // Pastikan di antara setiap dua non-text node juga ada text node
        const children = [...composer.childNodes];
        for (let i = 0; i < children.length - 1; i++) {
            const cur  = children[i];
            const next = children[i + 1];
            if (cur.nodeType !== Node.TEXT_NODE && next.nodeType !== Node.TEXT_NODE) {
                cur.after(document.createTextNode(ZWS));
            }
        }
    }

    // ── Deserialize ──────────────────────────────────────────────
    function deserialize(fmt) {
        composer.innerHTML = '';
        fmt.split(/(\{[A-Za-z]+\})/).forEach(part => {
            if (/^\{[A-Za-z]+\}$/.test(part)) {
                composer.appendChild(makePill(part));
            } else if (part) {
                composer.appendChild(document.createTextNode(part));
            }
        });
        ensureTextNodes();
    }

    // ── Serialize ────────────────────────────────────────────────
    function serialize() {
        let out = '';
        composer.childNodes.forEach(node => {
            if (node.nodeType === Node.TEXT_NODE) {
                out += node.textContent.replace(/​/g, '');
            } else if (node.dataset?.token) {
                out += node.dataset.token;
            }
        });
        return out;
    }

    function syncAndPreview() {
        hidden.value = serialize();
        hidden.dispatchEvent(new Event('change'));
    }

    // ── Insert pill dari palette di posisi kursor ─────────────────
    function insertPillAtCursor(token) {
        composer.focus();
        const sel = window.getSelection();
        let range;

        if (sel && sel.rangeCount && composer.contains(sel.getRangeAt(0).commonAncestorContainer)) {
            range = sel.getRangeAt(0);
        } else {
            range = document.createRange();
            range.selectNodeContents(composer);
            range.collapse(false);
        }

        range.deleteContents();
        const pill = makePill(token);
        range.insertNode(pill);

        ensureTextNodes();

        // Tempatkan cursor di text node setelah pill
        const afterNode = pill.nextSibling;
        const r = document.createRange();
        if (afterNode && afterNode.nodeType === Node.TEXT_NODE) {
            r.setStart(afterNode, 0);
        } else {
            r.setStartAfter(pill);
        }
        r.collapse(true);
        sel.removeAllRanges();
        sel.addRange(r);
        syncAndPreview();
    }

    // ── Composer drag events ──────────────────────────────────────
    composer.addEventListener('dragover', e => {
        e.preventDefault();
        if (draggingPill) {
            // Mode reorder — tampilkan indikator posisi
            e.dataTransfer.dropEffect = 'move';
            const after = getNodeAfter(e.clientX);
            after ? showIndicatorBefore(after) : showIndicatorAtEnd();
        } else {
            // Mode insert dari palette
            e.dataTransfer.dropEffect = 'copy';
            composer.classList.add('drag-over');
        }
    });

    composer.addEventListener('dragleave', e => {
        if (!composer.contains(e.relatedTarget)) {
            composer.classList.remove('drag-over');
            removeIndicator();
        }
    });

    composer.addEventListener('drop', e => {
        e.preventDefault();
        composer.classList.remove('drag-over');

        if (draggingPill) {
            // Reorder pill
            const after = getNodeAfter(e.clientX);
            removeIndicator();
            if (after) {
                composer.insertBefore(draggingPill, after);
            } else {
                composer.appendChild(draggingPill);
            }
            draggingPill = null;
            syncAndPreview();
        } else {
            // Insert token baru dari palette
            removeIndicator();
            const token = e.dataTransfer.getData('text/plain');
            if (!token) return;

            let range;
            if (document.caretRangeFromPoint) {
                range = document.caretRangeFromPoint(e.clientX, e.clientY);
            } else if (document.caretPositionFromPoint) {
                const pos = document.caretPositionFromPoint(e.clientX, e.clientY);
                if (pos) { range = document.createRange(); range.setStart(pos.offsetNode, pos.offset); }
            }
            if (range && composer.contains(range.commonAncestorContainer)) {
                const sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);
            }
            insertPillAtCursor(token);
        }
    });

    // ── Palette buttons ───────────────────────────────────────────
    document.querySelectorAll('.token-source[data-doc="' + doc + '"]').forEach(btn => {
        btn.addEventListener('dragstart', e => {
            draggingPill = null; // pastikan bukan mode reorder
            e.dataTransfer.setData('text/plain', btn.dataset.token);
            e.dataTransfer.effectAllowed = 'copy';
        });
        btn.addEventListener('click', () => insertPillAtCursor(btn.dataset.token));
    });

    // ── Klik di composer → tempatkan cursor di posisi klik ────────
    composer.addEventListener('click', e => {
        if (e.target !== composer) return; // klik pada pill ditangani browser
        let range;
        if (document.caretRangeFromPoint) {
            range = document.caretRangeFromPoint(e.clientX, e.clientY);
        } else if (document.caretPositionFromPoint) {
            const pos = document.caretPositionFromPoint(e.clientX, e.clientY);
            if (pos) { range = document.createRange(); range.setStart(pos.offsetNode, pos.offset); }
        }
        if (range && composer.contains(range.commonAncestorContainer)) {
            const sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        } else {
            // Fallback: cursor ke akhir
            const r = document.createRange();
            r.selectNodeContents(composer);
            r.collapse(false);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(r);
        }
        composer.focus();
    });

    composer.addEventListener('input', () => { ensureTextNodes(); syncAndPreview(); });
    composer.addEventListener('keydown', e => { if (e.key === 'Enter') e.preventDefault(); });

    deserialize(initial);
    syncAndPreview();
})();
</script>

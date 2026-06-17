<style>
/* ── Table layout ────────────────────────────────────────────────── */
.perm-table {
    border-collapse: collapse;
    table-layout: fixed;  /* prevents reflow when checkbox state changes */
    width: 100%;
}

/* Module column takes remaining space; action columns fixed width */
.perm-th-module { width: auto; }
.perm-th-action { width: 76px; }

.perm-thead-row {
    border-bottom: 1px solid var(--border);
    background-color: color-mix(in oklab, var(--muted) 40%, transparent);
}

.perm-th {
    padding: 0.75rem 0.75rem;
    text-align: center;
    font-weight: 500;
    font-size: 0.75rem;
    color: var(--muted-foreground);
    white-space: nowrap;
    overflow: hidden;
}
.perm-th-module {
    text-align: left;
    padding-left: 1.25rem;
}

/* ── Group header ────────────────────────────────────────────────── */
.perm-group-row { background-color: color-mix(in oklab, var(--muted) 60%, transparent); }
.perm-group-label {
    padding: 0.375rem 1.25rem;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--muted-foreground);
}

/* ── Data rows ───────────────────────────────────────────────────── */
.perm-row {
    border-bottom: 1px solid var(--border);
}
.perm-row:last-child { border-bottom: none; }
.perm-row:hover { background-color: color-mix(in oklab, var(--muted) 30%, transparent); }

.perm-td-module {
    padding: 0.6rem 0.75rem 0.6rem 1.25rem;
}
.perm-td-action {
    padding: 0.6rem 0.75rem;
    text-align: center;
    vertical-align: middle;
}

.perm-module-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 1.75rem;
    height: 1.75rem;
    flex-shrink: 0;
    color: var(--muted-foreground);
}

/* ── Checkbox ────────────────────────────────────────────────────── */
.perm-cb {
    appearance: none;
    -webkit-appearance: none;
    width: 1.125rem;
    height: 1.125rem;
    border: 1.5px solid var(--border);
    border-radius: calc(var(--radius) - 4px);
    background: var(--background);
    cursor: pointer;
    vertical-align: middle;
    transition: background 120ms, border-color 120ms;
    flex-shrink: 0;
    /* position:relative so ::after can be absolute — no layout impact */
    position: relative;
}
.perm-cb:hover { border-color: var(--primary); }
.perm-cb:checked {
    background: var(--primary);
    border-color: var(--primary);
}
/* absolute checkmark — does NOT affect box dimensions */
.perm-cb::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0.28rem;
    height: 0.52rem;
    border: 2px solid transparent;
    border-top: none;
    border-left: none;
    transform: translate(-50%, -60%) rotate(45deg);
}
.perm-cb:checked::after {
    border-color: var(--primary-foreground);
}
.perm-cb:focus-visible {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}

/* ── N/A cell ────────────────────────────────────────────────────── */
.perm-na {
    color: var(--muted-foreground);
    opacity: 0.25;
    user-select: none;
}

/* ── Toggle buttons ──────────────────────────────────────────────── */
.perm-toggle-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.25rem;
    height: 1.25rem;
    border: 1.5px solid var(--border);
    border-radius: calc(var(--radius) - 4px);
    background: var(--background);
    color: var(--muted-foreground);
    cursor: pointer;
    transition: background 120ms, border-color 120ms, color 120ms;
    padding: 0;
    flex-shrink: 0;
}
.perm-toggle-btn svg { width: 0.65rem; height: 0.65rem; }
.perm-toggle-btn:hover { border-color: var(--primary); color: var(--primary); }
.perm-toggle-btn.is-active {
    background: var(--primary);
    border-color: var(--primary);
    color: var(--primary-foreground);
}
</style>

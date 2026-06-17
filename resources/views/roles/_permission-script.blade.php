<script>
(function () {
    function sel(q) { return Array.from(document.querySelectorAll(q)); }

    function allChecked(boxes) {
        return boxes.length > 0 && boxes.every(cb => cb.checked);
    }

    function toggleAll(boxes) {
        const check = !allChecked(boxes);
        boxes.forEach(cb => { cb.checked = check; });
    }

    function syncBtn(btn, boxes) {
        btn.classList.toggle('is-active', allChecked(boxes));
    }

    // stop all toggle buttons from bubbling to <th> (which has a sort handler)
    sel('[data-col-toggle], [data-row-toggle], #btn-global-toggle').forEach(btn => {
        btn.addEventListener('click', (e) => e.stopPropagation());
    });

    // ── column toggles ─────────────────────────────────────────────
    sel('[data-col-toggle]').forEach(btn => {
        const action = btn.dataset.colToggle;
        const boxes  = sel(`.perm-cb[data-action="${action}"]`);

        syncBtn(btn, boxes);

        btn.addEventListener('click', () => {
            toggleAll(boxes);
            syncBtn(btn, boxes);
            syncRowBtns();
            syncGlobal();
        });

        boxes.forEach(cb => cb.addEventListener('change', () => {
            syncBtn(btn, boxes);
            syncGlobal();
        }));
    });

    // ── row toggles ────────────────────────────────────────────────
    sel('[data-row-toggle]').forEach(btn => {
        const mod   = btn.dataset.rowToggle;
        const boxes = sel(`.perm-cb[data-module="${mod}"]`);

        syncBtn(btn, boxes);

        btn.addEventListener('click', () => {
            toggleAll(boxes);
            syncBtn(btn, boxes);
            syncColBtns();
            syncGlobal();
        });

        boxes.forEach(cb => cb.addEventListener('change', () => {
            syncBtn(btn, boxes);
            syncGlobal();
        }));
    });

    // ── global toggle ──────────────────────────────────────────────
    const globalBtn = document.getElementById('btn-global-toggle');
    const allBoxes  = sel('.perm-cb');

    function syncGlobal() {
        syncBtn(globalBtn, allBoxes);
    }

    function syncColBtns() {
        sel('[data-col-toggle]').forEach(btn => {
            syncBtn(btn, sel(`.perm-cb[data-action="${btn.dataset.colToggle}"]`));
        });
    }

    function syncRowBtns() {
        sel('[data-row-toggle]').forEach(btn => {
            syncBtn(btn, sel(`.perm-cb[data-module="${btn.dataset.rowToggle}"]`));
        });
    }

    globalBtn.addEventListener('click', () => {
        toggleAll(allBoxes);
        syncGlobal();
        syncColBtns();
        syncRowBtns();
    });

    // initial sync
    syncGlobal();
    syncColBtns();
    syncRowBtns();
})();
</script>

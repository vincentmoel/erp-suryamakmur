const root = document.documentElement;
const savedTheme = localStorage.getItem("mocci-theme");
if (savedTheme === "dark") root.classList.add("dark");

// ── TOAST ────────────────────────────────────────────────────
const _toastIcons = {
  success: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>',
  error  : '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>',
  warning: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>',
  info   : '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>',
};

function showToast(title, description = "", type = "info") {
  let host = document.querySelector("[data-toast-host]");
  if (!host) {
    host = document.createElement("div");
    host.dataset.toastHost = "";
    host.className = "toast-host";
    document.body.append(host);
  }

  const toast = document.createElement("div");
  toast.className = `toast toast--${type}`;
  toast.innerHTML = `
    <span class="toast-icon">${_toastIcons[type] ?? _toastIcons.info}</span>
    <div class="toast-body">
      <p class="toast-title">${title}</p>
      ${description ? `<p class="toast-description">${description}</p>` : ""}
    </div>
    <button class="toast-close" aria-label="Close">&times;</button>
  `;

  toast.querySelector(".toast-close").addEventListener("click", () => _dismissToast(toast));
  host.append(toast);

  requestAnimationFrame(() => toast.classList.add("toast--visible"));

  const timer = setTimeout(() => _dismissToast(toast), 4000);
  toast._timer = timer;
}

function _dismissToast(toast) {
  clearTimeout(toast._timer);
  toast.classList.remove("toast--visible");
  toast.addEventListener("transitionend", () => toast.remove(), { once: true });
}

// ── TABLE SORT & FILTER ──────────────────────────────────────
function enhanceTables() {
  document.querySelectorAll("table").forEach((table) => {
    table.querySelectorAll("th").forEach((th, index) => {
      th.classList.add("cursor-pointer", "select-none");
      th.addEventListener("click", () => {
        const rows = [...table.tBodies[0].rows];
        const dir = th.dataset.sortDir === "asc" ? "desc" : "asc";
        table.querySelectorAll("th").forEach((item) => delete item.dataset.sortDir);
        th.dataset.sortDir = dir;
        rows.sort((a, b) => {
          const left = a.cells[index]?.innerText.trim() || "";
          const right = b.cells[index]?.innerText.trim() || "";
          return dir === "asc" ? left.localeCompare(right, undefined, { numeric: true }) : right.localeCompare(left, undefined, { numeric: true });
        });
        rows.forEach((row) => table.tBodies[0].append(row));
      });
    });
  });

  document.querySelectorAll("[data-table-filter]").forEach((input) => {
    input.addEventListener("input", () => {
      const table = input.closest(".card")?.querySelector("table") || document.querySelector("table");
      const query = input.value.toLowerCase();
      table?.querySelectorAll("tbody tr").forEach((row) => {
        row.hidden = !row.innerText.toLowerCase().includes(query);
      });
    });
  });
}

// ── CLICK HANDLER ────────────────────────────────────────────
document.addEventListener("click", (event) => {

  // Toggle dark/light theme
  const toggle = event.target.closest("[data-toggle-theme]");
  if (toggle) {
    root.classList.toggle("dark");
    localStorage.setItem("mocci-theme", root.classList.contains("dark") ? "dark" : "light");
  }

  // Sidebar: open (mobile) / collapse (desktop)
  if (event.target.closest("[data-toggle-sidebar]")) document.body.classList.toggle("sidebar-open");
  if (event.target.closest("[data-collapse-sidebar]") && window.innerWidth >= 1024) document.body.classList.toggle("sidebar-collapsed");
  if (event.target.matches("[data-sidebar-backdrop]")) document.body.classList.remove("sidebar-open");

  // Sidebar: submenu accordion
  const navToggle = event.target.closest("[data-nav-toggle]");
  if (navToggle) {
    const expanded = navToggle.getAttribute("aria-expanded") === "true";
    navToggle.setAttribute("aria-expanded", String(!expanded));
    navToggle.closest("[data-nav-parent]")?.querySelector("[data-nav-sub]")?.classList.toggle("hidden", expanded);
  }

  // Command palette
  const paletteToggle = event.target.closest("[data-toggle-palette]");
  if (paletteToggle) document.querySelector("[data-command-palette]")?.classList.toggle("hidden");
  if (event.target.matches("[data-command-palette]")) event.target.classList.add("hidden");

  // Tabs
  const tab = event.target.closest("[data-tab]");
  if (tab) {
    const scope = tab.closest("[data-tabs]");
    const target = tab.dataset.tab;
    scope.querySelectorAll("[data-tab]").forEach((item) => item.classList.toggle("active", item === tab));
    scope.querySelectorAll("[data-panel]").forEach((panel) => panel.classList.toggle("hidden", panel.dataset.panel !== target));
  }

  // Dropdown
  const dropdown = event.target.closest("[data-dropdown-trigger]");
  if (dropdown) {
    const menu = dropdown.closest("[data-dropdown]")?.querySelector("[data-dropdown-menu]");
    if (menu) {
      const isUserBtn = dropdown.classList.contains("sidebar-user-btn");
      if (isUserBtn) {
        const rect = dropdown.getBoundingClientRect();
        menu.style.position = "fixed";
        menu.style.left = rect.left + "px";
        menu.style.width = rect.width + "px";
        menu.style.bottom = (window.innerHeight - rect.top + 4) + "px";
        menu.style.top = "auto";
        menu.style.zIndex = "9999";
      }
      menu.classList.toggle("hidden");
    }
  }

  // Dialog
  const dialogOpen = event.target.closest("[data-dialog-open]");
  if (dialogOpen) document.querySelector(dialogOpen.dataset.dialogOpen)?.classList.remove("hidden");
  if (event.target.closest("[data-dialog-close]") || event.target.matches("[data-dialog]")) event.target.closest("[data-dialog]")?.classList.add("hidden");

  // Toast trigger
  const toast = event.target.closest("[data-toast]");
  if (toast) showToast(toast.dataset.toast || "Action completed", toast.dataset.toastDescription || "", toast.dataset.toastType || "info");
});

// ── COMMAND PALETTE SEARCH ───────────────────────────────────
document.addEventListener("input", (event) => {
  if (event.target.matches("[data-command-input]")) {
    const query = event.target.value.toLowerCase();
    document.querySelectorAll("[data-command-item]").forEach((item) => {
      item.hidden = !item.innerText.toLowerCase().includes(query);
    });
  }
});

// ── KEYBOARD SHORTCUTS ───────────────────────────────────────
document.addEventListener("keydown", (event) => {
  if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === "k") {
    event.preventDefault();
    document.querySelector("[data-command-palette]")?.classList.toggle("hidden");
  }
  if (event.key === "Escape") {
    document.querySelector("[data-command-palette]")?.classList.add("hidden");
    document.querySelectorAll("[data-dialog]").forEach((dialog) => dialog.classList.add("hidden"));
    document.body.classList.remove("sidebar-open");
  }
  if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === "b") {
    event.preventDefault();
    if (window.innerWidth >= 1024) document.body.classList.toggle("sidebar-collapsed");
  }
});

// ── RESPONSIVE: tutup drawer saat resize ke desktop ─────────
window.addEventListener("resize", () => {
  if (window.innerWidth >= 1024) {
    document.body.classList.remove("sidebar-open");
  }
});

// ── CLOSE DROPDOWN saat klik di luar ────────────────────────
document.addEventListener("click", (event) => {
  if (!event.target.closest("[data-dropdown]")) {
    document.querySelectorAll("[data-dropdown-menu]").forEach((menu) => menu.classList.add("hidden"));
  }
}, true);

// ── CONFIRM DELETE DIALOG ─────────────────────────────────────
$(function () {
  let pendingUrl = null;

  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
  });

  $(document).on("click", ".dt-delete-btn", function () {
    pendingUrl = $(this).data("url");
    $("#confirm-delete-dialog").removeClass("hidden");
  });

  $("#confirm-delete-btn").on("click", function () {
    if (!pendingUrl) return;

    const label = $(this).text().trim();
    const $btn  = $(this).prop("disabled", true).html('<span class="dt-loading-spinner"></span> ' + label);

    $.ajax({
      url    : pendingUrl,
      type   : "DELETE",
      success: function (res) {
        $(document).trigger("dt:refresh");
        showToast(res.success.title, res.success.message, "success");
      },
      error  : function (xhr) {
        const err = xhr.responseJSON?.error;
        showToast(err?.message ?? "Failed to delete. Please try again.", "", "error");
      },
      complete: function () {
        $("#confirm-delete-dialog").addClass("hidden");
        $btn.prop("disabled", false).html("Delete");
        pendingUrl = null;
      },
    });
  });
});

// ── FORM SUBMIT LOADING ───────────────────────────────────────
(function () {
  const spinner = '<span class="dt-loading-spinner"></span>';

  document.querySelectorAll("form").forEach((form) => {
    let clickedBtn = null;

    form.querySelectorAll('button[type="submit"]').forEach((btn) => {
      btn.addEventListener("click", () => { clickedBtn = btn; });
    });

    form.addEventListener("submit", () => {
      form.querySelectorAll('button[type="submit"]').forEach((btn) => {
        btn.disabled = true;
      });

      if (clickedBtn) {
        const text = clickedBtn.innerText.trim();
        clickedBtn.innerHTML = spinner + " " + text;
      }
    });
  });
})();

// ── INIT ─────────────────────────────────────────────────────
enhanceTables();

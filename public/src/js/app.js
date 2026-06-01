const root = document.documentElement;
const savedTheme = localStorage.getItem("mocci-theme");
if (savedTheme === "dark") root.classList.add("dark");

// ── ICON PATHS ───────────────────────────────────────────────
const iconPaths = {
  search: '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
  bell: '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 7h18s-3 0-3-7"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>',
  users: '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
  user: '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
  calendar: '<path d="M8 2v4"/><path d="M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/>',
  "calendar-days": '<path d="M8 2v4"/><path d="M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/>',
  "chart-pie": '<path d="M21 12c.6 5-3.3 9-8.3 9A9 9 0 1 1 12 3v9z"/><path d="M12 3a9 9 0 0 1 9 9h-9z"/>',
  "chart-no-axes-combined": '<path d="M4 18V8"/><path d="M8 18v-4"/><path d="M12 18V6"/><path d="M16 18v-9"/><path d="M20 18V4"/>',
  gauge: '<path d="M12 14l4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/>',
  wallet: '<path d="M19 7V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-2"/><path d="M16 12h6v5h-6a2.5 2.5 0 0 1 0-5z"/>',
  "credit-card": '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>',
  package: '<path d="m7.5 4.3 9 5.2"/><path d="M21 8v8a2 2 0 0 1-1 1.7l-7 4a2 2 0 0 1-2 0l-7-4A2 2 0 0 1 3 16V8a2 2 0 0 1 1-1.7l7-4a2 2 0 0 1 2 0l7 4A2 2 0 0 1 21 8z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>',
  "shopping-cart": '<circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.7 12.4a2 2 0 0 0 2 1.6h8.7a2 2 0 0 0 2-1.6l1.6-7.4H5.1"/>',
  "shopping-bag": '<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>',
  settings: '<path d="M12 15.5A3.5 3.5 0 1 0 12 8a3.5 3.5 0 0 0 0 7.5z"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.9.3H9a1.7 1.7 0 0 0 1-1.6V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.9V9a1.7 1.7 0 0 0 1.6 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>',
  "chevron-right": '<path d="m9 18 6-6-6-6"/>',
  "chevrons-up-down": '<path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/>',
  "panel-left": '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18"/>',
  sun: '<circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>',
  moon: '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>',
  default: '<circle cx="12" cy="12" r="9"/><path d="M12 8v8"/><path d="M8 12h8"/>'
};

// ── RENDER ICONS ─────────────────────────────────────────────
function renderFallbackIcons() {
  document.querySelectorAll("i[data-lucide]").forEach((node) => {
    const name = node.getAttribute("data-lucide");
    const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
    svg.setAttribute("viewBox", "0 0 24 24");
    svg.setAttribute("fill", "none");
    svg.setAttribute("stroke", "currentColor");
    svg.setAttribute("stroke-width", "2");
    svg.setAttribute("stroke-linecap", "round");
    svg.setAttribute("stroke-linejoin", "round");
    svg.setAttribute("class", node.getAttribute("class") || "size-4");
    svg.innerHTML = iconPaths[name] || iconPaths.default;
    node.replaceWith(svg);
  });
}

// ── TOAST ────────────────────────────────────────────────────
function showToast(title, description = "") {
  let host = document.querySelector("[data-toast-host]");
  if (!host) {
    host = document.createElement("div");
    host.dataset.toastHost = "";
    host.className = "fixed bottom-4 right-4 z-[70] flex w-80 max-w-[calc(100vw-2rem)] flex-col gap-2";
    document.body.append(host);
  }
  const toast = document.createElement("div");
  toast.className = "rounded-lg border bg-popover p-4 text-popover-foreground shadow-lg";
  toast.innerHTML = `<p class="text-sm font-medium">${title}</p>${description ? `<p class="mt-1 text-xs text-muted-foreground">${description}</p>` : ""}`;
  host.append(toast);
  setTimeout(() => toast.remove(), 3200);
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
  if (toast) showToast(toast.dataset.toast || "Action completed", toast.dataset.toastDescription || "");
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

    const $btn = $(this).prop("disabled", true).text("Deleting...");

    $.ajax({
      url    : pendingUrl,
      type   : "DELETE",
      success: function (res) {
        $("#confirm-delete-dialog").addClass("hidden");
        $(document).trigger("dt:refresh");
        showToast(res.success.title, res.success.message);
      },
      error  : function (xhr) {
        const err = xhr.responseJSON?.error;
        showToast(err?.message ?? "Failed to delete. Please try again.", "");
      },
      complete: function () {
        $btn.prop("disabled", false).text("Delete");
        pendingUrl = null;
      },
    });
  });
});

// ── INIT ─────────────────────────────────────────────────────
renderFallbackIcons();
enhanceTables();

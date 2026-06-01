const root = document.documentElement;
const savedTheme = localStorage.getItem("mocci-theme");
if (savedTheme === "dark") root.classList.add("dark");

const iconPaths = {
  search: '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
  bell: '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 7h18s-3 0-3-7"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>',
  users: '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
  user: '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
  calendar: '<path d="M8 2v4"/><path d="M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/>',
  "calendar-days": '<path d="M8 2v4"/><path d="M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/>',
  "chart-pie": '<path d="M21 12c.6 5-3.3 9-8.3 9A9 9 0 1 1 12 3v9z"/><path d="M12 3a9 9 0 0 1 9 9h-9z"/>',
  "chart-no-axes-combined": '<path d="M12 16v5"/><path d="M16 14v7"/><path d="M20 10v11"/><path d="m4 16 4-4 4 4 8-8"/>',
  gauge: '<path d="M12 14l4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/>',
  wallet: '<path d="M19 7V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-2"/><path d="M16 12h6v5h-6a2.5 2.5 0 0 1 0-5z"/>',
  "credit-card": '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>',
  package: '<path d="m7.5 4.3 9 5.2"/><path d="M21 8v8a2 2 0 0 1-1 1.7l-7 4a2 2 0 0 1-2 0l-7-4A2 2 0 0 1 3 16V8a2 2 0 0 1 1-1.7l7-4a2 2 0 0 1 2 0l7 4A2 2 0 0 1 21 8z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>',
  "shopping-cart": '<circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.7 12.4a2 2 0 0 0 2 1.6h8.7a2 2 0 0 0 2-1.6l1.6-7.4H5.1"/>',
  "shopping-bag": '<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>',
  settings: '<path d="M12 15.5A3.5 3.5 0 1 0 12 8a3.5 3.5 0 0 0 0 7.5z"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.9.3H9a1.7 1.7 0 0 0 1-1.6V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.9V9a1.7 1.7 0 0 0 1.6 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>',
  "chevron-right": '<path d="m9 18 6-6-6-6"/>',
  "chevrons-up-down": '<path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/>',
  sparkles: '<path d="M9.9 10.8 8 15l-1.9-4.2L2 9l4.1-1.8L8 3l1.9 4.2L14 9z"/><path d="M19 3v4"/><path d="M21 5h-4"/><path d="M17 14v6"/><path d="M20 17h-6"/>',
  rocket: '<path d="M4.5 16.5c-1.5 1.3-2 3.5-2 5 1.5 0 3.7-.5 5-2"/><path d="M9 15 4 10l7-7c2-2 5-2 8-2 0 3 0 6-2 8z"/><path d="M15 9h.01"/>',
  briefcase: '<path d="M10 6V5a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v1"/><rect x="3" y="6" width="18" height="14" rx="2"/><path d="M3 12h18"/>',
  lock: '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
  "triangle-alert": '<path d="m21.7 18-8-14a2 2 0 0 0-3.4 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.7-3"/><path d="M12 9v4"/><path d="M12 17h.01"/>',
  "layout-grid": '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>',
  type: '<path d="M4 7V4h16v3"/><path d="M9 20h6"/><path d="M12 4v16"/>',
  box: '<path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>',
  navigation: '<polygon points="3 11 22 2 13 21 11 13 3 11"/>',
  "app-window": '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M10 4v4"/><path d="M2 8h20"/>',
  "panel-left": '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18"/>',
  sun: '<circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>',
  moon: '<path d="M12 3a6 6 0 0 0 9 6 9 9 0 1 1-9-6"/>',
  default: '<circle cx="12" cy="12" r="9"/><path d="M12 8v8"/><path d="M8 12h8"/>'
};

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

function parseData(node, fallback) {
  try {
    return JSON.parse(node.dataset.config || node.dataset.rows || node.dataset.items || "null") || fallback;
  } catch {
    return fallback;
  }
}

function linePath(points) {
  return points.map((p, index) => `${index ? "L" : "M"}${p[0].toFixed(1)} ${p[1].toFixed(1)}`).join(" ");
}

function areaPath(points, height, pad) {
  return `${linePath(points)} L${points.at(-1)[0].toFixed(1)} ${height - pad} L${points[0][0].toFixed(1)} ${height - pad} Z`;
}

function renderAreaChart(node) {
  const config = parseData(node, {});
  const data = config.data || [24, 36, 31, 48, 56, 50, 64, 72];
  const labels = config.labels || ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug"];
  const width = 640;
  const height = 260;
  const pad = 24;
  const max = Math.max(...data) * 1.12;
  const min = Math.min(0, ...data);
  const points = data.map((value, index) => {
    const x = pad + (index * (width - pad * 2)) / Math.max(data.length - 1, 1);
    const y = height - pad - ((value - min) / (max - min || 1)) * (height - pad * 2);
    return [x, y];
  });
  const gradientId = `chart-${Math.random().toString(36).slice(2)}`;
  node.innerHTML = `<svg class="mini-chart h-72 w-full overflow-visible" viewBox="0 0 ${width} ${height}" role="img">
    <defs><linearGradient id="${gradientId}" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="currentColor" stop-opacity=".28"/><stop offset="100%" stop-color="currentColor" stop-opacity="0"/></linearGradient></defs>
    ${[0, 1, 2, 3, 4].map((i) => `<line x1="${pad}" x2="${width - pad}" y1="${pad + i * 48}" y2="${pad + i * 48}" stroke="var(--border)" />`).join("")}
    <path d="${areaPath(points, height, pad)}" fill="url(#${gradientId})" class="text-primary"/>
    <path d="${linePath(points)}" fill="none" stroke="var(--primary)" stroke-width="3"/>
    ${points.map((p) => `<circle cx="${p[0]}" cy="${p[1]}" r="3" fill="var(--background)" stroke="var(--primary)" stroke-width="2"/>`).join("")}
    ${labels.map((label, i) => `<text x="${pad + i * ((width - pad * 2) / Math.max(labels.length - 1, 1))}" y="${height - 2}" font-size="12" text-anchor="middle" fill="var(--muted-foreground)">${label}</text>`).join("")}
  </svg>`;
}

function renderBarChart(node) {
  const config = parseData(node, {});
  const items = config.items || [["Desktop", 57, "21,840"], ["Mobile", 34, "12,940"], ["Tablet", 9, "3,511"]];
  const width = 640;
  const row = 42;
  const pad = 24;
  const labelWidth = 142;
  const height = pad * 2 + items.length * row;
  const max = Math.max(...items.map((item) => Number(item[1]))) || 1;
  node.innerHTML = `<svg class="mini-chart h-72 w-full overflow-visible" viewBox="0 0 ${width} ${height}" role="img">
    ${items.map(([label, value, text], index) => {
      const y = pad + index * row;
      const barWidth = ((Number(value) || 0) / max) * (width - labelWidth - pad * 2);
      return `<text x="${pad}" y="${y + 15}" font-size="12" fill="var(--muted-foreground)">${label}</text>
        <rect x="${labelWidth}" y="${y}" width="${width - labelWidth - pad}" height="18" rx="5" fill="var(--muted)"/>
        <rect x="${labelWidth}" y="${y}" width="${barWidth}" height="18" rx="5" fill="var(--primary)"/>
        <text x="${width - pad}" y="${y + 15}" text-anchor="end" font-size="12" fill="var(--muted-foreground)">${text || value}</text>`;
    }).join("")}
  </svg>`;
}

function renderPieChart(node) {
  const config = parseData(node, {});
  const items = config.items || [["Desktop", 57], ["Mobile", 34], ["Tablet", 9]];
  let offset = 25;
  const total = items.reduce((sum, item) => sum + Number(item[1]), 0);
  const rings = items.map((item, index) => {
    const value = (Number(item[1]) / total) * 100;
    const stroke = index === 0 ? "var(--primary)" : `color-mix(in oklab, var(--primary) ${70 - index * 20}%, transparent)`;
    const ring = `<circle cx="50" cy="50" r="36" fill="none" stroke="${stroke}" stroke-width="14" stroke-dasharray="${value} ${100 - value}" stroke-dashoffset="${offset}" pathLength="100"/>`;
    offset -= value;
    return ring;
  }).join("");
  node.innerHTML = `<div class="grid place-items-center gap-4">
    <svg viewBox="0 0 100 100" class="size-44 -rotate-90">${rings}<circle cx="50" cy="50" r="22" fill="var(--background)"/></svg>
    <div class="w-full space-y-1.5">${items.map(([label, value], i) => `<div class="flex items-center gap-2 text-xs"><span class="size-2 rounded-full bg-primary" style="opacity:${1 - i * .22}"></span><span class="flex-1">${label}</span><span class="text-muted-foreground">${value}%</span></div>`).join("")}</div>
  </div>`;
}

function renderCharts() {
  document.querySelectorAll("[data-chart='area']").forEach(renderAreaChart);
  document.querySelectorAll("[data-chart='bar']").forEach(renderBarChart);
  document.querySelectorAll("[data-chart='pie']").forEach(renderPieChart);
  document.querySelectorAll("[data-progress]").forEach((bar) => {
    requestAnimationFrame(() => {
      bar.style.width = `${bar.dataset.progress}%`;
    });
  });
}

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

function enhanceTables() {
  document.querySelectorAll("table").forEach((table) => {
    table.dataset.enhancedTable = "";
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

function initKanban() {
  let dragged = null;
  document.querySelectorAll("[data-kanban-card]").forEach((card) => {
    card.draggable = true;
    card.addEventListener("dragstart", () => {
      dragged = card;
      card.classList.add("opacity-50");
    });
    card.addEventListener("dragend", () => {
      card.classList.remove("opacity-50");
      dragged = null;
      showToast("Board updated", "Task position saved locally.");
    });
  });
  document.querySelectorAll("[data-kanban-column]").forEach((column) => {
    column.addEventListener("dragover", (event) => event.preventDefault());
    column.addEventListener("drop", (event) => {
      event.preventDefault();
      if (dragged) column.querySelector("[data-kanban-list]")?.append(dragged);
    });
  });
}

function initOtp() {
  document.querySelectorAll("[data-otp] input").forEach((input, index, inputs) => {
    input.addEventListener("input", () => {
      input.value = input.value.replace(/\D/g, "").slice(0, 1);
      if (input.value && inputs[index + 1]) inputs[index + 1].focus();
    });
    input.addEventListener("keydown", (event) => {
      if (event.key === "Backspace" && !input.value && inputs[index - 1]) inputs[index - 1].focus();
    });
  });
}

function initForms() {
  document.querySelectorAll("form").forEach((form) => {
    form.addEventListener("submit", (event) => {
      event.preventDefault();
      const invalid = [...form.querySelectorAll("input[required], textarea[required]")].find((field) => !field.value.trim());
      if (invalid) {
        invalid.focus();
        showToast("Validation error", "Please complete the required fields.");
        return;
      }
      showToast("Saved", "This vanilla JS form handler replaced react-hook-form + sonner.");
    });
  });
}

function initCalendar() {
  document.querySelectorAll("[data-date-picker]").forEach((button) => {
    button.addEventListener("click", () => {
      const next = button.dataset.alt || "Jun 01, 2026 - Jun 30, 2026";
      button.dataset.alt = button.textContent.trim();
      button.textContent = next;
      showToast("Date range changed", next);
    });
  });
}

function initCarousel() {
  document.querySelectorAll("[data-carousel]").forEach((carousel) => {
    const track = carousel.querySelector("[data-carousel-track]");
    const slides = [...carousel.querySelectorAll("[data-carousel-slide]")];
    let index = 0;
    const render = () => {
      if (track) track.style.transform = `translateX(-${index * 100}%)`;
    };
    carousel.querySelector("[data-carousel-next]")?.addEventListener("click", () => {
      index = (index + 1) % slides.length;
      render();
    });
    carousel.querySelector("[data-carousel-prev]")?.addEventListener("click", () => {
      index = (index - 1 + slides.length) % slides.length;
      render();
    });
  });
}

function initRichText() {
  document.querySelectorAll("[data-rich-text]").forEach((editor) => {
    editor.contentEditable = "true";
    editor.addEventListener("input", () => {
      editor.dataset.dirty = "true";
    });
  });
}

document.addEventListener("click", (event) => {
  const toggle = event.target.closest("[data-toggle-theme]");
  if (toggle) {
    root.classList.toggle("dark");
    localStorage.setItem("mocci-theme", root.classList.contains("dark") ? "dark" : "light");
  }

  if (event.target.closest("[data-toggle-sidebar]")) document.body.classList.toggle("sidebar-open");
  if (event.target.closest("[data-collapse-sidebar]")) document.body.classList.toggle("sidebar-collapsed");
  if (event.target.matches("[data-sidebar-backdrop]")) document.body.classList.remove("sidebar-open");

  const navToggle = event.target.closest("[data-nav-toggle]");
  if (navToggle) {
    const expanded = navToggle.getAttribute("aria-expanded") === "true";
    navToggle.setAttribute("aria-expanded", String(!expanded));
    navToggle.closest("[data-nav-parent]")?.querySelector("[data-nav-sub]")?.classList.toggle("hidden", expanded);
  }

  const paletteToggle = event.target.closest("[data-toggle-palette]");
  if (paletteToggle) document.querySelector("[data-command-palette]")?.classList.toggle("hidden");
  if (event.target.matches("[data-command-palette]")) event.target.classList.add("hidden");

  const tab = event.target.closest("[data-tab]");
  if (tab) {
    const scope = tab.closest("[data-tabs]");
    const target = tab.dataset.tab;
    scope.querySelectorAll("[data-tab]").forEach((item) => item.classList.toggle("active", item === tab));
    scope.querySelectorAll("[data-panel]").forEach((panel) => panel.classList.toggle("hidden", panel.dataset.panel !== target));
  }

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

  const dialogOpen = event.target.closest("[data-dialog-open]");
  if (dialogOpen) document.querySelector(dialogOpen.dataset.dialogOpen)?.classList.remove("hidden");
  if (event.target.closest("[data-dialog-close]") || event.target.matches("[data-dialog]")) event.target.closest("[data-dialog]")?.classList.add("hidden");

  const toast = event.target.closest("[data-toast]");
  if (toast) showToast(toast.dataset.toast || "Action completed", toast.dataset.toastDescription || "");
});

document.addEventListener("input", (event) => {
  if (event.target.matches("[data-command-input]")) {
    const query = event.target.value.toLowerCase();
    document.querySelectorAll("[data-command-item]").forEach((item) => {
      item.hidden = !item.innerText.toLowerCase().includes(query);
    });
  }
});

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
    document.body.classList.toggle("sidebar-collapsed");
  }
});

renderFallbackIcons();
renderCharts();
enhanceTables();
initKanban();
initOtp();
initForms();
initCalendar();
initCarousel();
initRichText();

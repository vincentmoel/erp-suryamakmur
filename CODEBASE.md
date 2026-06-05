# Codebase Reference

Dokumentasi arsitektur dan pola kode untuk ERP Surya Makmur. Dibaca otomatis oleh Claude Code via import di CLAUDE.md.

---

## Tech Stack

- Laravel 10, PHP 8.x
- MySQL — Yajra DataTables (AJAX), Vite, Tailwind CSS

---

## Model Overview

| Model | Tabel | Soft Delete | Catatan |
|-------|-------|-------------|---------|
| `User` | `users` | ✓ | Auth user |
| `Role` / `Permission` | `roles`, `permissions` | – | RBAC |
| `Category` | `categories` | ✓ | Master kategori produk |
| `Unit` | `units` | ✓ | Satuan produk |
| `Customer` | `customers` | ✓ | Has `type` (enum `CustomerType`), `is_active` |
| `Product` | `products` | ✓ | Has `category`, `unit`, `is_active` |
| `Vendor` | `vendors` | ✓ | Has `type` (enum `VendorType`), `is_active` |
| `Invoice` | `invoices` | ✓ | Sales invoice, has `status` (enum `InvoiceStatus`) |
| `InvoiceDetail` | `invoice_details` | – | Line item invoice, has `product_snapshot` (JSON) |
| `InvoiceDetailBatch` | `invoice_detail_batches` | – | FIFO batch record per line item invoice |
| `Purchase` | `purchases` | ✓ | Purchase order, has `status` (enum `PurchaseStatus`) |
| `PurchaseDetail` | `purchase_details` | – | Line item purchase |
| `Inventory` | `inventories` | – | Master inventory per `(product_id, unit_cost)` |
| `InventoryDetail` | `inventory_details` | – | Batch stok masuk (FIFO), `quantity` = stok tersisa |
| `InventoryLog` | `inventory_logs` | – | Audit trail mutasi stok |
| `Receipt` | `receipts` | ✓ | Bukti pembayaran invoice |
| `SalesReturn` | `sales_returns` | ✓ | Retur penjualan |

### BaseModel

Semua model extend `BaseModel` kecuali model tanpa `timestamps` (e.g., `InvoiceDetail`). BaseModel:
- Trait `CreatedByUpdatedBy` — auto-set `created_by` / `updated_by` dari `auth()->id()`
- Eager-load `user_created_by` dan `user_updated_by` di `$with`
- `$guarded = ['id', 'created_at', 'updated_at', 'deleted_at']`

Model tanpa timestamps (`InvoiceDetail`, `PurchaseDetail`, `InventoryDetail`) menggunakan `public $timestamps = false` dan extend `Model` langsung.

---

## Inventory Architecture

> **Aturan utama:** Tidak ada modul yang boleh mengubah tabel inventory secara langsung. Semua perubahan wajib melalui `App\Services\InventoryService`.

### Tiga Lapisan

#### `inventories`

Master inventory. Unik per kombinasi `(product_id, unit_cost)`.

Satu produk bisa punya **banyak** inventory record jika dibeli dengan harga modal berbeda:

```
Inventory #1 → Barang A, unit_cost = 30.000
Inventory #2 → Barang A, unit_cost = 35.000
```

Jika pembelian baru memiliki unit_cost yang sama dengan inventory yang sudah ada, **tidak dibuat record baru** — inventory yang ada di-reuse.

#### `inventory_details`

Setiap penerimaan stok dibuatkan satu baris baru, bahkan jika inventory-nya sama.

- `quantity` = **stok tersisa saat ini** (dikurangi setiap ada penjualan/retur)
- `received_at` = tanggal penerimaan (dipakai untuk urutan FIFO)

FIFO membaca `inventory_details` urut `received_at ASC` (terlama dulu).

```
Inventory #1 (Barang A, 30.000)
├── InventoryDetail #1 — qty=10, received_at=2026-01-01
└── InventoryDetail #2 — qty=15, received_at=2026-01-05

Inventory #2 (Barang A, 35.000)
└── InventoryDetail #3 — qty=20, received_at=2026-01-10
```

#### `inventory_logs`

Audit trail saja. **Tidak dipakai untuk menghitung stok.** Stok selalu dihitung dari `inventory_details.quantity`.

Kolom penting:
- `source` — `PURCHASE` | `SALE` | `SALES_RETURN` | `STOCK_OPNAME`
- `reference_id` — ID record sumber (purchase.id, invoice.id, dst.)
- `quantity` — positif = masuk, negatif = keluar
- `balance_after` — sisa stok di batch tersebut setelah mutasi

### InventoryService API

File: `app/Services/InventoryService.php`

```php
// Tambah stok (PURCHASE, SALES_RETURN, STOCK_OPNAME masuk)
InventoryService::addStock(
    productId:   $detail->product_id,
    unitCost:    $unitCost,          // harga modal efektif per unit
    quantity:    $detail->quantity,
    receivedAt:  $purchase->purchase_date,
    source:      'PURCHASE',
    referenceId: $purchase->id,
    notes:       'Purchase #' . $purchase->code,
);

// Kurangi stok FIFO (SALE, STOCK_OPNAME keluar)
InventoryService::deductStock(
    productId:   $product->id,
    quantity:    $qty,
    source:      'SALE',
    referenceId: $invoice->id,
    notes:       'Invoice #' . $invoice->code,
);

// Cek stok tersedia
$stock = InventoryService::getStock($product->id);
```

`unit_cost` untuk pembelian dihitung: `round((qty × unit_price − discount_amount) / qty)`.

---

## Enum Overview

| Enum | File | Values |
|------|------|--------|
| `Module` | `app/Enums/Module.php` | User, Role, Dashboard, Config, Category, Unit, Customer, Product, Invoice, Vendor, **Purchase** |
| `InvoiceStatus` | `app/Enums/InvoiceStatus.php` | DRAFT, WAITING_FOR_PAYMENT, PAID, PARTIALLY_PAID, CANCELLED |
| `PurchaseStatus` | `app/Enums/PurchaseStatus.php` | DRAFT, ORDERED, RECEIVED, CANCELLED |
| `CustomerType` | `app/Enums/CustomerType.php` | INDIVIDUAL, COMPANY |
| `VendorType` | `app/Enums/VendorType.php` | INDIVIDUAL, COMPANY |
| `PaymentMethod` | `app/Enums/PaymentMethod.php` | – |

Semua enum implements `label(): string`. Enum untuk status juga implements `badgeClass(): string` dan method `canXxx(): bool`.

Traits tersedia: `EnumToArray`, `EnumLabel`.

---

## Services

| Service | File | Kegunaan |
|---------|------|----------|
| `InventoryService` | `app/Services/InventoryService.php` | Satu-satunya pintu masuk untuk mutasi inventory |

---

## Helpers

| Helper | File | Kegunaan |
|--------|------|----------|
| `Encryption` | `app/Helpers/Encryption.php` | Encrypt/decrypt ID di URL. Semua route param `{encryptedId}` |
| `CodeGenerator` | `app/Helpers/CodeGenerator.php` | Auto-generate kode dokumen (INV-202606-0001, PUR-202606-0001) |
| `FileManager` | `app/Helpers/FileManager.php` | Upload file ke `storage/app/public/{folder}/` |
| `Response` | `app/Helpers/Response.php` | JSON response builder untuk AJAX |

---

## Purchase Module

### Status Flow

```
DRAFT → ORDERED → RECEIVED
  ↓         ↓
CANCELLED  CANCELLED
```

- DRAFT / ORDERED → bisa edit, bisa receive, bisa cancel
- RECEIVED → tidak bisa edit, tidak bisa cancel
- CANCELLED → final

### Receive Goods

Saat tombol "Receive Goods" diklik di halaman show:
1. `PurchaseController::receive()` dipanggil
2. Untuk setiap `PurchaseDetail`, hitung `unit_cost` efektif
3. Panggil `InventoryService::addStock()` per item
4. Update `purchase.status` → `RECEIVED`

### Routes

```
GET    /purchases              → index
GET    /purchases/create       → create
POST   /purchases              → store
GET    /purchases/{id}         → show
GET    /purchases/{id}/edit    → edit
PATCH  /purchases/{id}         → update
PATCH  /purchases/{id}/receive → receive (+ inventory)
PATCH  /purchases/{id}/cancel  → cancel
DELETE /purchases/{id}/destroy → destroy
GET    /purchases/trashed      → trashed
PATCH  /purchases/{id}/restore → restore
```

---

## Invoice Module (FIFO Deduction)

Invoice (penjualan) menggunakan `InvoiceDetailBatch` untuk mencatat batch mana yang digunakan saat FIFO deduction. Relasi: `InvoiceDetail → InvoiceDetailBatch → InventoryDetail`.

---

## AJAX Routes

File: `routes/ajax.php` — prefix `/ajax`, named `ajax.*`.

| Route | Kegunaan |
|-------|----------|
| `GET /ajax/products/{id}` | Info produk (name, sku, unit) untuk form line item |

---

## Sidebar

Konfigurasi: `config/sidebar.php`. Struktur nested PHP array. Gunakan `route` (named route), bukan URL.

Grup saat ini:
- (root) — Dashboard, Users
- **Master Data** — Categories, Units, Customers, Products
- **Sales** — Invoices
- **Purchase** — Purchases, Vendors

---

## Permission Middleware

```php
->middleware("check.permission:ModuleName,read|create|update|delete|restore")
```

`ModuleName` = value dari `Module` enum (e.g., `"Purchase"`). Permission disimpan di session.

---

## DataTable Pattern

Index views menggunakan Yajra DataTables via AJAX. Controller `index()` serve JSON jika DataTable request. Selalu eager-load relasi yang dipakai di `dataTable()` columns dalam method `query()`.

```php
public function query(): QueryBuilder
{
    return Purchase::with('vendor')->latest()->newQuery(); // eager load wajib
}
```

---

## Form Helpers (Global JS)

Tersedia di semua halaman via layout:
- `formatRupiah(number)` — format angka ke "Rp 1.000.000"
- `parseMoney(string)` — parse string rupiah ke integer
- `bindMoneyInput(displayEl, hiddenEl, callback)` — bind money input dengan hidden field

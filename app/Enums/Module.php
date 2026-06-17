<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum Module : string {
    use EnumToArray;

    // ── Urutan case = urutan tampil di sidebar & permission matrix ──
    // Module tanpa route() tidak muncul di sidebar tapi tetap ada di permission matrix.

    case Dashboard   = "Dashboard";
    case User        = "User";
    case Role        = "Role";
    case Category    = "Category";
    case Unit        = "Unit";
    case Customer    = "Customer";
    case Product     = "Product";
    case Invoice     = "Invoice";
    case Receipt     = "Receipt";
    case SalesReturn = "SalesReturn";
    case Bill        = "Bill";
    case Vendor      = "Vendor";
    case Config      = "Config";
    case Developer   = "Developer";

    // ── Sidebar ─────────────────────────────────────────────────────

    /** Named route untuk index. Null = tidak muncul di sidebar. */
    public function route(): ?string
    {
        return match($this) {
            self::Dashboard   => 'dashboard',
            self::User        => 'users.index',
            self::Role        => 'roles.index',
            self::Category    => 'categories.index',
            self::Unit        => 'units.index',
            self::Customer    => 'customers.index',
            self::Product     => 'products.index',
            self::Invoice     => 'invoices.index',
            self::Receipt     => 'receipts.index',
            self::SalesReturn => 'sales-returns.index',
            self::Bill        => 'bills.index',
            self::Vendor      => 'vendors.index',
            default           => null,
        };
    }

    /** Sidebar group label. Null = tidak masuk grup (General). */
    public function group(): ?string
    {
        return match($this) {
            self::Category, self::Unit, self::Customer, self::Product => 'Master Data',
            self::Invoice, self::Receipt, self::SalesReturn           => 'Sales',
            self::Bill, self::Vendor                                  => 'Purchase',
            default                                                   => null,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::SalesReturn => 'Sales Return',
            default           => $this->value,
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Dashboard   => 'chart-no-axes-combined',
            self::User        => 'users',
            self::Role        => 'shield',
            self::Config      => 'settings',
            self::Developer   => 'key',
            self::Category    => 'tag',
            self::Unit        => 'ruler',
            self::Customer    => 'contact',
            self::Product     => 'box',
            self::Invoice     => 'invoice',
            self::Receipt     => 'money',
            self::Vendor      => 'building',
            self::Bill        => 'receipt',
            self::SalesReturn => 'return',
        };
    }

    // ── Permissions ──────────────────────────────────────────────────

    public function permissions(): array
    {
        return match($this) {
            self::Dashboard   => ['read'],
            self::Config      => ['read', 'update'],
            self::Developer   => ['read'],
            self::Role        => ['read', 'create', 'update', 'delete'],
            self::User        => ['read', 'create', 'update', 'delete', 'restore'],
            self::Category    => ['read', 'create', 'update', 'delete', 'restore'],
            self::Unit        => ['read', 'create', 'update', 'delete', 'restore'],
            self::Customer    => ['read', 'create', 'update', 'delete', 'restore'],
            self::Product     => ['read', 'create', 'update', 'delete', 'restore'],
            self::Vendor      => ['read', 'create', 'update', 'delete', 'restore'],
            self::Invoice     => ['read', 'create', 'update', 'delete', 'restore'],
            self::Receipt     => ['read', 'create', 'delete'],
            self::Bill        => ['read', 'create', 'update', 'delete', 'restore', 'receive', 'cancel'],
            self::SalesReturn => ['read', 'create', 'update', 'delete', 'restore'],
        };
    }

    public static function actionLabel(string $action): string
    {
        return match($action) {
            'read'    => 'Read',
            'create'  => 'Create',
            'update'  => 'Update',
            'delete'  => 'Delete',
            'restore' => 'Restore',
            'receive' => 'Receive',
            'cancel'  => 'Cancel',
            default   => ucfirst($action),
        };
    }
}
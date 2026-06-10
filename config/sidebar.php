<?php

return [

    [
        'children' => [
            [
                'title' => 'general.nav_dashboard',
                'icon'  => 'chart-no-axes-combined',
                'route' => 'dashboard',
            ],
            [
                'title' => 'general.nav_users',
                'icon'  => 'users',
                'route' => 'users.index',
            ],
        ],
    ],

    [
        'group'    => 'general.group_master_data',
        'children' => [
            [
                'title' => 'general.nav_categories',
                'icon'  => 'tag',
                'route' => 'categories.index',
            ],
            [
                'title' => 'general.nav_units',
                'icon'  => 'ruler',
                'route' => 'units.index',
            ],
            [
                'title' => 'general.nav_customers',
                'icon'  => 'contact',
                'route' => 'customers.index',
            ],
            [
                'title' => 'general.nav_products',
                'icon'  => 'box',
                'route' => 'products.index',
            ],
        ],
    ],

    [
        'group'    => 'general.group_sales',
        'children' => [
            [
                'title' => 'general.nav_invoices',
                'icon'  => 'invoice',
                'route' => 'invoices.index',
            ],
            [
                'title' => 'general.nav_receipts',
                'icon'  => 'money',
                'route' => 'receipts.index',
            ],
            [
                'title' => 'general.nav_sales_returns',
                'icon'  => 'return',
                'route' => 'sales-returns.index',
            ],
        ],
    ],

    [
        'group'    => 'general.group_purchase',
        'children' => [
            [
                'title' => 'general.nav_bills',
                'icon'  => 'receipt',
                'route' => 'bills.index',
            ],
            [
                'title' => 'general.nav_vendors',
                'icon'  => 'building',
                'route' => 'vendors.index',
            ],
        ],
    ],

];

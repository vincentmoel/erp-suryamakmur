<?php

return [

    [
        'title' => 'Dashboard',
        'icon' => 'chart-no-axes-combined',
        'route' => 'dashboard',
    ],

    [
        'title' => 'Users',
        'icon' => 'users',
        'route' => 'users.index',
    ],

    [
        'group' => 'E-Commerce',
        'title' => 'E-Commerce',
        'icon' => 'shopping-cart',
        'children' => [
            [
                'title' => 'Dashboard',
                'url' => './pages/ecommerce/dashboard.html',
            ],
            [
                'title' => 'Products',
                'url' => './pages/ecommerce/products.html',
            ],
            [
                'title' => 'Orders',
                'url' => './pages/ecommerce/orders.html',
            ],
            [
                'title' => 'Customers',
                'url' => './pages/ecommerce/customers.html',
            ],
        ],
    ],

];

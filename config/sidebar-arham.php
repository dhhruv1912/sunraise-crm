<?php

use App\Helpers\Menu;
return [

    [
        'title' => 'Dashboard',
        'icon' => 'mdi mdi-home-outline',
        'route' => 'dashboard',
        'permission' => 'view dashboard',
        'active' => Menu::is('dashboard', ['sunraise', 'arham']),
        'open' => Menu::open('dashboard'),
    ],

    [
        'title' => 'Users',
        'icon' => 'mdi mdi-account-star',
        'permission' => 'view users',
        'open' => Menu::open('user'),
        'children' => [
            [
                'title' => 'List',
                'route' => 'Users',
                'permission' => 'view users',
                'active' => Menu::is('user'),
            ],
            [
                'title' => 'Log',
                'route' => 'attendance.log',
                'permission' => 'manage roles',
                'active' => Menu::is('user', 'attendance'),
            ],
            [
                'title' => 'Roles',
                'route' => 'roles.index',
                'permission' => 'manage roles',
                'active' => Menu::is('user', 'roles'),
            ],
            [
                'title' => 'Permissions',
                'route' => 'permissions.index',
                'permission' => 'manage permissions',
                'active' => Menu::is('user', 'permissions'),
            ],
            [
                'title' => 'Settings',
                'route' => ['Setting', 'user'],
                'permission' => 'manage users',
                'active' => Menu::is('user', 'setting'),
            ],
        ],
    ],

    [
        'title' => 'Vendors',
        'icon' => 'mdi mdi-account-group',
        'permission' => 'view tally',
        'open' => Menu::open('vendors'),
        'children' => [
            [
                'title' => 'List',
                'route' => 'vendors.index',
                'permission' => 'view ledger',
                'active' => Menu::is('vendors', ''),
            ],
            [
                'title' => 'Settings',
                'route' => ['Setting', 'tally'],
                'permission' => 'manage tally',
                'active' => Menu::is('tally', 'setting'),
            ],
        ],
    ],

    [
        'title' => 'Warehouses',
        'icon' => 'mdi mdi-home-silo-outline',
        'permission' => 'view tally',
        'open' => Menu::open('warehouse'),
        'children' => [
            [
                'title' => 'List',
                'route' => 'warehouse.index',
                'permission' => 'view ledger',
                'active' => Menu::is('warehouse', ''),
            ],
            [
                'title' => 'Settings',
                'route' => ['Setting', 'warehouse'],
                'permission' => 'manage tally',
                'active' => Menu::is('warehouse', 'setting'),
            ],
        ],
    ],

    [
        'title' => 'Stocks',
        'icon' => 'mdi mdi-solar-panel-large',
        'permission' => 'view tally',
        'open' => Menu::open('panels') || Menu::open('item-categories') || Menu::open('batches') || Menu::open('items') || Menu::open('panels'),
        'children' => [
            [
                'title' => 'Add',
                'route' => 'panels.receive.uploadPage',
                'permission' => 'view ledger',
                'active' => Menu::is('panels', 'receive'),
            ],
            [
                'title' => 'Categories',
                'route' => 'item_categories.index',
                'permission' => 'view stocks',
                'active' => Menu::is('item-categories'),
            ],
            [
                'title' => 'Batches',
                'route' => 'batches.index',
                'permission' => 'view stocks',
                'active' => Menu::is('batches'),
            ],
            [
                'title' => 'Items',
                'route' => 'items.index',
                'permission' => 'view stocks',
                'active' => Menu::is('items'),
            ],
            [
                'title' => 'Panels',
                'route' => 'panels.index',
                'permission' => 'view stocks',
                'active' => Menu::is('panels'),
            ],
            // [
            //     'title' => 'Attachments',
            //     'route' => ['Setting', 'tally'],
            //     'permission' => 'manage tally',
            //     'active' => Menu::is('tally', 'setting'),
            // ],
            [
                'title' => 'Settings',
                'route' => ['Setting', 'tally'],
                'permission' => 'manage tally',
                'active' => Menu::is('tally', 'setting'),
            ],
        ],
    ],

    [
        'title' => 'Tally',
        'icon' => 'mdi mdi mdi-warehouse',
        'permission' => 'view tally',
        'open' => Menu::open('tally'),
        'children' => [
            [
                'title' => 'Ledger',
                'route' => 'tally.ledger',
                'permission' => 'view ledger',
                'active' => Menu::is('tally', 'ledger'),
            ],
            [
                'title' => 'Stocks',
                'route' => 'tally.stocks',
                'permission' => 'view stocks',
                'active' => Menu::is('tally', 'stocks'),
            ],
            [
                'title' => 'Settings',
                'route' => ['Setting', 'tally'],
                'permission' => 'manage tally',
                'active' => Menu::is('tally', 'setting'),
            ],
        ],
    ],
    // [
    //     'title' => 'Settings',
    //     'icon'  => 'mdi mdi-cog-outline',
    //     'permission' => 'view settings',
    //     'open' => Menu::open('settings'),
    //     'children' => [
    //         [
    //             'title' => 'General',
    //             'route' => ['settings.module', 'general'],
    //             'permission' => 'manage settings',
    //             'active' => Menu::is('settings', 'general'),
    //         ],
    //         [
    //             'title' => 'Staff Settings',
    //             'route' => ['settings.module', 'staff'],
    //             'permission' => 'manage settings',
    //             'active' => Menu::is('settings', 'staff'),
    //         ],
    //     ]
    // ],

];

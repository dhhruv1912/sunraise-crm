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
        'title' => 'Quote Manager',
        'icon' => 'mdi mdi-chart-bubble',
        'permission' => 'view quotes',
        'open' => Menu::open('quote'),
        'children' => [
            [
                'title' => 'List',
                'route' => 'quote_master.index',
                'permission' => 'view quotes',
                'active' => Menu::is('quote', 'master'),
            ],
            [
                'title' => 'Requests',
                'route' => 'quote_requests.index',
                'permission' => 'manage quotes',
                'active' => Menu::is('quote', 'requests'),
            ],
            [
                'title' => 'Settings',
                'route' => ['Setting', 'quote'],
                'permission' => 'manage quotes',
                'active' => Menu::is('quote', 'setting'),
            ],
        ],
    ],



    [
        'title' => 'Marketing',
        'icon' => 'mdi mdi-bullseye-arrow',
        'permission' => 'view marketing',
        'open' => Menu::open('marketing') || Menu::open('quotations'),
        'children' => [
            [
                'title' => 'Lead',
                'route' => 'marketing.index',
                'permission' => 'view marketing',
                'active' => Menu::is('marketing', ''),
            ],
            [
                'title' => 'Quotation',
                'route' => 'quotations.index',
                'permission' => 'view marketing',
                'active' => Menu::is('quotations', ''),
            ],
            [
                'title' => 'Settings',
                'route' => ['Setting', 'marketing'],
                'permission' => 'manage marketing',
                'active' => Menu::is('marketing', 'setting'),
            ],
        ],
    ],
    [
        'title' => 'Projects',
        'icon' => 'mdi mdi-solar-power-variant',
        'permission' => 'view projects',
        'open' => Menu::open('projects') || Menu::open('documents') || Menu::open('customers'),
        'children' => [
            [
                'title' => 'List',
                'route' => 'projects.index',
                'permission' => 'view projects',
                'active' => Menu::is('projects', ''),
            ],
            [
                'title' => 'Documents',
                'route' => 'documents.index',
                'permission' => 'view documents',
                'active' => Menu::is('documents', ''),
            ],
            [
                'title' => 'Customers',
                'route' => 'customers.index',
                'permission' => 'view customers',
                'active' => Menu::is('customers', ''),
            ],
            [
                'title' => 'Settings',
                'route' => ['Setting', 'projects'],
                'permission' => 'manage projects',
                'active' => Menu::is('projects', 'setting'),
            ],
        ],
    ],

    [
        'title' => 'Billing',
        'icon' => 'mdi mdi-cash-sync',
        'permission' => 'view billing',
        'open' => Menu::open('billing'),
        'children' => [
            [
                'title' => 'List',
                'route' => 'invoices.index',
                'permission' => 'view billing',
                'active' => Menu::is('billing', 'invoices'),
            ],
            [
                'title' => 'Settings',
                'route' => ['Setting', 'billing'],
                'permission' => 'manage billing',
                'active' => Menu::is('billing', 'setting'),
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
    
    [
        'title' => 'Settings',
        'icon'  => 'mdi mdi-cog-outline',
        'permission' => 'view settings',
        'open' => Menu::open('settings'),
        'children' => [
            [
                'title' => 'General',
                'route' => ['settings.module', 'general'],
                'permission' => 'manage settings',
                'active' => Menu::is('settings', 'general'),
            ],
            [
                'title' => 'Staff Settings',
                'route' => ['settings.module', 'staff'],
                'permission' => 'manage settings',
                'active' => Menu::is('settings', 'staff'),
            ],
        ]
    ],

];

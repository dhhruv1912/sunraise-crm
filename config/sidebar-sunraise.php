<?php

use App\Helpers\Menu;
return [

    [
        'title' => 'Dashboard',
        'icon' => 'fa-solid fa-chart-line',
        'route' => 'dashboard',
        'permission' => 'dashboard.view',
        'active' => Menu::is('dashboard', ['sunraise', 'arham']),
        'open' => Menu::open('dashboard'),
    ],

    [
        'title' => 'Users',
        'icon' => 'fa-solid fa-users',
        'permission' => 'users.view',
        'open' => Menu::open('users'),
        'children' => [
            [
                'title' => 'List',
                'route' => 'users.view.list',
                'icon' => "fa-solid fa-bars-staggered",
                'permission' => 'users.view',
                'active' => Menu::is('users'),
            ],
            [
                'title' => 'Log',
                'route' => 'attendance.view.list',
                'icon' => "fa-solid fa-door-open",
                'permission' => 'users.attendance',
                'active' => Menu::is('user', 'attendance'),
            ],
            [
                'title' => 'Roles',
                'route' => 'roles.view.list',
                'icon' => "fa-solid fa-user-tie",
                'permission' => 'users.roles',
                'active' => Menu::is('roles',),
            ],
            [
                'title' => 'Permissions',
                'route' => 'permissions.view.list',
                'icon' => "fa-solid fa-user-shield",
                'permission' => 'users.permissions',
                'active' => Menu::is('permissions'),
            ],
        ],
    ],


    [
        'title' => 'Quote Manager',
        'icon' => 'fa-solid fa-quote-left',
        'permission' => 'quote.view',
        'open' => Menu::open('quote'),
        'children' => [
            [
                'title' => 'List',
                'route' => 'quote_master.view.list',
                'icon' => "fa-solid fa-",
                'permission' => 'quote.master.view',
                'active' => Menu::is('quote', 'master'),
            ],
            [
                'title' => 'Requests',
                'route' => 'quote_requests.view.list',
                'icon' => "fa-solid fa-",
                'permission' => 'quote.request.view',
                'active' => Menu::is('quote', 'requests'),
            ],
        ],
    ],



    [
        'title' => 'Marketing',
        'icon' => 'fa-solid fa-bullseye',
        'permission' => 'marketing.view',
        'open' => Menu::open('lead') || Menu::open('quotations'),
        'children' => [
            [
                'title' => 'Lead',
                'route' => 'leads.view.list',
                'icon' => "fa-solid fa-",
                'permission' => 'marketing.lead.view',
                'active' => Menu::is('marketing', ''),
            ],
            [
                'title' => 'Quotation',
                'route' => 'quotations.view.list',
                'icon' => "fa-solid fa-",
                'permission' => 'marketing.quotation.view',
                'active' => Menu::is('quotations', ''),
            ],
        ],
    ],
    [
        'title' => 'Projects',
        'icon' => 'mdi mdi-solar-power-variant',
        'permission' => 'project.view',
        'open' => Menu::open('projects') || Menu::open('documents') || Menu::open('customers'),
        'children' => [
            [
                'title' => 'List',
                'route' => 'projects.view.list',
                'icon' => "fa-solid fa-",
                'permission' => 'project.view',
                'active' => Menu::is('projects', ''),
            ],
            [
                'title' => 'Documents',
                'route' => 'documents.view.list',
                'icon' => "fa-solid fa-",
                'permission' => 'project.documents.view',
                'active' => Menu::is('documents', ''),
            ],
            [
                'title' => 'Customers',
                'route' => 'customers.view.list',
                'icon' => "fa-solid fa-",
                'permission' => 'project.customer.view',
                'active' => Menu::is('customers', ''),
            ],
        ],
    ],

    [
        'title' => 'Billing',
        'icon' => 'mdi mdi-cash-sync',
        'permission' => 'billing.view',
        'open' => Menu::open('invoices'),
        'children' => [
            [
                'title' => 'List',
                'route' => 'invoices.view.list',
                'icon' => "fa-solid fa-",
                'permission' => 'billing.view',
                'active' => Menu::is('invoices'),
            ],
        ],
    ],

    [
        'title' => 'Tally',
        'icon' => 'mdi mdi mdi-warehouse',
        'permission' => 'tally.dashboard',
        'open' => Menu::open('tally'),
        'children' => [
            [
                'title' => 'Dashboard',
                'route' => 'tally.dashboard',
                'icon' => "fa-solid fa-",
                'permission' => 'tally.dashboard',
                'active' => Menu::is('tally', 'dashboard'),
            ],
            [
                'title' => 'Ledger',
                'route' => 'tally.ledger',
                'icon' => "fa-solid fa-",
                'permission' => 'tally.ledger',
                'active' => Menu::is('tally', 'ledger'),
            ],
            [
                'title' => 'Stocks',
                'route' => 'tally.stocks',
                'icon' => "fa-solid fa-",
                'permission' => 'tally.stock',
                'active' => Menu::is('tally', 'stocks'),
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
                'icon' => "fa-solid fa-",
                'permission' => 'manage settings',
                'active' => Menu::is('settings', 'general'),
            ],
            [
                'title' => 'Staff Settings',
                'route' => ['settings.module', 'staff'],
                'icon' => "fa-solid fa-",
                'permission' => 'manage settings',
                'active' => Menu::is('settings', 'staff'),
            ],
        ]
    ],

];

<?php

namespace App\Helpers;

class MenuArray
{
    public static function items()
    {
        return [


            [
                'title' => 'Quote Manager',
                'icon' => 'mdi mdi-chart-bubble',
                'permission' => 'view quotes',
                'open' => Menu::open('quote'),
                'children' => [
                    [
                        'title' => 'List',
                        // 'route' => 'Quote',
                        'permission' => 'view quotes',
                        'active' => Menu::is('quote', ''),
                    ],
                    [
                        'title' => 'Requests',
                        // 'route' => 'QuoteRequests',
                        'permission' => 'manage quotes',
                        'active' => Menu::is('quote', 'requests'),
                    ],
                    [
                        'title' => 'Settings',
                        // 'route' => ['Setting', 'quote'],
                        'permission' => 'manage quotes',
                        'active' => Menu::is('quote', 'setting'),
                    ],
                ],
            ],

            [
                'title' => 'Projects',
                'icon' => 'mdi mdi-solar-power-variant',
                'permission' => 'view projects',
                'open' => Menu::open('project'),
                'children' => [
                    [
                        'title' => 'List',
                        // 'route' => 'ProjectLead',
                        'permission' => 'view projects',
                        'active' => Menu::is('project', 'list'),
                    ],
                    [
                        'title' => 'Documents',
                        // 'route' => 'Documents',
                        'permission' => 'view projects',
                        'active' => Menu::is('documents'),
                    ],
                    [
                        'title' => 'Settings',
                        // 'route' => ['Setting', 'project'],
                        'permission' => 'manage projects',
                        'active' => Menu::is('project', 'setting'),
                    ],
                ],
            ],

            [
                'title' => 'Marketing',
                'icon' => 'mdi mdi-bullseye-arrow',
                'permission' => 'view marketing',
                'open' => Menu::open('marketing'),
                'children' => [
                    [
                        'title' => 'Lead',
                        // 'route' => 'MarketingLead',
                        'permission' => 'view marketing',
                        'active' => Menu::is('marketing', 'list'),
                    ],
                    [
                        'title' => 'Settings',
                        // 'route' => ['Setting', 'marketing'],
                        'permission' => 'manage marketing',
                        'active' => Menu::is('marketing', 'setting'),
                    ],
                ],
            ],

            [
                'title' => 'Tally',
                'icon' => 'mdi mdi-warehouse',
                'permission' => 'view tally',
                'open' => Menu::open('tally'),
                'children' => [
                    [
                        'title' => 'Ledger',
                        // 'route' => 'Ledger',
                        'permission' => 'view tally',
                        'active' => Menu::is('tally', 'ledger'),
                    ],
                    [
                        'title' => 'Stocks',
                        // 'route' => 'Stocks',
                        'permission' => 'manage tally',
                        'active' => Menu::is('tally', 'stocks'),
                    ],
                ],
            ],

        ];
    }
}

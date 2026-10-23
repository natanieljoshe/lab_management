<?php

return [
    'days' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],

    'auto_schedule_days' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],

    // In minutes from 00:00
    'auto_schedule_start_min' => 450, // 07:30
    'auto_schedule_end_min' => 990,   // 16:30 (on Monday, last auto slot finishes at 16:00 = 960)

    'day_auto_end_min' => [
        'Senin' => 960,  // 16:00 (slot ends at 16:00 because 16:00-17:00 is manual only)
        'Selasa' => 990, // 16:30
        'Rabu' => 990,   // 16:30
        'Kamis' => 990,  // 16:30
        'Jumat' => 990,  // 16:30
    ],

    // Forbidden windows (hard constraint)
    'forbidden_windows' => [
        'Senin' => [
            [
                'start' => 630, // 10:30
                'end' => 780,   // 13:00
                'label' => 'Kebaktian Umum',
            ],
        ],
        'Jumat' => [
            [
                'start' => 630, // 10:30
                'end' => 870,   // 14:30
                'label' => 'Jam Kemahasiswaan',
            ],
        ],
        'Selasa' => [],
        'Rabu' => [],
        'Kamis' => [],
    ],

    // Grid slots per day
    'slots' => [
        'Senin' => [
            ['start' => 450, 'end' => 510, 'label' => '07.30 - 08.30', 'is_auto' => true],
            ['start' => 510, 'end' => 570, 'label' => '08.30 - 09.30', 'is_auto' => true],
            ['start' => 570, 'end' => 630, 'label' => '09.30 - 10.30', 'is_auto' => true],
            ['start' => 630, 'end' => 690, 'label' => '10.30 - 11.30', 'is_auto' => false, 'forbidden' => true],
            ['start' => 690, 'end' => 750, 'label' => '11.30 - 12.30', 'is_auto' => false, 'forbidden' => true],
            ['start' => 750, 'end' => 780, 'label' => '12.30 - 13.00', 'is_auto' => false, 'forbidden' => true],
            ['start' => 780, 'end' => 840, 'label' => '13.00 - 14.00', 'is_auto' => true],
            ['start' => 840, 'end' => 900, 'label' => '14.00 - 15.00', 'is_auto' => true],
            ['start' => 900, 'end' => 960, 'label' => '15.00 - 16.00', 'is_auto' => true],
            ['start' => 960, 'end' => 1020, 'label' => '16.00 - 17.00', 'is_auto' => false, 'manual_only' => true],
            ['start' => 1020, 'end' => 1080, 'label' => '17.00 - 18.00', 'is_auto' => false, 'manual_only' => true],
            ['start' => 1080, 'end' => 1140, 'label' => '18.00 - 19.00', 'is_auto' => false, 'manual_only' => true],
        ],
        'Selasa' => [
            ['start' => 450, 'end' => 510, 'label' => '07.30 - 08.30', 'is_auto' => true],
            ['start' => 510, 'end' => 570, 'label' => '08.30 - 09.30', 'is_auto' => true],
            ['start' => 570, 'end' => 630, 'label' => '09.30 - 10.30', 'is_auto' => true],
            ['start' => 630, 'end' => 690, 'label' => '10.30 - 11.30', 'is_auto' => true],
            ['start' => 690, 'end' => 750, 'label' => '11.30 - 12.30', 'is_auto' => true],
            ['start' => 750, 'end' => 810, 'label' => '12.30 - 13.30', 'is_auto' => true],
            ['start' => 810, 'end' => 870, 'label' => '13.30 - 14.30', 'is_auto' => true],
            ['start' => 870, 'end' => 930, 'label' => '14.30 - 15.30', 'is_auto' => true],
            ['start' => 930, 'end' => 990, 'label' => '15.30 - 16.30', 'is_auto' => true],
            ['start' => 990, 'end' => 1050, 'label' => '16.30 - 17.30', 'is_auto' => false, 'manual_only' => true],
            ['start' => 1050, 'end' => 1110, 'label' => '17.30 - 18.30', 'is_auto' => false, 'manual_only' => true],
            ['start' => 1110, 'end' => 1170, 'label' => '18.30 - 19.30', 'is_auto' => false, 'manual_only' => true],
            ['start' => 1170, 'end' => 1230, 'label' => '19.30 - 20.30', 'is_auto' => false, 'manual_only' => true],
            ['start' => 1230, 'end' => 1290, 'label' => '20.30 - 21.30', 'is_auto' => false, 'manual_only' => true],
        ],
        'Rabu' => [
            ['start' => 450, 'end' => 510, 'label' => '07.30 - 08.30', 'is_auto' => true],
            ['start' => 510, 'end' => 570, 'label' => '08.30 - 09.30', 'is_auto' => true],
            ['start' => 570, 'end' => 630, 'label' => '09.30 - 10.30', 'is_auto' => true],
            ['start' => 630, 'end' => 690, 'label' => '10.30 - 11.30', 'is_auto' => true],
            ['start' => 690, 'end' => 750, 'label' => '11.30 - 12.30', 'is_auto' => true],
            ['start' => 750, 'end' => 810, 'label' => '12.30 - 13.30', 'is_auto' => true],
            ['start' => 810, 'end' => 870, 'label' => '13.30 - 14.30', 'is_auto' => true],
            ['start' => 870, 'end' => 930, 'label' => '14.30 - 15.30', 'is_auto' => true],
            ['start' => 930, 'end' => 990, 'label' => '15.30 - 16.30', 'is_auto' => true],
            ['start' => 990, 'end' => 1050, 'label' => '16.30 - 17.30', 'is_auto' => false, 'manual_only' => true],
            ['start' => 1050, 'end' => 1110, 'label' => '17.30 - 18.30', 'is_auto' => false, 'manual_only' => true],
            ['start' => 1110, 'end' => 1170, 'label' => '18.30 - 19.30', 'is_auto' => false, 'manual_only' => true],
            ['start' => 1170, 'end' => 1230, 'label' => '19.30 - 20.30', 'is_auto' => false, 'manual_only' => true],
            ['start' => 1230, 'end' => 1290, 'label' => '20.30 - 21.30', 'is_auto' => false, 'manual_only' => true],
        ],
        'Kamis' => [
            ['start' => 450, 'end' => 510, 'label' => '07.30 - 08.30', 'is_auto' => true],
            ['start' => 510, 'end' => 570, 'label' => '08.30 - 09.30', 'is_auto' => true],
            ['start' => 570, 'end' => 630, 'label' => '09.30 - 10.30', 'is_auto' => true],
            ['start' => 630, 'end' => 690, 'label' => '10.30 - 11.30', 'is_auto' => true],
            ['start' => 690, 'end' => 750, 'label' => '11.30 - 12.30', 'is_auto' => true],
            ['start' => 750, 'end' => 810, 'label' => '12.30 - 13.30', 'is_auto' => true],
            ['start' => 810, 'end' => 870, 'label' => '13.30 - 14.30', 'is_auto' => true],
            ['start' => 870, 'end' => 930, 'label' => '14.30 - 15.30', 'is_auto' => true],
            ['start' => 930, 'end' => 990, 'label' => '15.30 - 16.30', 'is_auto' => true],
            ['start' => 990, 'end' => 1050, 'label' => '16.30 - 17.30', 'is_auto' => false, 'manual_only' => true],
            ['start' => 1050, 'end' => 1110, 'label' => '17.30 - 18.30', 'is_auto' => false, 'manual_only' => true],
            ['start' => 1110, 'end' => 1170, 'label' => '18.30 - 19.30', 'is_auto' => false, 'manual_only' => true],
            ['start' => 1170, 'end' => 1230, 'label' => '19.30 - 20.30', 'is_auto' => false, 'manual_only' => true],
            ['start' => 1230, 'end' => 1290, 'label' => '20.30 - 21.30', 'is_auto' => false, 'manual_only' => true],
        ],
        'Jumat' => [
            ['start' => 450, 'end' => 510, 'label' => '07.30 - 08.30', 'is_auto' => true],
            ['start' => 510, 'end' => 570, 'label' => '08.30 - 09.30', 'is_auto' => true],
            ['start' => 570, 'end' => 630, 'label' => '09.30 - 10.30', 'is_auto' => true],
            ['start' => 630, 'end' => 690, 'label' => '10.30 - 11.30', 'is_auto' => false, 'forbidden' => true],
            ['start' => 690, 'end' => 750, 'label' => '11.30 - 12.30', 'is_auto' => false, 'forbidden' => true],
            ['start' => 750, 'end' => 810, 'label' => '12.30 - 13.30', 'is_auto' => false, 'forbidden' => true],
            ['start' => 810, 'end' => 870, 'label' => '13.30 - 14.30', 'is_auto' => false, 'forbidden' => true],
            ['start' => 870, 'end' => 930, 'label' => '14.30 - 15.30', 'is_auto' => true],
            ['start' => 930, 'end' => 990, 'label' => '15.30 - 16.30', 'is_auto' => true],
            ['start' => 990, 'end' => 1050, 'label' => '16.30 - 17.30', 'is_auto' => false, 'manual_only' => true],
            ['start' => 1050, 'end' => 1110, 'label' => '17.30 - 18.30', 'is_auto' => false, 'manual_only' => true],
            ['start' => 1110, 'end' => 1170, 'label' => '18.30 - 19.30', 'is_auto' => false, 'manual_only' => true],
        ],
    ],

    // Cost weights for generator
    'costs' => [
        'external_lab' => 5,
        'cross_semester_clash' => 50,
        'path_loss' => 1000,
        'soft_spread_multiplier' => 3,
    ],

    // Cross semester dependency mapping: e.g. sem 1 relies on sem 3; sem 2 relies on sem 4
    'cross_semester_pairs' => [
        1 => 3,
        2 => 4,
    ],
];

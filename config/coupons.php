<?php

return [
    'SAVE' => [
        'type' => 'percentage',
        'allowed_values' => [10, 20, 30, 50],
        'max_discount' => 500,   // cap
    ],

    'FLAT' => [
        'type' => 'flat',
        'allowed_values' => [50, 100, 200],
    ],
];

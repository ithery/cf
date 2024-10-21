<?php

return [
    'debugbar' => [
        'collectors' => [
            'cache' => true,
        ],
        'editor' => c::env('DEBUGBAR_EDITOR', 'vscode'),
    ],
];

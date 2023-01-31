<?php

return [
    'snappy' => [

        'pdf' => [
            'enabled' => true,
            'binary' => c::env('WKHTML_PDF_BINARY', '/usr/local/bin/wkhtmltopdf'),
            'timeout' => false,
            'options' => [],
            'env' => [],
        ],

        'image' => [
            'enabled' => true,
            'binary' => c::env('WKHTML_IMG_BINARY', '/usr/local/bin/wkhtmltoimage'),
            'timeout' => false,
            'options' => [],
            'env' => [],
        ],
    ]
];

<?php
return [
    'openai'=> [
        'request_timeout' => c::env('OPENAI_REQUEST_TIMEOUT', 30),
    ]
];

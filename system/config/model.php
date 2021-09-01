<?php

/**
 * Description of log
 *
 * @author Hery
 */
return [
    'scout' => [
        'chunk' => [
            'searchable' => 500,
            'unsearchable' => 500,
        ],
        'after_commit' => false,
        'queue' => true,
        'soft_delete' => true,
    ]
];

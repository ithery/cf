<?php
class CElement_FormInput_EditorJs_DefaultConfig {
    protected static $defaultConfig = [

        /**
         * Editor settings.
         */
        'editorSettings' => [
            'placeholder' => '',
            'initialBlock' => 'paragraph',
            'autofocus' => false,
        ],

        /**
         * Configure tools.
         */
        'toolSettings' => [
            'paragraph' => [
                'enabled' => true,
                'inlineToolbar' => true,
                'placeholder' => '',
                'shortcut' => 'CMD+SHIFT+P',
            ],
            'header' => [
                'enabled' => true,
                'placeholder' => 'Heading',
                'shortcut' => 'CMD+SHIFT+H'
            ],
            'list' => [
                'enabled' => true,
                'inlineToolbar' => true,
                'shortcut' => 'CMD+SHIFT+L'
            ],
            'code' => [
                'enabled' => false,
                'placeholder' => '',
                'shortcut' => 'CMD+SHIFT+C'
            ],
            'link' => [
                'enabled' => false,
                'shortcut' => 'CMD+SHIFT+L'
            ],
            'image' => [
                'enabled' => true,
                'isSimple' => false,
                'shortcut' => 'CMD+SHIFT+I',
                'path' => 'editorjs/image',
                'disk' => 'local-temp',
                'alterations' => [
                    'resize' => [
                        'width' => false, // integer
                        'height' => false, // integer
                    ],
                    'optimize' => true, // true or false
                    'adjustments' => [
                        'brightness' => false, // -100 to 100
                        'contrast' => false, // -100 to 100
                        'gamma' => false // 0.1 to 9.99
                    ],
                    'effects' => [
                        'blur' => false, // 0 to 100
                        'pixelate' => false, // 0 to 100
                        'greyscale' => false, // true or false
                        'sepia' => false, // true or false
                        'sharpen' => false, // 0 to 100
                    ]
                ],
                'thumbnails' => [
                    // Specify as many thumbnails as required. Key is used as the name.
                    '_small' => [
                        'resize' => [
                            'width' => 250, // integer
                            'height' => 250, // integer
                        ],
                        'optimize' => true, // true or false
                        'adjustments' => [
                            'brightness' => false, // -100 to 100
                            'contrast' => false, // -100 to 100
                            'gamma' => false // 0.1 to 9.99
                        ],
                        'effects' => [
                            'blur' => false, // 0 to 100
                            'pixelate' => false, // 0 to 100
                            'greyscale' => false, // true or false
                            'sepia' => false, // true or false
                            'sharpen' => false, // 0 to 100
                        ]
                    ]
                ]
            ],
            'inlineCode' => [
                'enabled' => false,
                'shortcut' => 'CMD+SHIFT+A',
            ],
            'checklist' => [
                'enabled' => false,
                'inlineToolbar' => true,
                'shortcut' => 'CMD+SHIFT+J',
            ],
            'marker' => [
                'enabled' => true,
                'shortcut' => 'CMD+SHIFT+M',
            ],
            'delimiter' => [
                'enabled' => true,
            ],
            'table' => [
                'enabled' => true,
                'inlineToolbar' => true,
            ],
            'raw' => [
                'enabled' => false,
                'placeholder' => '',
            ],
            'embed' => [
                'enabled' => false,
                'inlineToolbar' => true,
                'services' => [
                    'codepen' => true,
                    'imgur' => false,
                    'vimeo' => true,
                    'youtube' => true
                ],
            ],
        ],

        /**
         * Output validation config
         * https://github.com/editor-js/editorjs-php.
         */
        'validationSettings' => [
            'tools' => [
                'header' => [
                    'text' => [
                        'type' => 'string',
                    ],
                    'level' => [
                        'type' => 'int',
                        'canBeOnly' => [1, 2, 3, 4, 5]
                    ],
                    'alignment' => [
                        'type' => 'string',
                        'canBeOnly' => ['left', 'center', 'right', 'justify'],
                        'required' => false,
                    ]
                ],
                'paragraph' => [
                    'text' => [
                        'type' => 'string',
                        'allowedTags' => 'i,b,u,a[href],span[class],code[class],mark[class]'
                    ],
                    'alignment' => [
                        'type' => 'string',
                        'canBeOnly' => ['left', 'center', 'right', 'justify'],
                        'required' => false,
                    ],

                ],
                'list' => [
                    'style' => [
                        'type' => 'string',
                        'canBeOnly' => [
                            0 => 'ordered',
                            1 => 'unordered',
                        ],
                    ],
                    'items' => [
                        'type' => 'array',
                        'data' => [
                            '-' => [
                                'type' => 'string',
                                'allowedTags' => 'i,b,u,a[href]',
                            ],
                        ],
                    ],
                ],
                'image' => [
                    'file' => [
                        'type' => 'array',
                        'data' => [
                            'url' => [
                                'type' => 'string',
                            ],
                            'thumbnails' => [
                                'type' => 'array',
                                'required' => false,
                                'data' => [
                                    '-' => [
                                        'type' => 'string',
                                    ]
                                ],
                            ]
                        ],
                    ],
                    'caption' => [
                        'type' => 'string'
                    ],
                    'withBorder' => [
                        'type' => 'boolean'
                    ],
                    'withBackground' => [
                        'type' => 'boolean'
                    ],
                    'stretched' => [
                        'type' => 'boolean'
                    ]
                ],
                'code' => [
                    'code' => [
                        'type' => 'string'
                    ]
                ],
                'linkTool' => [
                    'link' => [
                        'type' => 'string'
                    ],
                    'meta' => [
                        'type' => 'array',
                        'data' => [
                            'title' => [
                                'type' => 'string',
                            ],
                            'description' => [
                                'type' => 'string',
                            ],
                            'image' => [
                                'type' => 'array',
                                'required' => false,
                                'data' => [
                                    'url' => [
                                        'type' => 'string',
                                    ],
                                ]
                            ]
                        ]
                    ]
                ],
                'checklist' => [
                    'items' => [
                        'type' => 'array',
                        'data' => [
                            '-' => [
                                'type' => 'array',
                                'data' => [
                                    'text' => [
                                        'type' => 'string',
                                        'required' => false
                                    ],
                                    'checked' => [
                                        'type' => 'boolean',
                                        'required' => false
                                    ],
                                ],

                            ],
                        ],
                    ],
                ],
                'delimiter' => [

                ],
                'table' => [
                    'withHeadings' => [
                        'type' => 'boolean',
                        'required' => false
                    ],
                    'content' => [
                        'type' => 'array',
                        'data' => [
                            '-' => [
                                'type' => 'array',
                                'data' => [
                                    '-' => [
                                        'type' => 'string',
                                        'allowedTags' => 'i,b,u,a[href],span[class],code[class],mark[class]'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'raw' => [
                    'html' => [
                        'type' => 'string',
                        'allowedTags' => '*',
                    ]
                ],
                'embed' => [
                    'service' => [
                        'type' => 'string'
                    ],
                    'source' => [
                        'type' => 'string'
                    ],
                    'embed' => [
                        'type' => 'string'
                    ],
                    'width' => [
                        'type' => 'int'
                    ],
                    'height' => [
                        'type' => 'int'
                    ],
                    'caption' => [
                        'type' => 'string',
                        'required' => false,
                    ],
                ]
            ]
        ]
    ];

    public static function get($key, $default = null) {
        $result = carr::get(static::$defaultConfig, $key, $default);
        if ($result === null) {
            cdbg::dd($key);
        }

        return $result;
    }

    public static function data() {
        return static::$defaultConfig;
    }
}

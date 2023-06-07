<?php

trait CParser_HtmlParser_ConstantTrait {
    protected static $formTags = [
        'input',
        'option',
        'optgroup',
        'select',
        'button',
        'datalist',
        'textarea'
    ];
    protected static $pTag = [
        'p',
    ];
    protected static $openImpliesClose = [];
    protected static $voidElements = [
        'area',
        'base',
        'basefont',
        'br',
        'col',
        'command',
        'embed',
        'frame',
        'hr',
        'img',
        'input',
        'isindex',
        'keygen',
        'link',
        'meta',
        'param',
        'source',
        'track',
        'wbr'
    ];
    protected static $foreignContextElements = [
        'math',
        'svg',
    ];
    protected static $htmlIntegrationElements = [
        'mi',
        'mo',
        'mn',
        'ms',
        'mtext',
        'annotation-xml',
        'foreignObject',
        'desc',
        'title'
    ];

    protected static function rebuildConstant() {
        static::$openImpliesClose = [
            'tr' => ['tr', 'th', 'td'],
            'th' => ['th'],
            'td' => ['thead', 'th', 'td'],
            'body' => ['head', 'link', 'script'],
            'li' => ['li'],
            'p' => static::$pTag,
            'h1' => static::$pTag,
            'h2' => static::$pTag,
            'h3' => static::$pTag,
            'h4' => static::$pTag,
            'h5' => static::$pTag,
            'h6' => static::$pTag,
            'select' => static::$formTags,
            'input' => static::$formTags,
            'output' => static::$formTags,
            'button' => static::$formTags,
            'datalist' => static::$formTags,
            'textarea' => static::$formTags,
            'option' => ['option'],
            'optgroup' => ['optgroup', 'option'],
            'dd' => ['dt', 'dd'],
            'dt' => ['dt', 'dd'],
            'address' => static::$pTag,
            'article' => static::$pTag,
            'aside' => static::$pTag,
            'blockquote' => static::$pTag,
            'details' => static::$pTag,
            'div' => static::$pTag,
            'dl' => static::$pTag,
            'fieldset' => static::$pTag,
            'figcaption' => static::$pTag,
            'figure' => static::$pTag,
            'footer' => static::$pTag,
            'form' => static::$pTag,
            'header' => static::$pTag,
            'hr' => static::$pTag,
            'main' => static::$pTag,
            'nav' => static::$pTag,
            'ol' => static::$pTag,
            'pre' => static::$pTag,
            'section' => static::$pTag,
            'table' => static::$pTag,
            'ul' => static::$pTag,
            'rt' => ['rt', 'rp'],
            'rp' => ['rt', 'rp'],
            'tbody' => ['thead', 'tbody'],
            'tfoot' => ['thead', 'tbody']
        ];
    }
}

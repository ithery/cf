<?php

class CManager_EditorJs {
    /**
     * List of callbacks that can render blocks.
     *
     * @var array<Closure>
     */
    protected array $rendererCallbacks = [];

    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public static function createImageUploadHandler($options = []) {
        return new CManager_EditorJs_ImageUploadHandler($options);
    }

    public function __construct() {
        $this->registerDefaultRenderer();
    }

    /**
     * Add a custom render callback for the given block.
     *
     * @param string   $block    Name of the block, as defined in the JSON
     * @param callable $callback Closure that returns a string (or a Stringable)
     *
     * @return void
     */
    public function addRenderer(string $block, callable $callback): void {
        $this->rendererCallbacks[$block] = $callback;
    }

    /**
     * Renders the given EditorJS data to safe HTML.
     *
     * @param mixed $data
     *
     * @return \CBase_HtmlString safe, directly returnable string
     */
    public function generateHtmlOutput($data) {
        if (empty($data) || $data == new stdClass()) {
            return new CBase_HtmlString('');
        }

        // Clean non-string data
        if (!is_string($data)) {
            try {
                $data = json_encode($data, JSON_THROW_ON_ERROR);
            } catch (JsonException $exception) {
                // noop
            }
        }

        $config = CElement_FormInput_EditorJs_DefaultConfig::get('validationSettings');

        try {
            // Initialize Editor backend and validate structure
            $editor = new CElement_FormInput_EditorJs_EditorHandler($data, json_encode($config));

            // Get sanitized blocks (according to the rules from configuration)
            $blocks = $editor->getBlocks();
            $htmlOutput = '';

            foreach ($blocks as $block) {
                if (array_key_exists($block['type'], $this->rendererCallbacks)) {
                    $htmlOutput .= $this->rendererCallbacks[$block['type']]($block);
                }
            }

            return new CBase_HtmlString(
                c::view('cresenity.element.editorjs.content', ['content' => $htmlOutput])->render()
            );
        } catch (CElement_FormInput_EditorJs_EditorJSException $exception) {
            // process exception
            return new CBase_HtmlString(
                "Something went wrong: {$exception->getMessage()}"
            );
        }
    }

    protected function getBlockData($block) {
        $data = $block['data'];
        if (isset($block['tunes'])) {
            $alignment = carr::get($block, 'tunes.alignment.alignment');
            if ($data) {
                $data['alignment'] = $alignment;
            }
        }

        return $data;
    }

    /**
     * Registers all default render helpers.
     */
    protected function registerDefaultRenderer() {
        $this->addRenderer(
            'header',
            function ($block) {
                return c::view('cresenity.element.editorjs.heading', $this->getBlockData($block))->render();
            }
        );

        $this->addRenderer(
            'paragraph',
            function ($block) {
                return c::view('cresenity.element.editorjs.paragraph', $this->getBlockData($block))->render();
            }
        );

        $this->addRenderer(
            'list',
            function ($block) {
                return c::view('cresenity.element.editorjs.list', $this->getBlockData($block))->render();
            }
        );

        $this->addRenderer(
            'image',
            function ($block) {
                return c::view('cresenity.element.editorjs.image', array_merge($this->getBlockData($block), [
                    'classes' => $this->calculateImageClasses($this->getBlockData($block))
                ]))->render();
            }
        );

        $this->addRenderer(
            'code',
            function ($block) {
                return c::view('cresenity.element.editorjs.code', $this->getBlockData($block))->render();
            }
        );

        $this->addRenderer(
            'linkTool',
            function ($block) {
                return c::view('cresenity.element.editorjs.link', $this->getBlockData($block))->render();
            }
        );

        $this->addRenderer(
            'checklist',
            function ($block) {
                return c::view('cresenity.element.editorjs.checklist', $this->getBlockData($block))->render();
            }
        );

        $this->addRenderer(
            'delimiter',
            function ($block) {
                return c::view('cresenity.element.editorjs.delimiter', $this->getBlockData($block))->render();
            }
        );

        $this->addRenderer(
            'table',
            function ($block) {
                return c::view('cresenity.element.editorjs.table', $this->getBlockData($block))->render();
            }
        );

        $this->addRenderer(
            'raw',
            function ($block) {
                return c::view('cresenity.element.editorjs.raw', $this->getBlockData($block))->render();
            }
        );

        $this->addRenderer(
            'embed',
            function ($block) {
                return c::view('cresenity.element.editorjs.embed', $this->getBlockData($block))->render();
            }
        );
    }

    /**
     * @param $blockData
     *
     * @return string
     */
    protected function calculateImageClasses($blockData) {
        $classes = [];
        foreach ($blockData as $key => $data) {
            if (is_bool($data) && $data === true) {
                $classes[] = $key;
            }
        }

        return implode(' ', $classes);
    }
}

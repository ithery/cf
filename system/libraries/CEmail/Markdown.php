<?php

use League\CommonMark\MarkdownConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Table\TableExtension;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;

class CEmail_Markdown {
    /**
     * The view factory implementation.
     *
     * @var \CView_Factory
     */
    protected $view;

    /**
     * The current theme being used when generating emails.
     *
     * @var string
     */
    protected $theme = 'default';

    /**
     * The registered component paths.
     *
     * @var array
     */
    protected $componentPaths = [];

    /**
     * Create a new Markdown renderer instance.
     *
     * @param array $options
     *
     * @return void
     */
    public function __construct(array $options = []) {
        $this->theme = carr::get($options, 'theme', 'default');
        $this->loadComponentsFrom(carr::get($options, 'paths', []));
    }

    /**
     * Render the Markdown template into HTML.
     *
     * @param string                          $view
     * @param array                           $data
     * @param null|\CParser_CssToInlineStyles $inliner
     *
     * @return \CBase_HtmlString
     */
    public function render($view, array $data = [], $inliner = null) {
        CView_Factory::instance()->flushFinderCache();

        $contents = CView_Factory::instance()->replaceNamespace(
            'mail',
            $this->htmlComponentPaths()
        )->make($view, $data)->render();

        if (CView_Factory::instance()->exists($customTheme = cstr::start($this->theme, 'mail.'))) {
            $theme = $customTheme;
        } else {
            $theme = cstr::contains($this->theme, '::')
                ? $this->theme
                : 'mail::themes.' . $this->theme;
        }

        return new CBase_HtmlString(($inliner ?: new CParser_CssToInlineStyles())->convert(
            $contents,
            CView_Factory::instance()->make($theme, $data)->render()
        ));
    }

    /**
     * Render the Markdown template into text.
     *
     * @param string $view
     * @param array  $data
     *
     * @return \CBase_HtmlString
     */
    public function renderText($view, array $data = []) {
        $this->view->flushFinderCache();

        $contents = $this->view->replaceNamespace(
            'mail',
            $this->textComponentPaths()
        )->make($view, $data)->render();

        return new CBase_HtmlString(
            html_entity_decode(preg_replace("/[\r\n]{2,}/", "\n\n", $contents), ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Parse the given Markdown text into HTML.
     *
     * @param string $text
     *
     * @return \CBase_HtmlString
     */
    public static function parse($text) {
        $environment = new Environment([
            'allow_unsafe_links' => false,
        ]);

        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());

        $converter = new MarkdownConverter($environment);

        return new CBase_HtmlString($converter->convert($text)->getContent());
    }

    /**
     * Get the HTML component paths.
     *
     * @return array
     */
    public function htmlComponentPaths() {
        return array_map(function ($path) {
            return $path . '/html';
        }, $this->componentPaths());
    }

    /**
     * Get the text component paths.
     *
     * @return array
     */
    public function textComponentPaths() {
        return array_map(function ($path) {
            return $path . '/text';
        }, $this->componentPaths());
    }

    /**
     * Get the component paths.
     *
     * @return array
     */
    protected function componentPaths() {
        return array_unique(array_merge($this->componentPaths, [
            __DIR__ . '/resources/views',
        ]));
    }

    /**
     * Register new mail component paths.
     *
     * @param array $paths
     *
     * @return void
     */
    public function loadComponentsFrom(array $paths = []) {
        $this->componentPaths = $paths;
    }

    /**
     * Set the default theme to be used.
     *
     * @param string $theme
     *
     * @return $this
     */
    public function theme($theme) {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get the theme currently being used by the renderer.
     *
     * @return string
     */
    public function getTheme() {
        return $this->theme;
    }
}

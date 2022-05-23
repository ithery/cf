<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 *
 * @since Dec 5, 2020
 *
 * @license Ittron Global Teknologi
 */
use Cresenity\Documentation\Renderer;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;

class Controller_Docs extends CController {
    public function __construct() {
        parent::__construct();
        $app = CApp::instance();
        $app->setLoginRequired(false);
        $app->setTheme('cresenity-docs');
    }

    public function index() {
        return $this->page();
    }

    public function page($category = null, $page = null) {
        $app = CApp::instance();

        if ($page == null) {
            $page = 'installation';
        }
        if ($category == null) {
            $category = 'starter';
            $page = 'installation';
        }

        $fileData = c::fixPath(CF::appDir()) . 'default' . DS . 'data' . DS . 'docs' . DS . $category . DS . $page . '.md';
        if (!CFile::exists($fileData)) {
            c::abort(404);
        }
        $content = CFile::get($fileData);

        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
        // Define your configuration, if needed
        $config = [
            'table' => [
                'wrap' => [
                    'enabled' => false,
                    'tag' => 'div',
                    'attributes' => [],
                ],
            ],
        ];

        $environment = Environment::createCommonMarkEnvironment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new TableExtension());
        $converter = new CommonMarkConverter([], $environment);
        $html = $converter->convertToHtml($content);

        $renderer = new Renderer($html);
        $h3List = $renderer->getH3List();

        $navData = $app->resolveNav('docs');

        $pageLabel = $page;
        $currentNav = c::collect($navData)->firstWhere('name', '=', $category);
        $categoryLabel = carr::get($currentNav, 'label', $category);

        $currentSubnav = c::collect(carr::get($currentNav, 'subnav'))->firstWhere('name', '=', $category . '.' . $page);

        $pageLabel = carr::get($currentSubnav, 'label', $page);
        $app->add($renderer->getHtml());
        $app->setView('docs');
        $app->setData('rightSubnavs', $h3List);
        $app->setData('categoryLabel', $categoryLabel);
        $app->setData('pageLabel', $pageLabel);
        $app->setNav('docs');
        $app->setNavRenderer(function ($navs) use ($category, $page) {
            return c::view('docs.nav', [
                'navs' => $navs,
                'category' => $category,
                'page' => $page,
            ])->render();
        });

        return $app;
    }

    public function __call($method, $args) {
        return $this->page($method, carr::first($args));
    }
}

<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 *
 * @since Dec 5, 2020
 *
 * @license Ittron Global Teknologi
 */
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;

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

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        $converter = new CommonMarkConverter([], $environment);
        $html = $converter->convertToHtml($content);

        $app->add($html);
        $app->setView('docs');

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

<?php

class CApp_SEO {
    /**
     * Singleton instance of this class.
     *
     * @var CApp_SEO
     */
    private static $instance = null;

    /**
     * CApp_SEO constructor.
     */
    private function __construct() {
        //do nothing
    }

    /**
     * @return CApp_SEO
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @return CApp_SEO_MetaTags
     */
    public function metatags() {
        return CApp_SEO_MetaTags::instance();
    }

    /**
     * @return CApp_SEO_OpenGraph
     */
    public function opengraph() {
        return CApp_SEO_OpenGraph::instance();
    }

    /**
     * @return CApp_SEO_Twitter
     */
    public function twitter() {
        return CApp_SEO_Twitter::instance();
    }

    /**
     * @return CApp_SEO_JsonLd
     */
    public function jsonLd() {
        return CApp_SEO_JsonLd::instance();
    }

    /**
     * @param string $title
     * @param bool   $appendDefault
     *
     * @return static
     */
    public function setTitle($title, $appendDefault = true) {
        $this->metatags()->setTitle($title, $appendDefault);
        $this->opengraph()->setTitle($title);
        $this->twitter()->setTitle($title);
        $this->jsonLd()->setTitle($title);

        return $this;
    }

    /**
     * @param string $description
     *
     * @return static
     */
    public function setDescription($description) {
        $this->metatags()->setDescription($description);
        $this->opengraph()->setDescription($description);
        $this->twitter()->setDescription($description);
        $this->jsonLd()->setDescription($description);

        return $this;
    }

    /**
     * @param string $url
     *
     * @return static
     */
    public function setCanonical($url) {
        $this->metatags()->setCanonical($url);

        return $this;
    }

    /**
     * @param string|array $urls
     *
     * @return static
     */
    public function setImages($urls) {
        $this->opengraph()->setImages(carr::wrap($urls));
        $this->twitter()->setImage($urls);
        $this->jsonLd()->setImages($urls);

        return $this;
    }

    /**
     * @param string|array $urls
     *
     * @return static
     */
    public function addImages($urls) {
        if (is_array($urls)) {
            $this->opengraph()->addImages($urls);
        } else {
            $this->opengraph()->addImage($urls);
        }

        $this->twitter()->setImage($urls);

        $this->jsonLd()->addImage($urls);

        return $this;
    }

    /**
     * @param bool $session
     *
     * @return string
     */
    public function getTitle($session = false) {
        if ($session) {
            return $this->metatags()->getTitleSession();
        }

        return $this->metatags()->getTitle();
    }

    /**
     * @param bool $minify
     *
     * @return string
     */
    public function generate($minify = false) {
        $html = $this->metatags()->generate();
        $html .= PHP_EOL;
        $html .= $this->opengraph()->generate();
        $html .= PHP_EOL;
        $html .= $this->twitter()->generate();
        $html .= PHP_EOL;
        $html .= $this->jsonLd()->generate();

        return ($minify) ? str_replace(PHP_EOL, '', $html) : $html;
    }
}

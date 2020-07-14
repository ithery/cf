<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CApp_SEO {

    protected $instance = null;

    private function __construct() {
        //do nothing
    }

    /**
     * 
     * @return CApp_SEO
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 
     * @return CApp_SEO_MetaTags
     */
    public function metatags() {
        return CApp_SEO_MetaTags::instance();
    }

    /**
     * 
     * @return CApp_SEO_OpenGraph
     */
    public function opengraph() {
        return CApp_SEO_OpenGraph::instance();
    }

    /**
     * 
     * @return CApp_SEO_Twitter
     */
    public function twitter() {
        return CApp_SEO_Twitter::instance();
    }

    /**
     * 
     * @return CApp_SEO_JsonLd
     */
    public function jsonLd() {
        return CApp_SEO_JsonLd::instance();
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title, $appendDefault = true) {
        $this->metatags()->setTitle($title, $appendDefault);
        $this->opengraph()->setTitle($title);
        $this->twitter()->setTitle($title);
        $this->jsonLd()->setTitle($title);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description) {
        $this->metatags()->setDescription($description);
        $this->opengraph()->setDescription($description);
        $this->twitter()->setDescription($description);
        $this->jsonLd()->setDescription($description);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCanonical($url) {
        $this->metatags()->setCanonical($url);

        return $this;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getTitle($session = false) {
        if ($session) {
            return $this->metatags()->getTitleSession();
        }

        return $this->metatags()->getTitle();
    }

    /**
     * {@inheritdoc}
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

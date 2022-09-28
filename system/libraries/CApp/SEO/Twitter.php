<?php

class CApp_SEO_Twitter implements CApp_SEO_TwitterInterface {
    /**
     * @var string
     */
    protected $prefix = 'twitter:';

    /**
     * @var array
     */
    protected $html = [];

    /**
     * @var array
     */
    protected $values = [];

    /**
     * @var array
     */
    protected $images = [];

    /**
     * @var array
     */
    protected $config = [];

    /**
     * Singleton instance of this class.
     *
     * @var CApp_SEO_Twitter
     */
    private static $instance = null;

    private function __construct() {
        $this->config = CF::config('seo.twitter');

        $this->values = carr::get($this->config, 'defaults', []);
    }

    /**
     * @return CApp_SEO_MetaTags
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @param array $defaults
     * @param mixed $minify
     */

    /**
     * @inheritdoc
     */
    public function generate($minify = false) {
        $this->eachValue($this->values);
        $this->eachValue($this->images, 'images');

        return ($minify) ? implode('', $this->html) : implode(PHP_EOL, $this->html);
    }

    /**
     * Make tags.
     *
     * @param array       $values
     * @param null|string $prefix
     *
     * @internal param array $properties
     */
    protected function eachValue(array $values, $prefix = null) {
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $this->eachValue($value, $key);
            } else {
                if (is_numeric($key)) {
                    $key = $prefix . $key;
                } elseif (is_string($prefix)) {
                    $key = $prefix . ':' . $key;
                }

                $this->html[] = $this->makeTag($key, $value);
            }
        }
    }

    /**
     * @param string $key
     * @param $value
     *
     * @return string
     *
     * @internal param string $values
     */
    private function makeTag($key, $value) {
        return '<meta name="' . $this->prefix . strip_tags($key) . '" content="' . strip_tags($value) . '" />';
    }

    /**
     * @inheritdoc
     */
    public function addValue($key, $value) {
        $this->values[$key] = $value;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setTitle($title) {
        return $this->addValue('title', $title);
    }

    /**
     * @inheritdoc
     */
    public function setType($type) {
        return $this->addValue('card', $type);
    }

    /**
     * @inheritdoc
     */
    public function setSite($site) {
        return $this->addValue('site', $site);
    }

    /**
     * @inheritdoc
     */
    public function setDescription($description) {
        return $this->addValue('description', htmlspecialchars($description, ENT_QUOTES, 'UTF-8', false));
    }

    /**
     * @inheritdoc
     */
    public function setUrl($url) {
        return $this->addValue('url', $url);
    }

    /**
     * @inheritdoc
     */
    public function addImage($image) {
        foreach ((array) $image as $url) {
            $this->images[] = $url;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setImages($images) {
        $this->images = [];

        return $this->addImage($images);
    }

    /**
     * @param $image
     *
     * @return CApp_SEO_TwitterInterface
     */
    public function setImage($image) {
        return $this->addValue('image', $image);
    }
}

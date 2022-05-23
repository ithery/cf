<?php
/**
 * @see CApp
 * @see CApp_SEO
 */
class CApp_SEO_JsonLd implements CApp_SEO_JsonLdInterface {
    /**
     * @var array
     */
    protected $values = [];

    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var null|string|bool
     */
    protected $url = false;

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
     * @var CApp_SEO_JsonLd
     */
    private static $instance = null;

    private function __construct() {
        $this->config = CF::config('seo.twitter');

        $this->values = carr::get($this->config, 'defaults', []);

        $defaults = carr::get($this->config, 'defaults', []);

        $this->setTitle(carr::get($defaults, 'title'));
        carr::forget($defaults, 'title');

        $this->setDescription(carr::get($defaults, 'description'));
        carr::forget($defaults, 'description');

        $this->setType(carr::get($defaults, 'type'));
        carr::forget($defaults, 'type');

        $this->setUrl(carr::get($defaults, 'url'));
        carr::forget($defaults, 'url');

        $this->setImages(carr::get($defaults, 'images'));
        carr::forget($defaults, 'images');

        $this->values = $defaults;
    }

    /**
     * @return CApp_SEO_JsonLd
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @inheritdoc
     */
    public function generate($minify = false) {
        $generated = [
            '@context' => 'https://schema.org',
        ];

        if (!empty($this->type)) {
            $generated['@type'] = $this->type;
        }

        if (!empty($this->title)) {
            $generated['name'] = $this->title;
        }

        if (!empty($this->description)) {
            $generated['description'] = $this->description;
        }

        if ($this->url !== false) {
            $generated['url'] = $this->url ? $this->url : curl::urlFull();
        }

        if (!empty($this->images)) {
            $generated['image'] = count($this->images) === 1 ? reset($this->images) : json_encode($this->images);
        }

        $generated = array_merge($generated, $this->values);

        return '<script type="application/ld+json">' . json_encode($generated) . '</script>';
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
    public function addValues(array $values) {
        foreach ($values as $key => $value) {
            $this->addValue($key, $value);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setSite($site) {
        $this->url = $site;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setUrl($url) {
        $this->url = $url;

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
     * @inheritdoc
     */
    public function addImage($image) {
        if (is_array($image)) {
            $this->images = array_merge($this->images, $image);
        } elseif (is_string($image)) {
            $this->images[] = $image;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setImage($image) {
        $this->images = [$image];

        return $this;
    }
}

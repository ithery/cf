<?php

use CApp_OpenGraph_Tag as OpenGraphTag;

/**
 * Library that assists in building Open Graph meta tags.
 * Open Graph protocol official docs: http://ogp.me/.
 */
class CApp_OpenGraph {
    /**
     * The version number.
     */
    const VERSION = '2.0.0';

    /**
     * Define a prefix for tag names.
     */
    const NAME_PREFIX = 'og:';

    /**
     * Array containing the tags.
     *
     * @var OpenGraphTag[]
     */
    protected $tags;

    /**
     * Enables validation. A violation of the standard will throw an exception.
     *
     * @var bool
     */
    protected $validate;

    /**
     * HTML code of the tag template. {{name}} will be replaced by the variable's name and {{value}} with its value.
     *
     * @var string
     */
    protected $template = "<meta property=\"{{name}}\" content=\"{{value}}\" />\n";

    /**
     * Constructor call.
     *
     * @param bool $validate Enable validation?
     */
    public function __construct($validate = false) {
        $this->tags = [];
        $this->validate = $validate;
    }

    /**
     * Creates and returns a new open graph tag object.
     *
     * @param string $name     The name of the tag
     * @param mixed  $value    The value of the tag
     * @param bool   $prefixed Add the "og"-prefix?
     *
     * @return OpenGraphTag
     */
    protected function createTag($name, $value, $prefixed = true) {
        return new OpenGraphTag($name, $value, $prefixed);
    }

    /**
     * Getter for the validation mode.
     *
     * @return bool
     */
    public function valid() {
        return $this->validate;
    }

    /**
     * Setter for the validation mode.
     *
     * @param bool $validate
     *
     * @return CApp_OpenGraph
     */
    public function validate($validate = true) {
        $this->validate = $validate;

        return $this;
    }

    /**
     * Getter for the tags.
     *
     * @return OpenGraphTag[]
     */
    public function tags() {
        return $this->tags;
    }

    /**
     * True if at least one tag with the given name exists.
     * It's possible that a tag has multiple values.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name) {
        foreach ($this->tags as $tag) {
            if ($tag->name == $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove all tags with the given name.
     *
     * @param string $name
     *
     * @return CApp_OpenGraph
     */
    public function forget($name) {
        foreach ($this->tags as $key => $tag) {
            if ($tag->name == $name) {
                unset($this->tags[$key]);
            }
        }

        return $this;
    }

    /**
     * Remove all tags.
     *
     * @return CApp_OpenGraph
     */
    public function clear() {
        $this->tags = [];

        return $this;
    }

    /**
     * Adds a custom tag to the list of tags.
     *
     * @param string $name     The name of the tag
     * @param mixed  $value    The value of the tag
     * @param bool   $prefixed Add the "og"-prefix?
     *
     * @return CApp_OpenGraph
     */
    public function tag($name, $value, $prefixed = true) {
        $value = $this->convertDate($value);
        $this->tags[] = $this->createTag($name, $value, $prefixed);

        return $this;
    }

    /**
     * Adds attribute tags to the list of tags.
     *
     * @param string   $tagName    The name of the base tag
     * @param array    $attributes Array with attributes (pairs of name and value)
     * @param string[] $valid      Array with names of valid attributes
     * @param bool     $prefixed   Add the "og"-prefix?
     *
     * @return CApp_OpenGraph
     */
    public function attributes($tagName, array $attributes = [], array $valid = [], $prefixed = true) {
        foreach ($attributes as $name => $value) {
            if ($this->validate and sizeof($valid) > 0) {
                if (!in_array($name, $valid)) {
                    throw new Exception("Open Graph: Invalid attribute '{$name}' (unknown type)");
                }
            }
            $value = $this->convertDate($value);
            $this->tags[] = $this->createTag($tagName . ':' . $name, $value, $prefixed);
        }

        return $this;
    }

    /**
     * Shortcut for attributes() with $prefixed = false.
     *
     * @param string   $tagName    The name of the base tag
     * @param array    $attributes Array with attributes (pairs of name and value)
     * @param string[] $valid      Array with names of valid attributes
     *
     * @return CApp_OpenGraph
     */
    public function unprefixedAttributes($tagName, array $attributes = [], array $valid = []) {
        return $this->attributes($tagName, $attributes, $valid, false);
    }

    /**
     * Adds a title tag.
     *
     * @param string $title
     *
     * @return CApp_OpenGraph
     */
    public function title($title) {
        $title = trim($title);
        if ($this->validate and !$title) {
            throw new Exception('Open Graph: Invalid title (empty)');
        }
        $this->forget('title');
        $this->tags[] = $this->createTag('title', strip_tags($title));

        return $this;
    }

    /**
     * Adds a type tag.
     *
     * @param string $type
     *
     * @return CApp_OpenGraph
     */
    public function type($type) {
        $types = [
            'music.song',
            'music.album',
            'music.playlist',
            'music.radio_station',
            'video.movie',
            'video.episode',
            'video.tv_show',
            'video.other',
            'article',
            'book',
            'profile',
            'website',
        ];
        if ($this->validate and !in_array($type, $types)) {
            throw new Exception("Open Graph: Invalid type '{$type}' (unknown type)");
        }
        $this->forget('type');
        $this->tags[] = $this->createTag('type', $type);

        return $this;
    }

    /**
     * Adds an image tag.
     * If the URL is relative it's converted to an absolute one.
     *
     * @param string     $imageFile  The URL of the image file
     * @param null|array $attributes Array with additional attributes (pairs of name and value)
     *
     * @return CApp_OpenGraph
     */
    public function image($imageFile, array $attributes = null) {
        if ($this->validate and !$imageFile) {
            throw new Exception('Open Graph: Invalid image URL (empty)');
        }
        // if (strpos($imageFile, '://') === false and function_exists('asset')) {
        //     $imageFile = asset($imageFile);
        // }
        if ($this->validate and !filter_var($imageFile, FILTER_VALIDATE_URL)) {
            throw new Exception("Open Graph: Invalid image URL '{$imageFile}'");
        }
        $this->tags[] = $this->createTag('image', $imageFile);
        if ($attributes) {
            $valid = [
                'secure_url',
                'type',
                'width',
                'height',
            ];
            $this->attributes('image', $attributes, $valid);
        }

        return $this;
    }

    /**
     * Adds a description tag.
     *
     * @param string $description The description text
     * @param int    $maxLength   If the text is longer than this it is shortened
     *
     * @return CApp_OpenGraph
     */
    public function description($description, $maxLength = 250) {
        $description = trim(strip_tags($description));
        $description = preg_replace("/\r|\n/", '', $description);
        $length = mb_strlen($description);
        $description = mb_substr($description, 0, $maxLength);
        if (mb_strlen($description) < $length) {
            $description .= '...';
        }
        $this->forget('description');
        $this->tags[] = $this->createTag('description', $description);

        return $this;
    }

    /**
     * Adds a URL tag.
     *
     * @param string $url
     *
     * @return CApp_OpenGraph
     */
    public function url($url = null) {
        return curl::httpbase();
        if (!$url) {
            $url = null;
            $httpHost = c::env('APP_URL', false); // Has to start with a protocol - for example "http://"!
            if ($httpHost === false) {
                $url = 'http';
                // Quick and dirty
                if (isset($_SERVER['HTTPS'])) {
                    $url .= 's';
                }
                $url .= '://';
                $httpHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost/';
            }
            $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

            $safeRequestURI = htmlentities(strip_tags(urldecode($requestUri)));
            $url .= "{$httpHost}{$safeRequestURI}";
        }
        if ($this->validate and !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception("Open Graph: Invalid URL '{$url}'");
        }
        $this->forget('url');
        $this->tags[] = $this->createTag('url', $url);

        return $this;
    }

    /**
     * Adds a locale tag.
     *
     * @param string $locale
     *
     * @return CApp_OpenGraph
     */
    public function locale($locale) {
        if ($this->validate and !$locale) {
            throw new Exception('Open Graph: Invalid locale (none set)');
        }
        $this->forget('locale');
        $this->tags[] = $this->createTag('locale', $locale);

        return $this;
    }

    /**
     * Adds locale:alternate tags.
     *
     * @param string[] $locales An array of alternative locales
     *
     * @return CApp_OpenGraph
     */
    public function localeAlternate(array $locales = []) {
        if (is_string($locales)) {
            $locales = (array) $locales;
        }

        foreach ($locales as $key => $locale) {
            if ($this->validate and !$locale) {
                throw new Exception("Open Graph: Invalid locale (item key: {$key})");
            }
            $this->tags[] = $this->createTag('locale:alternate', $locale);
        }

        return $this;
    }

    /**
     * Adds a site_name tag.
     *
     * @param string $siteName
     *
     * @return CApp_OpenGraph
     */
    public function siteName($siteName) {
        if ($this->validate and !$siteName) {
            throw new Exception('Open Graph: Invalid site_name (empty)');
        }
        $this->forget('site_name');
        $this->tags[] = $this->createTag('site_name', $siteName);

        return $this;
    }

    /**
     * Adds a determiner tag.
     *
     * @param string $determiner
     *
     * @return CApp_OpenGraph
     */
    public function determiner($determiner = '') {
        $enum = [
            'a',
            'an',
            'the',
            'auto',
            ''
        ];
        if ($this->validate and !in_array($determiner, $enum)) {
            throw new Exception("Open Graph: Invalid determiner '{$determiner}' (unkown value)");
        }
        $this->tags[] = $this->createTag('determiner', $determiner);

        return $this;
    }

    /**
     * Adds an audio tag.
     * If the URL is relative its converted to an absolute one.
     *
     * @param string     $audioFile  The URL of the video file
     * @param null|array $attributes Array with additional attributes (pairs of name and value)
     *
     * @return CApp_OpenGraph
     */
    public function audio($audioFile, array $attributes = null) {
        if ($this->validate and !$audioFile) {
            throw new Exception('Open Graph: Invalid audio URL (empty)');
        }
        // if (strpos($audioFile, '://') === false and function_exists('asset')) {
        //     $audioFile = asset($audioFile);
        // }
        if ($this->validate and !filter_var($audioFile, FILTER_VALIDATE_URL)) {
            throw new Exception("Open Graph: Invalid audio URL '{$audioFile}'");
        }
        $this->tags[] = $this->createTag('audio', $audioFile);
        if ($attributes) {
            $valid = [
                'secure_url',
                'type',
            ];
            $tag = $this->lastTag('type');
            $specialValid = [];
            if ($tag and $tag->name == 'music.song') {
                $specialValid = [
                    'duration',
                    'album',
                    'album:disc',
                    'album:track',
                    'musician',
                ];
            }
            if ($tag and $tag->name == 'music.album') {
                $specialValid = [
                    'song',
                    'song:disc',
                    'song:track',
                    'musician',
                    'release_date',
                ];
            }
            if ($tag and $tag->name == 'music.playlist') {
                $specialValid = [
                    'song',
                    'song:disc',
                    'song:track',
                    'creator',
                ];
            }
            if ($tag and $tag->name == 'music.radio_station') {
                $specialValid = [
                    'creator',
                ];
            }
            $valid = array_merge($valid, $specialValid);
            $this->attributes('audio', $attributes, $valid);
        }

        return $this;
    }

    /**
     * Adds a video tag
     * If the URL is relative its converted to an absolute one.
     *
     * @param string     $videoFile  The URL of the video file
     * @param null|array $attributes Array with additional attributes (pairs of name and value)
     *
     * @return CApp_OpenGraph
     */
    public function video($videoFile, array $attributes = null) {
        if ($this->validate and !$videoFile) {
            throw new Exception('Open Graph: Invalid video URL (empty)');
        }
        // if (strpos($videoFile, '://') === false and function_exists('asset')) {
        //     $videoFile = asset($videoFile);
        // }
        if ($this->validate and !filter_var($videoFile, FILTER_VALIDATE_URL)) {
            throw new Exception("Open Graph: Invalid video URL '{$videoFile}'");
        }
        $this->tags[] = $this->createTag('video', $videoFile);
        if ($attributes) {
            $valid = [
                'secure_url',
                'type',
                'width',
                'height',
            ];
            $tag = $this->lastTag('type');
            if ($tag and cstr::startsWith($tag->value, 'video.')) {
                $specialValid = [
                    'actor',
                    'role',
                    'director',
                    'writer',
                    'duration',
                    'release_date',
                    'tag',
                ];
                if ($tag->value == 'video.episode') {
                    $specialValid[] = 'video:series';
                }
                $valid = array_merge($valid, $specialValid);
            }
            $this->attributes('video', $attributes, $valid);
        }

        return $this;
    }

    /**
     * Adds article attributes.
     *
     * @param array $attributes Array with attributes (pairs of name and value)
     *
     * @return CApp_OpenGraph
     */
    public function article(array $attributes = []) {
        $tag = $this->lastTag('type');
        if (!$tag or $tag->value != 'article') {
            throw new Exception("Open Graph: Type has to be 'article' to add article attributes");
        }
        $valid = [
            'published_time',
            'modified_time',
            'expiration_time',
            'author',
            'section',
            'tag',
        ];
        $this->unprefixedAttributes('article', $attributes, $valid);

        return $this;
    }

    /**
     * Adds book attributes.
     *
     * @param array $attributes Array with attributes (pairs of name and value)
     *
     * @return CApp_OpenGraph
     */
    public function book(array $attributes = []) {
        $tag = $this->lastTag('type');
        if (!$tag or $tag->value != 'book') {
            throw new Exception("Open Graph: Type has to be 'book' to add book attributes");
        }
        $valid = [
            'author',
            'isbn',
            'release_date',
            'tag',
        ];
        $this->unprefixedAttributes('book', $attributes);

        return $this;
    }

    /**
     * Adds profile attributes.
     *
     * @param array $attributes Array with attributes (pairs of name and value)
     *
     * @return CApp_OpenGraph
     */
    public function profile(array $attributes = []) {
        $tag = $this->lastTag('type');
        if (!$tag or $tag->value != 'profile') {
            throw new Exception("Open Graph: Type has to be 'profile' to add profile attributes");
        }
        $valid = [
            'first_name',
            'last_name',
            'username',
            'gender',
        ];
        $this->unprefixedAttributes('profile', $attributes);

        return $this;
    }

    /**
     * Set a template string for the render method.
     *
     * @param string $template The template string
     *
     * @return CApp_OpenGraph
     */
    public function template($template) {
        $this->template = $template;

        return $this;
    }

    /**
     * Returns the Open Graph tags rendered as HTML.
     *
     * @return string
     */
    public function renderTags() {
        $output = '';
        $vars = ['{{name}}', '{{value}}'];
        foreach ($this->tags as $tag) {
            $name = $tag->name;
            if ($tag->prefixed) {
                $name = self::NAME_PREFIX . $name;
            }
            $output .= str_replace($vars, [$name, $tag->value], $this->template);
        }

        return $output;
    }

    /**
     * Same as renderTags().
     *
     * @return string
     */
    public function __toString() {
        return $this->renderTags();
    }

    /**
     * Returns the last tag in the lists of tags with matching name.
     *
     * @param string $name The name of the tag
     *
     * @return null|OpenGraphTag Returns the tag object or null
     */
    public function lastTag($name) {
        $lastTag = null;
        foreach ($this->tags as $tag) {
            if ($tag->name == $name) {
                $lastTag = $tag;
            }
        }

        return $lastTag;
    }

    /**
     * Converts a DateTime object to a string (ISO 8601).
     *
     * @param string|DateTime $date The date (string or DateTime)
     *
     * @return string
     */
    protected function convertDate($date) {
        if (is_a($date, 'DateTime')) {
            return (string) $date->format(DateTime::ISO8601);
        }

        return $date;
    }
}

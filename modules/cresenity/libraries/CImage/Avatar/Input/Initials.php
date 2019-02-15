<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 2:25:18 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CImage_Avatar_Input_Initials {

    public $name;
    public $length;
    public $size;
    public $fontSize;
    public $background;
    public $color;
    public $cacheKey;
    public $rounded;
    public $uppercase;
    public $initials;
    public $request;
    private $hasQueryParameters = false;
    private static $indexes = [
        'name',
        'size',
        'background',
        'color',
        'length',
        'font-size',
        'rounded',
        'uppercase',
    ];

    public function __construct() {
        $this->detectQueryParameters();
        $this->detectUrlBasedParameters();
        $this->request = $_GET;
        $this->name = $this->getRequest('name', 'John Doe');
        $this->size = (int) $this->getRequest('size', 64);
        $this->background = $this->getRequest('background', '#ddd');
        $this->color = $this->getRequest('color', '#222');
        $this->length = (int) $this->getRequest('length', 2);
        $this->fontSize = (double) $this->getRequest('font-size', 0.5);
        $this->rounded = $this->getRounded();
        $this->uppercase = $this->getUppercase();
        $this->initials = $this->getInitials();
        $this->cacheKey = $this->generateCacheKey();
        $this->fixInvalidInput();
    }

    private function getRounded() {
        return filter_var($this->getRequest('rounded', false), FILTER_VALIDATE_BOOLEAN);
    }

    private function getUppercase() {
        return filter_var($this->getRequest('uppercase', true), FILTER_VALIDATE_BOOLEAN);
    }

    private function getInitials() {
        return ( new Initials)->length($this->length)->keepCase(!$this->uppercase)->generate($this->name);
    }

    private function generateCacheKey() {
        return md5("{$this->initials}-{$this->length}-{$this->size}-{$this->fontSize}-{$this->background}-{$this->color}-{$this->rounded}-{$this->uppercase}");
    }

    private function fixInvalidInput() {
        if ($this->length <= 0) {
            $this->length = 1;
        }
        if ($this->fontSize <= 0) {
            $this->fontSize = 0.5;
        }
        if ($this->fontSize > 1) {
            $this->fontSize = 1;
        }
        if ($this->size <= 15) {
            $this->size = 16;
        }
        if ($this->size > 512) {
            $this->size = 512;
        }
    }

    private function detectQueryParameters() {
        foreach ($_GET as $item => $value) {
            if (\in_array($item, self::$indexes, true)) {
                $this->hasQueryParameters = true;
                return true;
            }
        }
        return false;
    }

    private function detectUrlBasedParameters() {
        if ($this->hasQueryParameters) {
            return false;
        }
        $requestUrl = ltrim($_SERVER['REQUEST_URI'], '/');
        $requestUrl = ltrim($requestUrl, 'api');
        $requestUrl = ltrim($requestUrl, '/');
        foreach (explode('/', $requestUrl) as $index => $value) {
            if (!isset(self::$indexes[$index])) {
                continue;
            }
            $_GET[self::$indexes[$index]] = urldecode($value);
        }
        return true;
    }

    private function getRequest($key, $default = null) {
        return carr::get($this->request, $key, $default);
    }

}

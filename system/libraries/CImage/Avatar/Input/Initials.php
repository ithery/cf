<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2019, 2:25:18 AM
 */
class CImage_Avatar_Input_Initials {
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

    private $fontFamily;

    private $name;

    private $hasQueryParameters = false;

    private $isBackgroundAuto = true;

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
        $this->request = c::request()->query();
        $this->name = $this->getRequest('name', 'John Doe');
        $this->size = (int) $this->getRequest('size', 64);

        $this->background = $this->getRequest('background', CColor::fromString($this->name, ['luminosity' => 'dark'])->toHex());
        $this->color = $this->getRequest('color', '#fff');
        $this->length = (int) $this->getRequest('length', 2);
        $this->fontSize = (double) $this->getRequest('font-size', 0.5);
        $this->rounded = filter_var($this->getRequest('rounded', false), FILTER_VALIDATE_BOOLEAN);
        $this->uppercase = filter_var($this->getRequest('uppercase', true), FILTER_VALIDATE_BOOLEAN);
        $this->initials = $this->getInitials();
        $this->cacheKey = $this->generateCacheKey();
        $this->fixInvalidInput();
    }

    public function setName($name) {
        $this->name = $name;
        if ($this->isBackgroundAuto) {
            $this->background = $this->getRequest('background', CColor::fromString($this->name, ['luminosity' => 'dark'])->toHex());
        }

        return $this;
    }

    public function setRounded($rounded) {
        $this->rounded = (bool) $rounded;

        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function getSize() {
        return $this->size;
    }

    public function getBorderSize() {
        return 0;
    }

    public function getBorderColor() {
        return '';
    }

    public function getBorderRadius() {
        return 0;
    }

    public function getBackground() {
        return $this->background;
    }

    public function getRounded() {
        return $this->rounded;
    }

    public function getUppercase() {
        return $this->uppercase;
    }

    public function getFontFamily() {
        return $this->fontFamily;
    }

    public function getFontSize() {
        return $this->fontSize;
    }

    public function getColor() {
        return $this->color;
    }

    public function getInitials() {
        return ( new CString_Initials())->length($this->length)->keepCase(!$this->uppercase)->generate($this->name);
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

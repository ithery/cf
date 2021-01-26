<?php

class CVendor_Google_Recaptcha_RecaptchaV2 extends CVendor_Google_Recaptcha_AbstractRecaptcha {
    /* -----------------------------------------------------------------
      |  Properties
      | -----------------------------------------------------------------
     */

    /**
     * Decides if we've already loaded the script file or not.
     *
     * @param bool
     */
    protected $scriptLoaded = false;

    /* -----------------------------------------------------------------
      |  Getters & Setters
      | -----------------------------------------------------------------
     */

    /**
     * Get script source link.
     *
     * @param string|null $callbackName
     *
     * @return string
     */
    public function getScriptSrc($callbackName = null) {
        $queries = [];

        if ($this->hasLang()) {
            carr::set($queries, 'hl', $this->lang);
        }

        if ($this->hasCallbackName($callbackName)) {
            carr::set($queries, 'onload', $callbackName);
            carr::set($queries, 'render', 'explicit');
        }

        return $this->getClientUrl() . (count($queries) ? '?' . http_build_query($queries) : '');
    }

    /* -----------------------------------------------------------------
      |  Main Methods
      | -----------------------------------------------------------------
     */

    /**
     * Display Captcha.
     *
     * @param string|null $name
     * @param array       $attributes
     *
     * @return CElement_Element_Div
     */
    public function display($name = null, array $attributes = []) {
        $div = new CElement_Element_Div();
        $div->setAttrFromArray(array_merge(
            static::prepareNameAttribute($name),
            $this->prepareAttributes($attributes)
        ));

        return $div->html();
    }

    /**
     * Display image Captcha.
     *
     * @param string|null $name
     * @param array       $attributes
     *
     * @return CElement_Element_Div
     */
    public function image($name = null, array $attributes = []) {
        return $this->display($name, array_merge(
            $attributes,
            ['data-type' => 'image']
        ));
    }

    /**
     * Display audio Captcha.
     *
     * @param string|null $name
     * @param array       $attributes
     *
     * @return \Arcanedev\Html\Elements\Div
     */
    public function audio($name = null, array $attributes = []) {
        return $this->display($name, array_merge(
            $attributes,
            ['data-type' => 'audio']
        ));
    }

    /**
     * Display an invisible Captcha (bind the challenge to a button).
     *
     * @param string $value
     * @param array  $attributes
     * @param mixed  $dataCallback
     *
     * @return CApp_Element_Button
     */
    public function button($value, array $attributes = [], $dataCallback = 'onSubmit') {
        $button = new CElement_Element_Button();
        $button->setAttrFromArray(array_merge(
            ['data-callback' => $dataCallback],
            $this->prepareAttributes($attributes)
        ))->add($value);
        return $button->html();
    }

    /**
     * Get script tag.
     *
     * @param string|null $callbackName
     *
     * @return string
     */
    public function script($callbackName = null) {
        $script = '';

        if (!$this->scriptLoaded) {
            $script = '<script src="' . $this->getScriptSrc($callbackName) . '" async defer></script>';
            $this->scriptLoaded = true;
        }

        return $script;
    }

    /**
     * Get the NoCaptcha API Script.
     *
     * @return string
     */
    public function getApiScript() {
        return "
                window.noCaptcha = {
                    captchas: [],
                    reset: function(name) {
                        var captcha = window.noCaptcha.get(name);
                        if (captcha)
                            window.noCaptcha.resetById(captcha.id);
                    },
                    resetById: function(id) {
                        grecaptcha.reset(id);
                    },
                    get: function(name) {
                        return window.noCaptcha.find(function (captcha) {
                            return captcha.name === name;
                        });
                    },
                    getById: function(id) {
                        return window.noCaptcha.find(function (captcha) {
                            return captcha.id === id;
                        });
                    },
                    find: function(callback) {
                        return window.noCaptcha.captchas.find(callback);
                    },
                    render: function(name, sitekey) {
                        var captcha = {
                            id: grecaptcha.render(name, {'sitekey' : sitekey}),
                            name: name
                        };
                        window.noCaptcha.captchas.push(captcha);
                        return captcha;
                    }
                }
           ";
    }

    /**
     * Get script tag with a callback function.
     *
     * @param array  $captchas
     * @param string $callbackName
     *
     * @return string
     */
    public function scriptWithCallback(array $captchas, $callbackName = 'captchaRenderCallback') {
        $script = $this->script($callbackName);

        if (!empty($script) && !empty($captchas)) {
            $script = implode(PHP_EOL, [
                $this->getApiScript(),
                '<script>',
                "var $callbackName = function() {",
                $this->renderCaptchas($captchas),
                '};',
                '</script>',
                $script,
            ]);
        }

        return $script;
    }

    /**
     * Rendering captchas with callback function.
     *
     * @param array $captchas
     *
     * @return string
     */
    private function renderCaptchas(array $captchas) {
        return implode(PHP_EOL, array_map(function ($captcha) {
            return "if (document.getElementById('{$captcha}')) { window.noCaptcha.render('{$captcha}', '{$this->siteKey}'); }";
        }, $captchas));
    }

    /* -----------------------------------------------------------------
      |  Check Methods
      | -----------------------------------------------------------------
     */

    /**
     * Check if callback is not empty.
     *
     * @param string|null $callbackName
     *
     * @return bool
     */
    private function hasCallbackName($callbackName) {
        return !(is_null($callbackName) || trim($callbackName) === '');
    }

    /* -----------------------------------------------------------------
      |  Other Methods
      | -----------------------------------------------------------------
     */

    /**
     * Parse the response.
     *
     * @param string $json
     *
     * @return CVendor_Google_Recaptcha_Http_AbstractResponse|mixed
     */
    protected function parseResponse($json) {
        return CVendor_Google_Recaptcha_Http_ResponseV2::fromJson($json);
    }

    /**
     * Prepare the attributes.
     *
     * @param array $attributes
     *
     * @return array
     */
    private function prepareAttributes(array $attributes) {
        $attributes = array_merge(
            ['class' => 'g-recaptcha', 'data-sitekey' => $this->siteKey],
            array_filter($attributes)
        );

        self::checkDataAttribute($attributes, 'data-type', ['image', 'audio'], 'image');
        self::checkDataAttribute($attributes, 'data-theme', ['light', 'dark'], 'light');
        self::checkDataAttribute($attributes, 'data-size', ['normal', 'compact', 'invisible'], 'normal');
        self::checkDataAttribute($attributes, 'data-badge', ['bottomright', 'bottomleft', 'inline'], 'bottomright');

        return $attributes;
    }

    /**
     * Check the `data-*` attribute.
     *
     * @param array  $attributes
     * @param string $name
     * @param array  $supported
     * @param string $default
     */
    private static function checkDataAttribute(array &$attributes, $name, array $supported, $default) {
        $attribute = isset($attributes[$name]) ? $attributes[$name] : null;

        if (!is_null($attribute)) {
            $attribute = (is_string($attribute) && in_array($attribute, $supported)) ? strtolower(trim($attribute)) : $default;

            $attributes[$name] = $attribute;
        }
    }

    /**
     * Prepare the name and id attributes.
     *
     * @param string|null $name
     *
     * @return array
     *
     * @throws \Arcanedev\NoCaptcha\Exceptions\InvalidArgumentException
     */
    protected static function prepareNameAttribute($name) {
        if (is_null($name)) {
            return [];
        }

        if ($name === CVendor_Google_Recaptcha_AbstractRecaptcha::CAPTCHA_NAME) {
            throw new CVendor_Google_Recaptcha_Exception_InvalidArgumentException(
                'The captcha name must be different from "' . CVendor_Google_Recaptcha_AbstractRecaptcha::CAPTCHA_NAME . '".'
            );
        }

        return array_combine(['id', 'name'], [$name, $name]);
    }
}

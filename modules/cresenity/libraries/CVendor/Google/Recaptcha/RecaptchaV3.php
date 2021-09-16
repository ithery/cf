<?php

class CVendor_Google_Recaptcha_RecaptchaV3 extends CVendor_Google_Recaptcha_AbstractRecaptcha {
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
      |  Main Methods
      | -----------------------------------------------------------------
     */

    /**
     * @param string $name
     *
     * @return CElement_FormInput_Hidden
     */
    public function input($name = 'g-recaptcha-response') {
        $hidden = new CElement_FormInput_Hidden($name);
        return $hidden->html();
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
            $script = implode(PHP_EOL, [
                '<script src="' . $this->getScriptSrc($callbackName) . '"></script>',
            ]);
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
                    render: function(action, callback) {
                        grecaptcha.execute('" . $this->getSiteKey() . "', {action})
                              .then(callback);
                    }
                }
            ";
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
     * @return CVendor_Google_Recaptcha_Http_ResponseV3|mixed
     */
    protected function parseResponse($json) {
        return CVendor_Google_Recaptcha_Http_ResponseV3::fromJson($json);
    }

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
        carr::set($queries, 'render', $this->getSiteKey());

        if ($this->hasCallbackName($callbackName)) {
            carr::set($queries, 'onload', $callbackName);
        }

        return $this->getClientUrl() . (count($queries) ? '?' . http_build_query($queries) : '');
    }
}

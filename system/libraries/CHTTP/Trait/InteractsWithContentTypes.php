<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 5:37:09 AM
 */
trait CHTTP_Trait_InteractsWithContentTypes {
    /**
     * Determine if the given content types match.
     *
     * @param string $actual
     * @param string $type
     *
     * @return bool
     */
    public static function matchesType($actual, $type) {
        if ($actual === $type) {
            return true;
        }

        $split = explode('/', $actual);

        return isset($split[1]) && preg_match('#' . preg_quote($split[0], '#') . '/.+\+' . preg_quote($split[1], '#') . '#', $type);
    }

    /**
     * Determine if the request is sending JSON.
     *
     * @return bool
     */
    public function isJson() {
        return cstr::contains($this->header('CONTENT_TYPE'), ['/json', '+json']);
    }

    /**
     * Determine if the current request probably expects a JSON response.
     *
     * @return bool
     */
    public function expectsJson() {
        return ($this->ajax() && !$this->pjax()) || $this->wantsJson();
    }

    /**
     * Determine if the current request is asking for JSON in return.
     *
     * @return bool
     */
    public function wantsJson() {
        $acceptable = $this->getAcceptableContentTypes();

        return isset($acceptable[0]) && cstr::contains($acceptable[0], ['/json', '+json']);
    }

    /**
     * Determines whether the current requests accepts a given content type.
     *
     * @param string|array $contentTypes
     *
     * @return bool
     */
    public function accepts($contentTypes) {
        $accepts = $this->getAcceptableContentTypes();

        if (count($accepts) === 0) {
            return true;
        }

        $types = (array) $contentTypes;

        foreach ($accepts as $accept) {
            if ($accept === '*/*' || $accept === '*') {
                return true;
            }

            foreach ($types as $type) {
                if ($this->matchesType($accept, $type) || $accept === strtok($type, '/') . '/*') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Return the most suitable content type from the given array based on content negotiation.
     *
     * @param string|array $contentTypes
     *
     * @return null|string
     */
    public function prefers($contentTypes) {
        $accepts = $this->getAcceptableContentTypes();

        $contentTypes = (array) $contentTypes;

        foreach ($accepts as $accept) {
            if (in_array($accept, ['*/*', '*'])) {
                return $contentTypes[0];
            }

            foreach ($contentTypes as $contentType) {
                $type = $contentType;

                if (!is_null($mimeType = $this->getMimeType($contentType))) {
                    $type = $mimeType;
                }

                if ($this->matchesType($type, $accept) || $accept === strtok($type, '/') . '/*') {
                    return $contentType;
                }
            }
        }

        return null;
    }

    /**
     * Determines whether a request accepts JSON.
     *
     * @return bool
     */
    public function acceptsJson() {
        return $this->accepts('application/json');
    }

    /**
     * Determines whether a request accepts HTML.
     *
     * @return bool
     */
    public function acceptsHtml() {
        return $this->accepts('text/html');
    }

    /**
     * Get the data format expected in the response.
     *
     * @param string $default
     *
     * @return string
     */
    public function format($default = 'html') {
        foreach ($this->getAcceptableContentTypes() as $type) {
            if ($format = $this->getFormat($type)) {
                return $format;
            }
        }

        return $default;
    }
}

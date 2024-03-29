<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Abstract interface for a SQL reserved keyword dictionary.
 */
abstract class CDatabase_Platform_Keywords {
    /**
     * @var null|array
     */
    private $keywords = null;

    /**
     * Checks if the given word is a keyword of this dialect/vendor platform.
     *
     * @param string $word
     *
     * @return bool
     */
    public function isKeyword($word) {
        if ($this->keywords === null) {
            $this->initializeKeywords();
        }

        return isset($this->keywords[strtoupper($word)]);
    }

    /**
     * @return void
     */
    protected function initializeKeywords() {
        $this->keywords = array_flip(array_map('strtoupper', $this->getKeywords()));
    }

    /**
     * Returns the list of keywords.
     *
     * @return array
     */
    abstract protected function getKeywords();

    /**
     * Returns the name of this keyword list.
     *
     * @return string
     */
    abstract public function getName();
}

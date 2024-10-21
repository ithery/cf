<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 9:32:32 PM
 */
use Snowplow\RefererParser\Parser;

class CTracker_Parser_RefererParser {
    /**
     * Referer parser instance.
     *
     * @var Parser
     */
    private $parser;

    /**
     * Referer parser instance.
     *
     * @var \Snowplow\RefererParser\Referer
     */
    private $referer;

    /**
     * Create a referer parser instance.
     *
     * @return mixed
     */
    public function __construct() {
        $this->parser = new Parser();
    }

    /**
     * Parse a referer.
     *
     * @param mixed $refererUrl
     * @param mixed $pageUrl
     *
     * @return RefererParser
     */
    public function parse($refererUrl, $pageUrl) {
        $this->setReferer($this->parser->parse($refererUrl, $pageUrl));

        return $this;
    }

    /**
     * Get the search medium.
     *
     * @return null|string
     */
    public function getMedium() {
        if ($this->isKnown()) {
            return $this->referer->getMedium();
        }
    }

    /**
     * Get the search source.
     *
     * @return null|string
     */
    public function getSource() {
        if ($this->isKnown()) {
            return $this->referer->getSource();
        }
    }

    /**
     * Get the search term.
     *
     * @return null|string
     */
    public function getSearchTerm() {
        if ($this->isKnown()) {
            return $this->referer->getSearchTerm();
        }
    }

    /**
     * Check if the referer is knwon.
     *
     * @return bool
     */
    public function isKnown() {
        return $this->referer->isKnown();
    }

    /**
     * Set the referer.
     *
     * @param \Snowplow\RefererParser\Referer $referer
     *
     * @return RefererParser
     */
    public function setReferer($referer) {
        $this->referer = $referer;

        return $this;
    }
}

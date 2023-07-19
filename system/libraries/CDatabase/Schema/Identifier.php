<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * An abstraction class for an asset identifier.
 *
 * Wraps identifier names like column names in indexes / foreign keys
 * in an abstract class for proper quotation capabilities.
 */
class CDatabase_Schema_Identifier extends CDatabase_AbstractAsset {
    /**
     * Constructor.
     *
     * @param string $identifier identifier name to wrap
     * @param bool   $quote      whether to force quoting the given identifier
     */
    public function __construct($identifier, $quote = false) {
        $this->setName($identifier);

        if ($quote && !$this->quoted) {
            $this->setName('"' . $this->getName() . '"');
        }
    }
}

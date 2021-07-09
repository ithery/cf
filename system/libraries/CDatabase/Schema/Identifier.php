<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 11:57:45 AM
 */

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
        $this->_setName($identifier);

        if ($quote && !$this->_quoted) {
            $this->_setName('"' . $this->getName() . '"');
        }
    }
}

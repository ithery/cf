<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 25, 2019, 9:15:13 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * This interface describes the minimum interface needed for a comment parser object used by the
 * CXMLRPC_Documentor
 * @package CXMLRPC
 */
interface CXMLRPC_Documentor_ParserInterface {

    /**
     * This method parses a given docComment block and returns an array with information.
     * @param string $commentBlock The docComment block.
     * @return array The parsed information.
     */
    public function parse($commentBlock);
}

<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 1:41:28 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Visitor that can visit schema namespaces.
 *
 */
interface CDatabase_Schema_Visitor_NamespaceInterface {

    /**
     * Accepts a schema namespace name.
     *
     * @param string $namespaceName The schema namespace name to accept.
     */
    public function acceptNamespace($namespaceName);
}

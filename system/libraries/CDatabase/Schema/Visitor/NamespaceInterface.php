<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 1:41:28 PM
 */

/**
 * Visitor that can visit schema namespaces.
 */
interface CDatabase_Schema_Visitor_NamespaceInterface {
    /**
     * Accepts a schema namespace name.
     *
     * @param string $namespaceName the schema namespace name to accept
     */
    public function acceptNamespace($namespaceName);
}

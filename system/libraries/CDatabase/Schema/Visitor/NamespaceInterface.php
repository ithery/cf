<?php

defined('SYSPATH') or die('No direct access allowed.');

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

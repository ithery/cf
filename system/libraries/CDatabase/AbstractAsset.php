<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 10:32:11 AM
 */

/**
 * The abstract asset allows to reset the name of all assets without publishing this to the public userland.
 *
 * This encapsulation hack is necessary to keep a consistent state of the database schema. Say we have a list of tables
 * array($tableName => Table($tableName)); if you want to rename the table, you have to make sure
 */
abstract class CDatabase_AbstractAsset {
    /**
     * @var string
     */
    protected $name;

    /**
     * Namespace of the asset. If none isset the default namespace is assumed.
     *
     * @var string|null
     */
    protected $namespace = null;

    /**
     * @var bool
     */
    protected $quoted = false;

    /**
     * Sets the name of this asset.
     *
     * @param string $name
     *
     * @return void
     */
    protected function setName($name) {
        if ($this->isIdentifierQuoted($name)) {
            $this->quoted = true;
            $name = $this->trimQuotes($name);
        }

        if (strpos($name, '.') !== false) {
            $parts = explode('.', $name);
            $this->namespace = $parts[0];
            $name = $parts[1];
        }

        $this->name = $name;
    }

    /**
     * Is this asset in the default namespace?
     *
     * @param string $defaultNamespaceName
     *
     * @return bool
     */
    public function isInDefaultNamespace($defaultNamespaceName) {
        return $this->namespace == $defaultNamespaceName || $this->namespace === null;
    }

    /**
     * Gets the namespace name of this asset.
     *
     * If NULL is returned this means the default namespace is used.
     *
     * @return string|null
     */
    public function getNamespaceName() {
        return $this->namespace;
    }

    /**
     * The shortest name is stripped of the default namespace. All other
     * namespaced elements are returned as full-qualified names.
     *
     * @param string $defaultNamespaceName
     *
     * @return string
     */
    public function getShortestName($defaultNamespaceName) {
        $shortestName = $this->getName();
        if ($this->namespace == $defaultNamespaceName) {
            $shortestName = $this->name;
        }

        return strtolower($shortestName);
    }

    /**
     * The normalized name is full-qualified and lowerspaced. Lowerspacing is
     * actually wrong, but we have to do it to keep our sanity. If you are
     * using database objects that only differentiate in the casing (FOO vs
     * Foo) then you will NOT be able to use Doctrine Schema abstraction.
     *
     * Every non-namespaced element is prefixed with the default namespace
     * name which is passed as argument to this method.
     *
     * @param string $defaultNamespaceName
     *
     * @return string
     */
    public function getFullQualifiedName($defaultNamespaceName) {
        $name = $this->getName();
        if (!$this->namespace) {
            $name = $defaultNamespaceName . '.' . $name;
        }

        return strtolower($name);
    }

    /**
     * Checks if this asset's name is quoted.
     *
     * @return bool
     */
    public function isQuoted() {
        return $this->quoted;
    }

    /**
     * Checks if this identifier is quoted.
     *
     * @param string $identifier
     *
     * @return bool
     */
    protected function isIdentifierQuoted($identifier) {
        return (isset($identifier[0]) && ($identifier[0] == '`' || $identifier[0] == '"' || $identifier[0] == '['));
    }

    /**
     * Trim quotes from the identifier.
     *
     * @param string $identifier
     *
     * @return string
     */
    protected function trimQuotes($identifier) {
        return str_replace(['`', '"', '[', ']'], '', $identifier);
    }

    /**
     * Returns the name of this schema asset.
     *
     * @return string
     */
    public function getName() {
        if ($this->namespace) {
            return $this->namespace . '.' . $this->name;
        }

        return $this->name;
    }

    /**
     * Gets the quoted representation of this asset but only if it was defined with one. Otherwise
     * return the plain unquoted value as inserted.
     *
     * @param CDatabase_Platform $platform
     *
     * @return string
     */
    public function getQuotedName(CDatabase_Platform $platform) {
        $keywords = $platform->getReservedKeywordsList();
        $parts = explode('.', $this->getName());
        foreach ($parts as $k => $v) {
            $parts[$k] = ($this->quoted || $keywords->isKeyword($v)) ? $platform->quoteIdentifier($v) : $v;
        }

        return implode('.', $parts);
    }

    /**
     * Generates an identifier from a list of column names obeying a certain string length.
     *
     * This is especially important for Oracle, since it does not allow identifiers larger than 30 chars,
     * however building idents automatically for foreign keys, composite keys or such can easily create
     * very long names.
     *
     * @param array  $columnNames
     * @param string $prefix
     * @param int    $maxSize
     *
     * @return string
     */
    protected function generateIdentifierName($columnNames, $prefix = '', $maxSize = 30) {
        $hash = implode('', array_map(function ($column) {
            return dechex(crc32($column));
        }, $columnNames));

        return strtoupper(substr($prefix . '_' . $hash, 0, $maxSize));
    }
}

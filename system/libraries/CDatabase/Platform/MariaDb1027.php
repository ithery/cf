<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 8:46:25 AM
 */
final class CDatabase_Platform_MariaDb1027 extends CDatabase_Platform_Mysql {
    /**
     * {@inheritdoc}
     */
    public function hasNativeJsonType() {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @link https://mariadb.com/kb/en/library/json-data-type/
     */
    public function getJsonTypeDeclarationSQL(array $field) {
        return 'LONGTEXT';
    }

    /**
     * {@inheritdoc}
     */
    protected function getReservedKeywordsClass() {
        return CDatabase_Platform_Keywords_MariaDb102::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeDoctrineTypeMappings() {
        parent::initializeDoctrineTypeMappings();

        $this->doctrineTypeMapping['json'] = CDatabase_Type::JSON;
    }
}

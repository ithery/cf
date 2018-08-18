<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 8:46:25 AM
 * @license Ittron Global Teknologi <ittron.co.id>
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
        return Keywords\MariaDb102Keywords::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeDoctrineTypeMappings() {
        parent::initializeDoctrineTypeMappings();

        $this->doctrineTypeMapping['json'] = Type::JSON;
    }

}

<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 8:46:25 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CDatabase_Driver_AbstractMysql extends CDatabase_Driver implements CDatabase_Driver_VersionAwarePlatformInterface, CDatabase_Driver_ServerInfoAwareInterface {

    /**
     * {@inheritdoc}
     */
    public function getDatabase(CDatabase $db) {
        $params = $db->config();
        
        $dbname = carr::path($params, 'connection.database');
        if ($dbname == null) {
            $dbname = $conn->query('SELECT DATABASE()')->fetchColumn();
        }
        return $dbname;
    }
    
     /**
     * {@inheritdoc}
     * @return CDatabase_Platform_Mysql
     */
    public function getDatabasePlatform()
    {
        return new CDatabase_Platform_Mysql();
    }

}

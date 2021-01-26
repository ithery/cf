<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 15, 2018, 1:29:40 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CServer_Database_Mysql {

    public static function getVersion() {
        $output = "";
        if (csys::func_enabled("shell_exec")) {
            $output = shell_exec('mysql -V');
            preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version);
            if (isset($version) && count($version) > 0) {
                $output = $version[0];
            }
        }
        /*
          if($output=="") {
          try {
          $output= mysql_get_server_info();
          } catch(Exception $ex) {
          }
          }
         */
        return $output;
    }

}

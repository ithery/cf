<?php

/**
 * Description of Nginx
 *
 * @author Hery
 */

/**
 * @mixin CDevSuite_Nginx_DriverAbstract
 */
class CDevSuite_Nginx implements CDevSuite_Contract_HaveOSDriverInterface {

    use CDevSuite_Trait_OSDriverTrait;

    
    
    public static function createDriver() {
        switch (CServer::getOS()) {
            case CServer::OS_WINNT:
                return new CDevSuite_Nginx_Driver_WindowsDriver();
            default:
                throw new Exception('No available nginx driver for OS:' . CServer::getOS());
        }
        return null;
    }

}

<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Dec 30, 2017, 3:11:24 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CApp_Trait_Base {

    private static $org = null;

    /**
     * 
     * @return int
     */
    public static function orgId() {
        $org_id = CF::org_id();
        $app = CApp::instance();
        if ($app->user() != null) {
            if (strlen($app->user()->org_id) > 0) {
                $org_id = $app->user()->org_id;
            }
        }
        return $org_id;
    }

    public static function org_id() {
        return self::orgId();
    }

    /**
     * 
     * @param int $org_id optional, default using return values of SM::org_id()
     * @return string Code of org
     */
    public static function orgCode($orgId = null) {
        $org = self::org($orgId);
        return cobj::get($org, 'code');
    }

    public static function org_code($orgId = null) {
        return self::orgCode($orgId);
    }

    /**
     * 
     * @param int $org_id optional, default using return values of SM::org_id()
     * @return string Name of org
     */
    public static function orgName($orgId = null) {
        $org = self::org($orgId);
        return cobj::get($org, 'name');
    }

    public static function org_name($orgId = null) {
        return self::orgName($orgId);
    }

    /**
     * 
     * @param int $org_id optional, default using return values of SM::org_id()
     * @return stdClass of org
     */
    public static function org($org_id = null) {
        $db = CDatabase::instance();

        if ($org_id == null) {
            $org_id = self::org_id();
        }
        if (self::$org == null) {
            self::$org = array();
        }
        if (!isset(self::$org[$org_id])) {
            self::$org[$org_id] = cdbutils::get_row('select * from org where org_id = ' . $db->escape($org_id));
        }
        return self::$org[$org_id];
    }

    /**
     * return current CSession object
     * 
     * @return CSession
     */
    public static function session() {
        return CSession::instance();
    }

    /**
     * 
     * @return string value of current theme
     */
    public static function theme() {
        $theme = CManager::theme()->getCurrentTheme();

        return $theme;
    }

    /**
     * User dari session CApp
     * 
     * @return stdClass 
     */
    public static function user() {
        $session = self::session();
        $user = $session->get('user');
        return $user;
    }

    /**
     * User ID dari session CApp
     * 
     * @return int
     */
    public static function userId() {
        return cobj::get(self::user(), 'user_id');
    }

    public static function user_id() {
        return self::userId();
    }

    public static function username() {
        $app = CApp::instance();
        $user = $app->user();
        if ($user != null) {
            return $user->username;
        }
        return 'system';
    }

    /**
     * Current Date Y-m-d H:i:s format
     * 
     * @return string
     */
    public static function now() {
        return date('Y-m-d H:i:s');
    }

    /**
     * 
     * @return array
     */
    public static function defaultInsert() {
        $data = array();
        $data['created'] = self::now();
        $data['createdby'] = self::username();
        $data['updated'] = self::now();
        $data['updatedby'] = self::username();
        $data['status'] = 1;

        return $data;
    }

    /**
     * 
     * @return array
     */
    public static function defaultUpdate() {
        $data = array();
        $data['updated'] = self::now();
        $data['updatedby'] = self::username();
        return $data;
    }

    /**
     * 
     * @return array
     */
    public static function defaultDelete() {
        $data = array();
        $data['updated'] = self::now();
        $data['updatedby'] = self::username();
        return $data;
    }

    /**
     * return protocol http or https depend on variables $_SERVER['HTTPS']
     * 
     * @return string
     */
    public static function protocol() {
        return isset($_SERVER['HTTPS']) ? 'https' : 'http';
    }

    public static function isMobile() {
        $useragent = CF::user_agent();
        return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));
    }

    public static function remoteAddress() {
        return crequest::remote_address();
    }

    public static function noImageUrl() {
        return curl::base() . 'cresenity/noimage/100/100';
    }

    public static function havePermission($action) {
        return CApp_Navigation_Helper::havePermission($action);
    }

}

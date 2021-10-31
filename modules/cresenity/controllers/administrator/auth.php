<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2018, 12:46:22 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CApp_Administrator as Administrator;

class Controller_Administrator_Auth extends CApp_Administrator_Controller {

    public function login() {
        $post = $_POST;
        if ($post != null) {
            $session = CSession::instance();
            $password = isset($post["password"]) ? $post["password"] : "";

            $errCode = 0;
            $errMessage = "";

            if ($errCode == 0) {
                if (strlen($password) == 0) {
                    $errCode++;
                    $errMessage = "Password required";
                }
            }
            if ($errCode == 0) {
                if (!Administrator::login($password)) {
                    $errCode++;
                    $errMessage = "Invalid Password";
                }
            }
            $json = array();
            if ($errCode == 0) {
                $json["result"] = "OK";
                $json["message"] = "Login success";
            } else {
                $json["result"] = "ERROR";
                $json["message"] = $errMessage;
            }
            echo json_encode($json);
        } else {
            curl::redirect("");
        }
    }

    public function logout() {
        Administrator::logout();

        curl::redirect("administrator/home");
    }

}

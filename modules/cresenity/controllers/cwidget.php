<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CWidget_Controller extends CController {

    function online_user_json() {
        $app = CApp::instance();
        $app = CApp::instance();
        $user = $app->user();
        $user_id = $user->user_id;
        $org = $app->org();
        $org_id = null;
        if ($org != null) {
            $org_id = $org->org_id;
        }
        $db = CDatabase::instance();

        $q = "select *,case when (TIME_TO_SEC(TIMEDIFF(now(), last_request))<60*5) then 'online' else 'offline' end as online_status from users where status>0 ";
        if (strlen($org_id) > 0) {
            $q.= "and org_id=" . $db->escape($org_id);
        }
        if (strlen($user_id) > 0) {
            $q.=" and user_id<>" . $db->escape($user_id);
        }
        $r = $db->query($q);
        $result = array();
        foreach ($r as $row) {
            $ruser = array();
            $ruser["username"] = $row->username;
            $user_photo = $row->user_photo;
            $imgsrc = curl::base() . 'cresenity/noimage/40/40';
            if (strlen($user_photo) > 0) {
                $imgsrc = cimage::get_image_src("user_photo", $row->user_id, "thumbnail", $user_photo);
            }
            $ruser["user_photo"] = $imgsrc;
            $ruser["user_id"] = $row->user_id;
            $ruser["user_online"] = $row->online_status;

            $result[] = $ruser;
        }
        header('Content-type: application/json');
        echo json_encode($result);
    }

    function add_user_msg() {
        $user_to_id = "";
        $msg = "";
        $post = $this->input->post();
        if (isset($post["msg"])) {
            $msg = $post["msg"];
        }
        if (isset($post["user_to_id"]) && strlen($post["user_to_id"]) > 0) {
            $user_to_id = $post["user_to_id"];
        }

        $db = CDatabase::instance();
        $app = CApp::instance();
        $user = $app->user();
        $user_id = $user->user_id;
        $org = $app->org();
        $org_id = null;
        if ($org != null) {
            $org_id = $org->org_id;
        }
        $data = array(
            "user_from_id" => $user_id,
            "org_id" => $org_id,
            "msg" => $msg,
            "created" => date("Y-m-d H:i:s"),
            "createdby" => $user->username,
        );
        if (strlen($user_to_id) > 0) {
            $data = array_merge($data, array(
                "user_to_id" => $user_to_id,
            ));
        }
        $db->begin();
        $db->insert("user_msg", $data);
        $db->commit();
        echo "OK";
    }

    function check_msg() {
        $db = CDatabase::instance();
        $app = CApp::instance();
        $user = $app->user();
        $user_id = $user->user_id;
        $org = $app->org();
        $org_id = null;
        if ($org != null) {
            $org_id = $org->org_id;
        }
        $last_id = "";
        $user_to_id = "";
        if (isset($_GET["user_to_id"])) {
            $user_to_id = $_GET["user_to_id"];
        }
        if (isset($_GET["last_id"])) {
            $last_id = $_GET["last_id"];
        }

        $found = false;
        while (!$found) {
            $q = "select um.*,uf.username as username_from,uf.user_photo as user_photo_from from user_msg um inner join users uf on um.user_from_id=uf.user_id where 1=1";
            if (strlen($org_id) > 0) {
                $q.= "and um.org_id=" . $db->escape($org_id);
            }
            if ($last_id != "") {
                $q.=" and um.user_msg_id>" . $last_id;
            }
            if ($user_to_id != "") {
                $q.=" and (not(um.user_to_id is null) and um.user_to_id <> 0)";
                $q.=" and (";
                $q.=" (um.user_to_id=" . $db->escape($user_id) . " and um.user_from_id=" . $db->escape($user_to_id) . ")";
                $q.=" or (um.user_from_id=" . $db->escape($user_id) . " and um.user_to_id=" . $db->escape($user_to_id) . ")";
                $q.=" )";
            } else {
                $q.=" and (um.user_to_id is null or um.user_to_id = 0)";
            }
            $q .= " order by user_msg_id desc limit 10";
            $r = $db->query($q);
            clogger::log('chat.log', 'query', $q);
            clogger::log('chat.log', 'result_count', $r->count());

            if ($r->count() > 0) {
                $found = true;
                $result = array();
                foreach ($r as $row) {
                    $rmsg = array();
                    $rmsg["user_msg_id"] = $row->user_msg_id;
                    $rmsg["user_id"] = $row->user_from_id;
                    $rmsg["username"] = $row->username_from;
                    $user_photo = $row->user_photo_from;
                    $imgsrc = curl::base() . 'cresenity/noimage/40/40';
                    if (strlen($user_photo) > 0) {
                        $imgsrc = cimage::get_image_src("user_photo", $row->user_from_id, "thumbnail", $user_photo);
                    }
                    $rmsg["user_photo"] = $imgsrc;
                    $rmsg["msg"] = $row->msg;
                    $rmsg["created"] = $row->created;
                    $result[] = $rmsg;
                }
                header('Content-type: application/json');
                echo json_encode($result);
                unset($r);
            }
            if (!$found) {

                sleep(1);
            }
        }
    }

    function prev_msg() {
        $db = CDatabase::instance();
        $app = CApp::instance();
        $user = $app->user();
        $user_id = $user->user_id;
        $org = $app->org();
        $org_id = null;
        if($org != null){
        $org_id = $org->org_id;
        }
        $first_id = "";
        $user_to_id = "";
        if (isset($_GET["user_to_id"])) {
            $user_to_id = $_GET["user_to_id"];
        }
        if (isset($_GET["first_id"])) {
            $first_id = $_GET["first_id"];
        }


        $q = "select um.*,uf.username as username_from,uf.user_photo as user_photo_from from user_msg um inner join users uf on um.user_from_id=uf.user_id where 1=1";
        if (strlen($org_id) > 0) {
            $q.= "and um.org_id=" . $db->escape($org_id);
        }
        if ($first_id != "")
            $q.=" and um.user_msg_id<" . $first_id;
        if ($user_to_id != "") {
            $q.=" and (not(um.user_to_id is null) and um.user_to_id <> 0)";
            $q.=" and (";
            $q.=" (um.user_to_id=" . $db->escape($user_id) . " and um.user_from_id=" . $db->escape($user_to_id) . ")";
            $q.=" or (um.user_from_id=" . $db->escape($user_id) . " and um.user_to_id=" . $db->escape($user_to_id) . ")";
            $q.=" )";
        } else {
            $q.=" and (um.user_to_id is null or um.user_to_id = 0)";
        }
        $q .= " order by user_msg_id desc limit 10";
        $r = $db->query($q);

        $result = array();
        if ($r->count() > 0) {

            foreach ($r as $row) {
                $rmsg = array();
                $rmsg["user_msg_id"] = $row->user_msg_id;
                $rmsg["user_id"] = $row->user_from_id;
                $rmsg["username"] = $row->username_from;
                $user_photo = $row->user_photo_from;
                $imgsrc = curl::base() . 'cresenity/noimage/40/40';
                if (strlen($user_photo) > 0) {
                    $imgsrc = cimage::get_image_src("user_photo", $row->user_from_id, "thumbnail", $user_photo);
                }
                $rmsg["user_photo"] = $imgsrc;
                $rmsg["msg"] = $row->msg;
                $rmsg["created"] = $row->created;
                $result[] = $rmsg;
            }
        }
        header('Content-type: application/json');
        echo json_encode($result);
        unset($r);
    }

}

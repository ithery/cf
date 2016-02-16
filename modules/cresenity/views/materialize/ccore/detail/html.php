<?php
$app = CApp::instance();
$org = $app->org();
$user = $app->user();
$org_id = $org->org_id;
$db = CDatabase::instance();
$role = $app->role();
$users = cuser::get($user_id);

$tab = "user_info";
if (isset($_GET["tab"]))
    $tab = $_GET["tab"];

$tabs_data = array(
    "user_info" => array(
        "label" => clang::__("Info"),
        "icon" => "info",
        "url" => curl::base() . "users/detail/tab/user_info?user_id=" . $user_id,
        "class" => "",
    ),
    "user_activity" => array(
        "label" => clang::__("Activity"),
        "icon" => "list",
        "url" => curl::base() . "users/detail/tab/user_activity?user_id=" . $user_id,
        "class" => "",
    ),
);
?>
<style>
    .form-horizontal .controls > label {
        display: inline;
    }
</style>
<form class="form-horizontal">
    <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>"> 

    <div class="row-fluid">
        <div class="span12">
            <div class="row-fluid">
                <div class="span6">
                    <div class="control-group">	
                        <label class="control-label"><?php echo clang::__('Role') ?></label>
                        <div class="controls"><span class="label"><?php echo $users->name; ?></span></div>
                    </div>
                </div>
                <div class="span6">
                    <div class="control-group">
                        <label class="control-label"><?php echo clang::__('Last Login') ?></label>
                        <div class="controls"><span class="label"><?php echo $users->last_login; ?></span></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row-fluid">
        <div class="span12">
            <div class="row-fluid">
                <div class="span2">
                    <div class="side-nav-container affix-top">
                        <ul id="users-tab-nav" class="nav nav-tabs nav-stacked">
                            <?php
                            foreach ($tabs_data as $k => $tab_data) :
                                ?>
                                <li class="<?php echo ($tab == $k ? "active" : ""); ?>"><a href="javascript:;" data-class="<?php echo $tab_data["class"]; ?>" data-icon="icon-<?php echo $tab_data["icon"]; ?>" data-tab="<?php echo $k; ?>" data-target="#users-ajax-tab-content" class="ajax-load" data-url="<?php echo $tab_data["url"] ?>"><?php echo $tab_data["label"]; ?>
                                        <!--
                                        <?php if (isset($tab_data["badge"])): ?>
                                            <?php
                                            $badge_class = "badge-info";
                                            if (is_numeric($tab_data["badge"])) {

                                                $badge_class = $tab_data["badge"] == 0 ? "badge-error" : "badge-success";
                                            }
                                            ?>
                                                                                    <span class="badge <?php echo $badge_class ?> pull-right"><?php echo $tab_data["badge"]; ?></span>
                                        <?php endif; ?>
                                        -->
                                    </a></li>

                                <?php
                            endforeach;
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="span10">
                    <div id="users-tab-widget" class="widget-box nomargin widget-users-tab">
                        <div class="widget-title">

                            <span class="icon">
                                <i class="icon-<?php echo $tabs_data[$tab]["icon"]; ?>"></i>
                            </span>
                            <h5><?php echo $tabs_data[$tab]["label"]; ?></h5>


                        </div>
                        <div class="widget-content nopadding">
                            <div id="users-ajax-tab-content">
                                <?php
                                $view = CView::factory("users/detail/tab/" . $tab);
                                $view->user_id = $user_id;
                                echo $view->render();
                                ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</form>
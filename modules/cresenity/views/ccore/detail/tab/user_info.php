<?php
$app = CApp::instance();
$org = $app->org();
$user = $app->user();
$org_id = $org->org_id;
$db = CDatabase::instance();

$users = cuser::get($user_id);
?>
<div class="row-fluid">
    <div class="span6">
        <div class="control-group">			
            <label class="control-label"><?php echo clang::__('Username') ?></label>
            <div class="controls"><a href="<?php echo curl::base(); ?>sales/detail/<?php echo $users_id; ?>"><span class="label label-success"><?php echo $users->username; ?></span></a></div>
        </div>
        <div class="control-group">			
            <label class="control-label"><?php echo clang::__('First Name') ?></label>
            <div class="controls"><span class="label"><?php echo $users->first_name; ?></span></div>
        </div>
        <div class="control-group">			
            <label class="control-label"><?php echo clang::__('Last Name') ?></label>
            <div class="controls"><span class="label"><?php echo $users->last_name; ?></span></div>
        </div>
    </div>
    <div class="span6">
        <div class="control-group">			
            <label class="control-label"><?php echo clang::__('Last Request') ?></label>
            <div class="controls"><span class="label"><?php echo $users->last_request; ?></span></div>
        </div>
        <div class="control-group">			
            <label class="control-label"><?php echo clang::__('Hit Count') ?></label>
            <div class="controls"><span class="label"><?php echo cuser::hit_count($users->user_id); ?></span></div>
        </div>
    </div>
</div>
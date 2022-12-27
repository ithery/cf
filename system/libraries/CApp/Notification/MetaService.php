<?php
class CApp_Notification_MetaService {
    public function __construct() {
    }

    public function render() {
        return "<?php if(c::app()->notification()->isEnabled()) { echo \$__env->make('cresenity.notification.meta')->render(); }  ?>";
    }
}

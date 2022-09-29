<?php
class CApp_Notification_JavascriptService {
    public function __construct() {
    }

    public function render() {
        return "<?php if(c::app()->notification()->isEnabled()) { echo \$__env->make('cresenity.notification.javascript')->render(); }  ?>";
    }
}

<?php
class CApp_Notification_MetaService {
    public function __construct() {
    }

    /**
     * @return string
     */
    public function render() {
        return "<?php if(c::app()->notification()->isEnabled()) { echo \$__env->make('cresenity.notification.meta')->render(); }  ?>";
    }
}

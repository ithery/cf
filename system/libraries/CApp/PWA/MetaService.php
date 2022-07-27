<?php
class CApp_PWA_MetaService {
    public function render($startUrl) {
        $startUrl = '/' . trim($startUrl, '/') . '/';

        return "<?php if(c::app()->pwa()->isEnabled()) { \$config = (new \CApp_PWA_ManifestService)->generate('" . $startUrl . "'); echo \$__env->make( 'cresenity.pwa.meta' , ['config' => \$config])->render(); }  ?>";
    }
}

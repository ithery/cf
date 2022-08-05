<?php
class CApp_PWA_MetaService {
    protected $group;

    public function __construct($group) {
        $this->group = $group;
    }

    public function render() {
        return "<?php if(c::app()->pwa('" . $this->group . "')->isEnabled()) { \$config = (new \CApp_PWA_ManifestService('" . $this->group . "'))->generate(); echo \$__env->make( 'cresenity.pwa.meta' , ['group' => '" . $this->group . "', 'config' => \$config])->render(); }  ?>";
    }
}

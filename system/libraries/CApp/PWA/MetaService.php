<?php
class CApp_PWA_MetaService {
    /**
     * @var string
     */
    protected $group;

    /**
     * @param string $group
     */
    public function __construct($group) {
        $this->group = $group;
    }

    /**
     * @return string
     */
    public function render() {
        return "<?php if(c::app()->pwa('" . $this->group . "')->isEnabled()) { \$config = (new \CApp_PWA_ManifestService('" . $this->group . "'))->generate(); echo \$__env->make( 'cresenity.pwa.meta' , ['group' => '" . $this->group . "', 'config' => \$config])->render(); }  ?>";
    }
}

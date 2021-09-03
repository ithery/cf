<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 3, 2018, 4:28:05 PM
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_ClientModules {
    /**
     * @deprecated since version 1.2, please use function registerModules
     *
     * @param string $modules
     *
     * @return type
     */
    public function register_modules($modules) {
        return $this->registerModules($modules);
    }

    /**
     * @deprecated since version 1.2, please use function registerModules
     *
     * @param string $module
     * @param string $parent
     *
     * @return type
     */
    public function register_module($module, $parent = null) {
        return $this->registerModule($module, $parent);
    }

    /**
     * @deprecated since version 1.2, please use function requireJs
     *
     * @param string $js
     *
     * @return type
     */
    public function require_js($js) {
        return $this->requireJs($js);
    }

    /**
     * @deprecated since version 1.2, please use function allModules
     *
     * @return type
     */
    public function all_modules() {
        return $this->allModules();
    }

    /**
     * @deprecated since version 1.2, please use function isRegisteredModule
     *
     * @param type $mod
     *
     * @return type
     */
    public function is_registered_module($mod) {
        return $this->isRegisteredModule($mod);
    }
}
//@codingStandardsIgnoreEnd

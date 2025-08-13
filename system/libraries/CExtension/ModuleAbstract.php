<?php

class CExtension_ModuleAbstract {
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $clientModules;

    /**
     * @var array
     */
    protected $nav;

    /**
     * @var string
     */
    protected $parentNav;

    public function getName() {
        return $this->name;
    }

    /**
     * Get config set in config/extension.php.
     *
     * @param string $key
     * @param null   $default
     *
     * @return mixed
     */
    public function config($key = null, $default = null) {
        $name = $this->name;

        if (is_null($key)) {
            $key = sprintf('extension.%s', $name);
        } else {
            $key = sprintf('extension.%s.%s', $name, $key);
        }

        return CF::config($key, $default);
    }
}

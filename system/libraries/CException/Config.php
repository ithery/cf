<?php

use Illuminate\Contracts\Support\Arrayable;

class CException_Config implements Arrayable {
    use CTrait_HasOptions;

    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function __construct() {
        $this->options = CConfig::repository()->get('exception');
    }

    public function editor() {
        return $this->getOption('editor', null);
    }

    public function remoteSitesPath() {
        return $this->getOption('remote_sites_path', null);
    }

    public function localSitesPath() {
        return $this->getOption('local_sites_path', null);
    }

    public function theme() {
        return $this->getOption('theme', null);
    }

    public function shareButtonEnabled() {
        return $this->getOption('enable_share_button', false);
    }

    public function runnableSolutionsEnabled() {
        return $this->getOption('enable_runnable_solutions', false);
    }

    public function toArray() {
        return [
            'editor' => $this->editor(),
            'remoteSitesPath' => $this->remoteSitesPath(),
            'localSitesPath' => $this->localSitesPath(),
            'theme' => $this->theme(),
            'enableShareButton' => $this->shareButtonEnabled(),
            'enableRunnableSolutions' => $this->runnableSolutionsEnabled(),
            'directorySeparator' => DIRECTORY_SEPARATOR,
        ];
    }
}

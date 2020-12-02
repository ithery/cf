<?php

/**
 * Description of Navigation
 *
 * @author Hery
 */
trait CApp_Concern_Navigation {

    protected $nav = 'nav';

    public function setNav($nav) {
        $this->nav = $this->resolveNav($nav);
    }

    public function resolveNav($nav) {
        if (is_string($nav)) {
            $fileNav = CF::getFile('nav', $nav);
            if ($fileNav != null) {
                $nav = include $fileNav;
            }
        }
        return $nav;
    }

    public function getNav() {

        return $this->resolveNav($this->nav);
    }

}

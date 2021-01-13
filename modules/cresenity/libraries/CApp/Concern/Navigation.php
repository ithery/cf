<?php

/**
 * Description of Navigation
 *
 * @author Hery
 */
trait CApp_Concern_Navigation {
    protected $nav = 'nav';
    protected $navRenderer = CApp_Navigation_Engine_SideNav::class;

    public function setNav($nav) {
        $this->nav = $this->resolveNav($nav);
    }

    public function resolveNav($nav) {
        if (is_string($nav)) {
            $fileNav = CF::getFile('navs', $nav);
            // if ($fileNav == null) {
            //     throw new CApp_Exception(c::__('Nav :nav not exists', ['nav' => $nav]));
            // }
            if ($fileNav != null) {
                $nav = include $fileNav;
            }
        }
        return $nav;
    }

    public function resolveNavRenderer($renderer = null) {
        if ($renderer == null) {
            $renderer = CApp::instance()->getNavRenderer();
        }
        if (is_array($renderer)) {
            $engine = carr::get($renderer, 'engine', 'Bootstrap');
            $layout = carr::get($renderer, 'layout', 'horizontal');

            $engineClassName = 'CApp_Navigation_Engine_' . $engine;
            $renderer = CContainer::getInstance()->make($engineClassName);
        }
        if (is_string($renderer) && class_exists($renderer)) {
            $renderer = CContainer::getInstance()->make($renderer);
        }

        if ($renderer instanceof Closure || is_callable($renderer)) {
            $engine = new CApp_Navigation_Engine_Closure();
            $engine->setClosure($renderer);
            $renderer = $engine;
        }

        if (!($renderer instanceof CApp_Navigation_Engine)) {
            throw new Exception('Renderer must extends CNavigation_Engine');
        }
        return $renderer;
    }

    public function getNav() {
        return $this->resolveNav($this->nav);
    }

    public function getNavRenderer() {
        return $this->navRenderer;
    }

    public function setNavRenderer($renderer) {
        $this->navRenderer = $renderer;
    }
}

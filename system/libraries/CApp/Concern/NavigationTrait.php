<?php

/**
 * Description of Navigation.
 *
 * @author Hery
 */
trait CApp_Concern_NavigationTrait {
    protected $nav = 'nav';

    protected $resolvedNavName = null;

    protected $navRenderer = CApp_Navigation_Engine_SideNav::class;

    public function setNav($nav) {
        $this->nav = $this->resolveNav($nav);

        return $this;
    }

    public function resolveNav($nav) {
        $this->resolvedNavName = null;
        if (is_callable($nav)) {
            $this->resolvedNavName = '{Closure}';
            $nav = $nav();
        }
        if (is_string($nav)) {
            if ($this->resolvedNavName == null) {
                $this->navName = $nav;
            }
            $fileNav = CF::getFile('navs', $nav);
            if ($fileNav == null) {
                if ($nav == 'nav') {
                    $fileNav = CF::getFile('config', $nav);
                }
            }
            // if ($fileNav == null) {
            //     throw new CApp_Exception(c::__('Nav :nav not exists', ['nav' => $nav]));
            // }
            if ($fileNav != null) {
                $nav = include $fileNav;
            } else {
                throw new Exception('nav ' . $nav . ' is not found');
            }
        }
        if ($this->resolvedNavName == null) {
            $this->resolvedNavName = '{array}';
        }

        return $nav;
    }

    public function getNavName() {
        if ($this->resolvedNavName != null) {
            return $this->resolvedNavName;
        }

        return null;
    }

    /**
     * Resolve Nav Engine.
     *
     * @param mixed $renderer
     *
     * @return CApp_Navigation_EngineInterface
     */
    public function resolveNavRenderer($renderer = null) {
        if ($renderer == null) {
            $renderer = CApp::instance()->getNavRenderer();
        }
        if (is_array($renderer)) {
            $engine = carr::get($renderer, 'engine', 'Bootstrap');
            $layout = carr::get($renderer, 'layout', 'horizontal');

            $engineClassName = 'CApp_Navigation_Engine_' . $engine;
            $renderer = c::container($engineClassName);
        }
        if (is_string($renderer) && class_exists($renderer)) {
            $renderer = c::container($renderer);
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

        return $this;
    }
}

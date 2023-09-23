<?php

/**
 * Description of Navigation.
 *
 * @author Hery
 */
trait CApp_Concern_NavigationTrait {
    /**
     * @var CNavigation_Nav
     */
    protected $nav = null;

    protected $navRenderer = CApp_Navigation_Engine_SideNav::class;

    public function setNav($nav) {
        $this->nav = $this->resolveNav($nav);

        return $this;
    }

    public function resolveNav($nav) {
        return CNavigation::manager()->resolveNav($nav);
    }

    public function getNavName() {
        return $this->nav ? $this->nav->getName() : null;
    }

    /**
     * Resolve Nav Engine.
     *
     * @param mixed $renderer
     *
     * @return CApp_Navigation_EngineInterface
     */
    public function resolveNavRenderer($renderer = null) {
        return CNavigation::manager()->resolveRenderer($renderer);
    }

    /**
     * Undocumented function.
     *
     * @return null|CNavigation_Nav
     */
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

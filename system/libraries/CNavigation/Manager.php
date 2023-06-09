<?php

class CNavigation_Manager {
    /**
     * @var array<CNavigation_Nav>
     */
    protected $navs = [];

    protected static $instance;

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function nav($nav = 'nav') {
        return $this->resolveNav($nav);
    }

    private function resolveName($nav) {
        if ($nav instanceof Closure) {
            return 'closure-' . $this->closureHash($nav);
        }
        if (is_array($nav)) {
            return  'array-' . carr::hash($nav);
        }
        if (is_string($nav)) {
            return $nav;
        }

        throw new Exception('Nav with type ' . gettype($nav) . ' is not supported');
    }

    public function resolveNav($nav) {
        if ($nav === null) {
            return [];
        }
        if ($nav instanceof CNavigation_Nav) {
            return $nav;
        }

        $name = $this->resolveName($nav);
        if (isset($this->navs[$name])) {
            return $this->navs[$name];
        }

        if (is_string($nav)) {
            $fileNav = CF::getFile('navs', $nav);
            if ($fileNav == null) {
                if ($nav == 'nav') {
                    $fileNav = CF::getFile('config', $nav);
                }
            }
            if ($fileNav != null) {
                $nav = include $fileNav;
            } else {
                if ($nav !== 'nav') {
                    throw new Exception('nav ' . $nav . ' is not found');
                }
            }
        }

        if ($nav instanceof Closure) {
            $nav = $nav();
        }

        $this->navs[$name] = new CNavigation_Nav($name, $nav);

        return $this->navs[$name];
    }

    protected function closureHash(Closure $closure) {
        $reflection = new CFunction_SerializableClosure_Support_ReflectionClosure($closure);

        return md5($reflection->getFileName() . $reflection->getStartLine() . $reflection->getEndLine());
    }
}

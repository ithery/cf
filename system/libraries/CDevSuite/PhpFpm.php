<?php

/**
 * Description of PhpFpm.
 *
 * @author Hery
 */
abstract class CDevSuite_PhpFpm {
    abstract public function restart();

    abstract public function install();

    abstract public function uninstall();

    abstract public function stop();
}

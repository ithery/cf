<?php

abstract class CDevSuite_ServiceManager {
    abstract public function start($services);

    abstract public function stop($services);

    abstract public function restart($services);

    abstract public function printStatus($services);

    abstract public function status($service);

    abstract public function disabled($service);

    abstract public function disable($services);

    abstract public function enable($services);
}

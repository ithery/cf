<?php

abstract class CDevSuite_PackageManager {
    abstract public function installed($package);

    abstract public function ensureInstalled($package);
}

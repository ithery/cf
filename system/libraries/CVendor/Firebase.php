<?php

use Kreait\Firebase\Factory;

class CVendor_Firebase extends Factory {
    public static function create($config = null) {
        $factory = new Factory();
        if ($config) {
            if (isset($config['json_credentials'])) {
                $config = $config['json_credentials'];
            }
            $factory = $factory->withServiceAccount($config);
        }
        return $factory;
    }
}

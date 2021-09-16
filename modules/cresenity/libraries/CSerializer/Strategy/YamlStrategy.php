<?php

use Symfony\Component\Yaml\Yaml;

class CSerializer_Strategy_YamlStrategy implements CSerializer_StrategyInterface {
    /**
     * @param mixed $value
     *
     * @return string
     */
    public function serialize($value) {
        return Yaml::dump($value);
    }

    /**
     * @param $value
     *
     * @return array
     */
    public function unserialize($value) {
        return Yaml::parse($value);
    }
}

<?php

abstract class CExporter_Event {
    /**
     * @return object
     */
    abstract public function getConcernable();

    /**
     * @return mixed
     */
    abstract public function getDelegate();

    /**
     * @param string $concern
     *
     * @return bool
     */
    public function appliesToConcern($concern) {
        return $this->getConcernable() instanceof $concern;
    }
}

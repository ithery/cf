<?php

/**
 * Description of SkipsOnError
 *
 * @author Hery
 */
interface CExporter_Concern_SkipsOnError {

    /**
     * @param Exception $e
     */
    public function onError(Exception $e);
}

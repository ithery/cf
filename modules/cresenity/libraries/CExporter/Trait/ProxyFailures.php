<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CExporter_Trait_ProxyFailures {

    /**
     * @param Throwable $e
     */
    public function failed(Throwable $e) {
        if (method_exists($this->sheetExport, 'failed')) {
            $this->sheetExport->failed($e);
        }
    }

}

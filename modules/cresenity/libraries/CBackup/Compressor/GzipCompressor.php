<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CBackup_Compressor_GzipCompressor extends CBackup_AbstractCompressor {

    public function useCommand() {
        return 'gzip';
    }

    public function useExtension() {
        return 'gz';
    }

}

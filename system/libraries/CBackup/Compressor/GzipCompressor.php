<?php

class CBackup_Compressor_GzipCompressor extends CBackup_AbstractCompressor {
    public function useCommand() {
        return 'gzip';
    }

    public function useExtension() {
        return 'gz';
    }
}

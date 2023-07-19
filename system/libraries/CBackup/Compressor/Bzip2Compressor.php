<?php

class Bzip2Compressor implements CBackup_AbstractCompressor {
    /**
     * @return string
     */
    public function useCommand() {
        return 'bzip2';
    }

    /**
     * @return string
     */
    public function useExtension() {
        return 'bz2';
    }
}

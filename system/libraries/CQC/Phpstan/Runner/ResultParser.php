<?php

class CQC_Phpstan_Runner_ResultParser {
    public function parse($content) {
        $regex = '#.+?({.+?})Note.+?$#ims';
        $result = [];
        if (preg_match($regex, $content, $matches)) {
            $json = carr::get($matches, 1);
            $result = json_decode($json, true);
        }

        return $result;
    }
}

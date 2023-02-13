<?php

class CQC_Phpstan_Runner_ResultParser {
    public function parse($content) {
        $regex = '#({.+?})Note.+?$#ims';
        $regex2 = '#({.+?})Something.+?$#ims';
        $result = [];
        if (preg_match($regex, $content, $matches)) {
            $json = carr::get($matches, 1);
            $result = json_decode($json, true);
        } elseif (preg_match($regex2, $content, $matches)) {
            $json = carr::get($matches, 1);
            $result = json_decode($json, true);
        } else {
            $result = json_decode($content, true);
        }

        return $result;
    }
}

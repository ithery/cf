<?php

class CResources_Exception_InvalidUrlException extends Exception {
    public static function doesNotStartWithProtocol(string $url): self {
        return new static("Could not add `{$url}` because it does not start with either `http://` or `https://`");
    }
}

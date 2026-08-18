<?php

class CImage_Pdf_Exception_PageDoesNotExistException extends Exception {
    public static function for(int $pageNumber) {
        return new static("Page {$pageNumber} does not exist.");
    }
}

<?php

class CImage_Pdf_Exception_PdfDoesNotExistException extends Exception {
    public static function for(string $pdfFile) {
        return new static("File '{$pdfFile}' does not exist.");
    }
}

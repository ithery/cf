<?php
/**
 * Ported from https://github.com/LibrePDF/OpenPDF.
 */
class CDocument_Pdf {
    public static function createDocument() {
        return new CDocument_Pdf_Document();
    }

    public static function createWriter(CDocument_Pdf_Document $document) {
        return new CDocument_Pdf_PdfWriter($document);
    }
}

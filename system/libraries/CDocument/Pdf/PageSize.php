<?php

class CDocument_Pdf_PageSize {
    public static function letter() {
        return new CDocument_Pdf_Element_RectangleReadOnly(0, 0, 612, 792);
    }

    public static function a4() {
        return new CDocument_Pdf_Element_RectangleReadOnly(0, 0, 595, 842);
    }
}

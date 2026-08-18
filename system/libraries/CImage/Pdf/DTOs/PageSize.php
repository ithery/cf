<?php

class CImage_Pdf_DTOs_PageSize {
    public int $width;

    public int $height;

    public function __construct(int $width, int $height) {
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @param int $width
     * @param int $height
     *
     * @return static
     */
    public static function make(int $width, int $height) {
        return new self($width, $height);
    }
}

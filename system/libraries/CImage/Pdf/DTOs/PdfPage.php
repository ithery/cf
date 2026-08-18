<?php

class CImage_Pdf_DTOs_PdfPage {
    public int $number;

    public string $format;

    public string $prefix;

    public string $path;

    public function __construct(int $number, string $format, string $prefix, string $path) {
        $this->number = $number;
        $this->format = $format;
        $this->prefix = $prefix;
        $this->path = $path;
    }

    public static function make(int $pageNumber, string $format, string $prefix, string $path): self {
        return new self($pageNumber, $format, $prefix, $path);
    }

    public function filename(): string {
        $info = pathinfo($this->path);

        return $info['dirname'] . DIRECTORY_SEPARATOR . $this->prefix . $info['filename'] . '.' . $this->format;
    }
}

<?php

class CImage_Pdf_Enums_OutputFormat {
    const JPG = 'jpg';

    const JPEG = 'jpeg';

    const PNG = 'png';

    const WEBP = 'webp';

    public static function isValid(int $value): bool {
        return in_array($value, [
            self::JPG,
            self::JPEG,
            self::PNG,
            self::WEBP,
        ]);
    }
}

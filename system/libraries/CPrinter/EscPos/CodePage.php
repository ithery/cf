<?php

/**
 * Class to handle data about a particular CodePage, as loaded from the receipt print
 * database.
 *
 * Also computes map between UTF-8 and this encoding if necessary, using the intl library.
 */
class CPrinter_EscPos_CodePage {
    /**
     * Value to use when no character is set. This is a space in ASCII.
     */
    const MISSING_CHAR_CODE = 0x20;

    /**
     * @var null|array
     *                 Data string, null if not known (can be computed with iconv)
     */
    protected $data;

    /**
     * @var null|string
     *                  Iconv encoding name, null if not known
     */
    protected $iconv;

    /**
     * @var string
     *             Internal ID of the CodePage
     */
    protected $id;

    /**
     * @var string
     *             Name of the code page. Substituted with the ID if not set.
     */
    protected $name;

    /**
     * @var null|string
     *                  Notes on this code page, or null if not set
     */
    protected $notes;

    /**
     * @param string $id
     *                             Unique internal identifier for the CodePage
     * @param array  $codePageData
     *                             Associative array of CodePage data, as
     *                             specified by the upstream receipt-print-hq/escpos-printer-db database.
     *                             May contain 'name', 'data', 'iconv', and 'notes' fields.
     */
    public function __construct($id, array $codePageData) {
        $this->id = $id;
        $this->name = isset($codePageData['name']) ? $codePageData['name'] : $id;
        $this->data = isset($codePageData['data']) ? self::encodingArrayFromData($codePageData['data']) : null;
        $this->iconv = isset($codePageData['iconv']) ? $codePageData['iconv'] : null;
        $this->notes = isset($codePageData['notes']) ? $codePageData['notes'] : null;
    }

    /**
     * Get a 128-entry array of unicode code-points from this code page.
     *
     * @throws InvalidArgumentException where the data is now known or computable
     *
     * @return array data for this encoding
     */
    public function getDataArray() : array {
        // Make string
        if ($this->data !== null) {
            // Return data if known
            return $this->data;
        }
        if ($this->iconv !== null) {
            // Calculate with iconv if we know the encoding name
            $this->data = self::generateEncodingArray($this->iconv);

            return $this->data;
        }
        // Can't encode..
        throw new InvalidArgumentException('Cannot encode this code page');
    }

    /**
     * @return null|string iconv encoding name, or null if not set
     */
    public function getIconv() {
        return $this->iconv;
    }

    /**
     * @return string unique identifier of the code page
     */
    public function getId() : string {
        return $this->id;
    }

    /**
     * @return string name of the code page
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * The notes may explain quirks about a code-page, such as a source if it's non-standard or un-encodeable.
     *
     * @return null|string notes on the code page, or null if not set
     */
    public function getNotes() {
        return $this->notes;
    }

    /**
     * @return bool True if we can encode with this code page (ie, we know what data it holds).
     *
     * Many printers contain vendor-specific code pages, which are named but have not been identified or
     * typed out. For our purposes, this is an "un-encodeable" code page.
     */
    public function isEncodable() {
        return $this->iconv !== null || $this->data !== null;
    }

    /**
     * Given an ICU encoding name, generate a 128-entry array, with the unicode code points
     * for the character at positions 128-255 in this code page.
     *
     * @param string $encodingName Name of the encoding
     *
     * @return array 128-entry array of code points
     */
    protected static function generateEncodingArrayNative(string $encodingName): array {
        // Loop through 128 code points
        $intArray = array_fill(0, 128, self::MISSING_CHAR_CODE);

        for ($char = 128; $char <= 255; $char++) {
            $encodingChar = chr($char);
            $utf8 = false;

            try {
                $utf8 = iconv($encodingName, 'UTF-8', $encodingChar);
            } catch (Exception $e) {
                //do nothing
            }

            if ($utf8 === false) {
                continue; // Cannot be mapped to Unicode
            }

            // Ensure the length of UTF-8 character is 1 (single byte)
            if (strlen($utf8) !== 1) {
                continue; // Skip multi-byte characters
            }

            // Get the Unicode code point of the UTF-8 character
            $codePoints = unpack('C*', $utf8);
            $unicodeCodePoint = reset($codePoints);

            // Replace space with the correct character if we found it
            $intArray[$char - 128] = $unicodeCodePoint;
        }

        assert(count($intArray) == 128);

        return $intArray;
    }

    /**
     * Given an ICU encoding name, generate a 128-entry array, with the unicode code points
     * for the character at positions 128-255 in this code page.
     *
     * @param string $encodingName Name of the encoding
     *
     * @return array 128-entry array of code points
     */
    protected static function generateEncodingArray(string $encodingName) : array {
        if (!class_exists(\UConverter::class)) {
            return self::generateEncodingArrayNative($encodingName);
        }
        // Set up converter for encoding
        $missingChar = chr(self::MISSING_CHAR_CODE);
        // Throws a lot of warnings for ambiguous code pages, but fallbacks seem fine.
        $converter = @new \UConverter('UTF-8', $encodingName);
        $converter->setSubstChars($missingChar);
        // Loop through 128 code points
        $intArray = array_fill(0, 128, self::MISSING_CHAR_CODE);
        for ($char = 128; $char <= 255; $char++) {
            // Try to identify the UTF-8 character at this position in the code page
            $encodingChar = chr($char);
            $utf8 = $converter->convert($encodingChar, false);
            if ($utf8 === $missingChar || $utf8 === false) {
                // Cannot be mapped to unicode
                continue;
            }
            $reverse = $converter->convert($utf8, true);
            if ($reverse !== $encodingChar) {
                // Avoid conversions which don't reverse well (eg. multi-byte code pages)
                continue;
            }
            // Replace space with the correct character if we found it
            $intArray[$char - 128] = \IntlChar::ord($utf8);
        }
        assert(count($intArray) == 128);

        return $intArray;
    }

    private static function encodingArrayFromDataNative(array $data): array {
        $text = implode('', $data); // Join lines
        $ret = array_fill(0, 128, self::MISSING_CHAR_CODE);
        $length = mb_strlen($text, 'UTF-8');
        for ($i = 0; $i < $length && $i < 128; $i++) {
            $codePoint = mb_ord(mb_substr($text, $i, 1), 'UTF-8');
            $ret[$i] = $codePoint;
        }
        assert(count($ret) == 128);

        return $ret;
    }

    private static function encodingArrayFromData(array $data) : array {
        if (!class_exists(\IntlBreakIterator::class)) {
            return self::encodingArrayFromDataNative($data);
        }
        $text = implode('', $data); // Join lines
        $codePointIterator = \IntlBreakIterator::createCodePointInstance();
        $codePointIterator->setText($text);
        $ret = array_fill(0, 128, self::MISSING_CHAR_CODE);
        for ($i = 0; ($codePointIterator->next() > 0) && ($i < 128); $i++) {
            $codePoint = $codePointIterator->getLastCodePoint();
            $ret[$i] = $codePoint;
        }
        assert(count($ret) == 128);

        return $ret;
    }
}

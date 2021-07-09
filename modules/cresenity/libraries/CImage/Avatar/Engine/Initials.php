<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2019, 1:43:07 AM
 */
use Intervention\Image\AbstractFont;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class CImage_Avatar_Engine_Initials extends CImage_Avatar_EngineAbstract {
    /** @var ImageManager */
    protected $image;

    /** @var Initials */
    protected $initials_generator;
    protected $driver = 'gd'; // imagick or gd
    protected $fontSize = 0.5;
    protected $name = 'John Doe';
    protected $size = 48;
    protected $bgColor = '#000';
    protected $fontColor = '#fff';
    protected $rounded = false;
    protected $smooth = false;
    protected $autofont = false;
    protected $keepCase = false;
    protected $fontFile = CConstant::CRESENITY_FONT_PATH . '/opensans/OpenSans-Regular.ttf';
    protected $generated_initials = 'JD';

    public function __construct() {
        $this->setupImageManager();
        $this->initials_generator = CString::initials();
    }

    /**
     * Create a ImageManager instance
     */
    protected function setupImageManager() {
        $this->image = new ImageManager(['driver' => $this->getDriver()]);
    }

    /**
     * Set the name used for generating initials.
     *
     * @param string $nameOrInitials
     *
     * @return $this
     */
    public function name($nameOrInitials) {
        $this->name = $nameOrInitials;
        $this->initials_generator->name($nameOrInitials);
        return $this;
    }

    /**
     * Transforms a unicode string to the proper format
     *
     * @param string $char the code to be converted (e.g., f007 would mean the "user" symbol)
     *
     * @return $this
     */
    public function glyph($char) {
        $uChar = json_decode(sprintf('"\u%s"', $char));
        $this->name($uChar);
        return $this;
    }

    /**
     * Set the length of the generated initials.
     *
     * @param int $length
     *
     * @return $this
     */
    public function length($length = 2) {
        $this->initials_generator->length($length);
        return $this;
    }

    /**
     * Set time avatar/image size in pixels.
     *
     * @param int $size
     *
     * @return $this
     */
    public function size($size) {
        $this->size = (int) $size;
        return $this;
    }

    /**
     * Set the background color.
     *
     * @param string $background
     *
     * @return $this
     */
    public function background($background) {
        $this->bgColor = (string) $background;
        return $this;
    }

    /**
     * Set the font color.
     *
     * @param string $color
     *
     * @return $this
     */
    public function color($color) {
        $this->fontColor = (string) $color;
        return $this;
    }

    /**
     * Set the font file by path or int (1-5).
     *
     * @param string|int $font
     *
     * @return $this
     */
    public function font($font) {
        $this->fontFile = $font;
        return $this;
    }

    /**
     * Use imagick as the driver.
     *
     * @return $this
     */
    public function imagick() {
        $this->driver = 'imagick';
        $this->setupImageManager();
        return $this;
    }

    /**
     * Use GD as the driver.
     *
     * @return $this
     */
    public function gd() {
        $this->driver = 'gd';
        $this->setupImageManager();
        return $this;
    }

    /**
     * Set if should make a round image or not.
     *
     * @param bool $rounded
     *
     * @return $this
     */
    public function rounded($rounded = true) {
        $this->rounded = (bool) $rounded;
        return $this;
    }

    /**
     * Set if should detect character script
     * and use a font that supports it.
     *
     * @param bool $autofont
     *
     * @return $this
     */
    public function autoFont($autofont = true) {
        $this->autofont = (bool) $autofont;
        return $this;
    }

    /**
     * Set if should make a rounding smoother with a resizing hack.
     *
     * @param bool $smooth
     *
     * @return $this
     */
    public function smooth($smooth = true) {
        $this->smooth = (bool) $smooth;
        return $this;
    }

    /**
     * Set if should skip uppercasing the name.
     *
     * @param bool $keepCase
     *
     * @return $this
     */
    public function keepCase($keepCase = true) {
        $this->keepCase = (bool) $keepCase;
        return $this;
    }

    /**
     * Set the font size in percentage
     * (0.1 = 10%).
     *
     * @param float $size
     *
     * @return $this
     */
    public function fontSize($size = 0.5) {
        $this->fontSize = number_format($size, 2);
        return $this;
    }

    /**
     * Generate the image.
     *
     * @param null|string $name
     *
     * @return Image
     */
    public function generate($name = null) {
        if ($name !== null) {
            $this->name = $name;
            $this->generated_initials = $this->initials_generator->keepCase($this->getKeepCase())->generate($name);
        }
        return $this->makeAvatar($this->image);
    }

    /**
     * Will return the generated initials.
     *
     * @return string
     */
    public function getInitials() {
        return $this->initials_generator->keepCase($this->getKeepCase())->name($this->name)->getInitials();
    }

    /**
     * Will return the background color parameter.
     *
     * @return string
     */
    public function getBackgroundColor() {
        return $this->bgColor;
    }

    /**
     * Will return the set driver.
     *
     * @return string
     */
    public function getDriver() {
        return $this->driver;
    }

    /**
     * Will return the font color parameter.
     *
     * @return string
     */
    public function getColor() {
        return $this->fontColor;
    }

    /**
     * Will return the font size parameter.
     *
     * @return float
     */
    public function getFontSize() {
        return $this->fontSize;
    }

    /**
     * Will return the font size parameter.
     *
     * @return string|int
     */
    public function getFontFile() {
        return $this->fontFile;
    }

    /**
     * Will return the round parameter.
     *
     * @return bool
     */
    public function getRounded() {
        return $this->rounded;
    }

    /**
     * Will return the smooth parameter.
     *
     * @return bool
     */
    public function getSmooth() {
        return $this->smooth;
    }

    /**
     * Will return the round parameter.
     *
     * @return int
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * Will return the keepCase parameter.
     *
     * @return boolean
     */
    public function getKeepCase() {
        return $this->keepCase;
    }

    /**
     * Will return the autofont parameter.
     *
     * @return bool
     */
    public function getAutoFont() {
        return $this->autofont;
    }

    /**
     * @param ImageManager $image
     *
     * @return Image
     */
    private function makeAvatar($image) {
        $size = $this->getSize();
        $bgColor = $this->getBackgroundColor();
        $name = $this->getInitials();
        $fontFile = $this->findFontFile();
        $color = $this->getColor();
        $fontSize = $this->getFontSize();
        if ($this->getRounded() && $this->getSmooth()) {
            $size *= 5;
        }
        $avatar = $image->canvas($size, $size, !$this->getRounded() ? $bgColor : null);
        if ($this->getRounded()) {
            $avatar = $avatar->circle($size - 2, $size / 2, $size / 2, function ($draw) use ($bgColor) {
                return $draw->background($bgColor);
            });
        }
        if ($this->getRounded() && $this->getSmooth()) {
            $size /= 5;
            $avatar->resize($size, $size);
        }
        return $avatar->text($name, $size / 2, $size / 2, function (AbstractFont $font) use ($size, $color, $fontFile, $fontSize) {
            $font->file($fontFile);
            $font->size($size * $fontSize);
            $font->color($color);
            $font->align('center');
            $font->valign('center');
        });
    }

    private function findFontFile() {
        $fontFile = $this->getFontFile();
        if ($this->getAutoFont()) {
            $fontFile = $this->getFontByScript();
        }
        if (is_int($fontFile) && in_array($fontFile, [1, 2, 3, 4, 5], false)) {
            return $fontFile;
        }
        if (file_exists($fontFile)) {
            return $fontFile;
        }
        if (file_exists(__DIR__ . $fontFile)) {
            return __DIR__ . $fontFile;
        }
        if (file_exists(__DIR__ . '/' . $fontFile)) {
            return __DIR__ . '/' . $fontFile;
        }
        return 1;
    }

    private function getFontByScript() {
        if (CString_Language::isArabic($this->getInitials())) {
            return CConstant::CRESENITY_FONT_PATH . '/notosans/script/Noto-Arabic-Regular.ttf';
        }
        if (CString_Language::isArmenian($this->getInitials())) {
            return CConstant::CRESENITY_FONT_PATH . '/notosans/script/Noto-Armenian-Regular.ttf';
        }
        if (CString_Language::isBengali($this->getInitials())) {
            return CConstant::CRESENITY_FONT_PATH . '/notosans/script/Noto-Bengali-Regular.ttf';
        }
        if (CString_Language::isGeorgian($this->getInitials())) {
            return CConstant::CRESENITY_FONT_PATH . '/notosans/script/Noto-Georgian-Regular.ttf';
        }
        if (CString_Language::isHebrew($this->getInitials())) {
            return CConstant::CRESENITY_FONT_PATH . '/notosans/script/Noto-Hebrew-Regular.ttf';
        }
        if (CString_Language::isMongolian($this->getInitials())) {
            return CConstant::CRESENITY_FONT_PATH . '/notosans/script/Noto-Mongolian-Regular.ttf';
        }
        if (CString_Language::isThai($this->getInitials())) {
            return CConstant::CRESENITY_FONT_PATH . '/notosans/script/Noto-Thai-Regular.ttf';
        }
        if (CString_Language::isTibetan($this->getInitials())) {
            return CConstant::CRESENITY_FONT_PATH . '/notosans/script/Noto-Tibetan-Regular.ttf';
        }
        if (CString_Language::isChinese($this->getInitials())) {
            return CConstant::CRESENITY_FONT_PATH . '/notosans/script/Noto-CJKJP-Regular.otf';
        }
        if (CString_Language::isJapanese($this->getInitials())) {
            return CConstant::CRESENITY_FONT_PATH . '/notosans/script/Noto-CJKJP-Regular.otf';
        }
        return $this->getFontFile();
    }
}

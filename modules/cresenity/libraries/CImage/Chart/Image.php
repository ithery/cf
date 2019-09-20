<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 30, 2019, 2:17:49 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CImage_Chart_Data as Data;
use CImage_Chart_Constant as Constant;

class CImage_Chart_Image extends CImage_Chart_Draw {

    /**
     * @param int $xSize
     * @param int $ySize
     * @param Data $dataSet
     * @param boolean $transparentBackground
     */
    public function __construct($xSize, $ySize, Data $dataSet = null, $transparentBackground = false) {
        parent::__construct();
        $this->transparentBackground = $transparentBackground;
        if ($dataSet) {
            $this->dataSet = $dataSet;
        }
        $this->xSize = $xSize;
        $this->ySize = $ySize;
        $this->picture = imagecreatetruecolor($xSize, $ySize);
        if ($this->transparentBackground) {
            imagealphablending($this->picture, false);
            imagefilledrectangle(
                    $this->picture, 0, 0, $xSize, $ySize, imagecolorallocatealpha($this->picture, 255, 255, 255, 127)
            );
            imagealphablending($this->picture, true);
            imagesavealpha($this->picture, true);
        } else {
            $C_White = $this->allocateColor($this->picture, 255, 255, 255);
            imagefilledrectangle($this->picture, 0, 0, $xSize, $ySize, $C_White);
        }
    }

    /**
     * Enable / Disable and set shadow properties
     * @param boolean $enabled
     * @param array $format
     */
    public function setShadow($enabled = true, array $format = []) {
        $x = isset($format["x"]) ? $format["x"] : 2;
        $y = isset($format["y"]) ? $format["y"] : 2;
        $r = isset($format["r"]) ? $format["r"] : 0;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 10;
        $this->Shadow = $enabled;
        $this->ShadowX = $x;
        $this->ShadowY = $y;
        $this->ShadowR = $r;
        $this->ShadowG = $g;
        $this->ShadowB = $b;
        $this->Shadowa = $alpha;
    }

    /**
     * Set the graph area position
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @return int|null
     */
    public function setGraphArea($x1, $y1, $x2, $y2) {
        if ($x2 < $x1 || $x1 == $x2 || $y2 < $y1 || $y1 == $y2) {
            return -1;
        }
        $this->graphAreaX1 = $x1;
        $this->dataSet->data["graphArea"]["x1"] = $x1;
        $this->graphAreaY1 = $y1;
        $this->dataSet->data["graphArea"]["y1"] = $y1;
        $this->graphAreaX2 = $x2;
        $this->dataSet->data["graphArea"]["x2"] = $x2;
        $this->graphAreaY2 = $y2;
        $this->dataSet->data["graphArea"]["y2"] = $y2;
    }

    /**
     * Return the width of the picture
     * @return int
     */
    public function getWidth() {
        return $this->xSize;
    }

    /**
     * Return the heigth of the picture
     * @return int
     */
    public function getHeight() {
        return $this->ySize;
    }

    /**
     * Render the picture to a file
     * @param string $fileName
     */
    public function render($fileName) {
        if ($this->transparentBackground) {
            imagealphablending($this->picture, false);
            imagesavealpha($this->picture, true);
        }
        imagepng($this->picture, $fileName);
    }

    public function __toString() {
        if ($this->transparentBackground) {
            imagealphablending($this->picture, false);
            imagesavealpha($this->picture, true);
        }
        ob_start();
        imagepng($this->picture);
        return ob_get_clean();
    }

    public function toDataURI() {
        return 'data:image/png;base64,' . base64_encode($this->__toString());
    }

    /**
     * Render the picture to a web browser stream
     * @param boolean $browserExpire
     */
    public function stroke($browserExpire = false) {
        if ($this->transparentBackground) {
            imagealphablending($this->picture, false);
            imagesavealpha($this->picture, true);
        }
        if ($browserExpire) {
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Cache-Control: no-cache");
            header("Pragma: no-cache");
        }
        header('Content-type: image/png');
        imagepng($this->picture);
    }

    /**
     * Automatic output method based on the calling interface
     * @param string $fileName
     */
    public function autoOutput($fileName = "output.png") {
        if (php_sapi_name() == "cli") {
            $this->Render($fileName);
        } else {
            $this->Stroke();
        }
    }

    /**
     * Return the length between two points
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @return float
     */
    public function getLength($x1, $y1, $x2, $y2) {
        return sqrt(
                pow(max($x1, $x2) - min($x1, $x2), 2) + pow(max($y1, $y2) - min($y1, $y2), 2)
        );
    }

    /**
     * Return the orientation of a line
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @return int
     */
    public function getAngle($x1, $y1, $x2, $y2) {
        $opposite = $y2 - $y1;
        $adjacent = $x2 - $x1;
        $angle = rad2deg(atan2($opposite, $adjacent));
        if ($angle > 0) {
            return $angle;
        } else {
            return 360 - abs($angle);
        }
    }

    /**
     * Return the surrounding box of text area
     * @param int $x
     * @param int $y
     * @param string $fontName
     * @param int $fontSize
     * @param int $angle
     * @param int $Text
     * @return array
     */
    public function getTextBox($x, $y, $fontName, $fontSize, $angle, $Text) {
        $coords = imagettfbbox($fontSize, 0, $this->loadFont($fontName, 'fonts'), $Text);
        $a = deg2rad($angle);
        $ca = cos($a);
        $sa = sin($a);
        $realPos = [];
        for ($i = 0; $i < 7; $i += 2) {
            $realPos[$i / 2]["x"] = $x + round($coords[$i] * $ca + $coords[$i + 1] * $sa);
            $realPos[$i / 2]["y"] = $y + round($coords[$i + 1] * $ca - $coords[$i] * $sa);
        }
        $realPos[Constant::TEXT_ALIGN_BOTTOMLEFT]["x"] = $realPos[0]["x"];
        $realPos[Constant::TEXT_ALIGN_BOTTOMLEFT]["y"] = $realPos[0]["y"];
        $realPos[Constant::TEXT_ALIGN_BOTTOMRIGHT]["x"] = $realPos[1]["x"];
        $realPos[Constant::TEXT_ALIGN_BOTTOMRIGHT]["y"] = $realPos[1]["y"];
        $realPos[Constant::TEXT_ALIGN_TOPLEFT]["x"] = $realPos[3]["x"];
        $realPos[Constant::TEXT_ALIGN_TOPLEFT]["y"] = $realPos[3]["y"];
        $realPos[Constant::TEXT_ALIGN_TOPRIGHT]["x"] = $realPos[2]["x"];
        $realPos[Constant::TEXT_ALIGN_TOPRIGHT]["y"] = $realPos[2]["y"];
        $realPos[Constant::TEXT_ALIGN_BOTTOMMIDDLE]["x"] = ($realPos[1]["x"] - $realPos[0]["x"]) / 2 + $realPos[0]["x"];
        $realPos[Constant::TEXT_ALIGN_BOTTOMMIDDLE]["y"] = ($realPos[0]["y"] - $realPos[1]["y"]) / 2 + $realPos[1]["y"];
        $realPos[Constant::TEXT_ALIGN_TOPMIDDLE]["x"] = ($realPos[2]["x"] - $realPos[3]["x"]) / 2 + $realPos[3]["x"];
        $realPos[Constant::TEXT_ALIGN_TOPMIDDLE]["y"] = ($realPos[3]["y"] - $realPos[2]["y"]) / 2 + $realPos[2]["y"];
        $realPos[Constant::TEXT_ALIGN_MIDDLELEFT]["x"] = ($realPos[0]["x"] - $realPos[3]["x"]) / 2 + $realPos[3]["x"];
        $realPos[Constant::TEXT_ALIGN_MIDDLELEFT]["y"] = ($realPos[0]["y"] - $realPos[3]["y"]) / 2 + $realPos[3]["y"];
        $realPos[Constant::TEXT_ALIGN_MIDDLERIGHT]["x"] = ($realPos[1]["x"] - $realPos[2]["x"]) / 2 + $realPos[2]["x"];
        $realPos[Constant::TEXT_ALIGN_MIDDLERIGHT]["y"] = ($realPos[1]["y"] - $realPos[2]["y"]) / 2 + $realPos[2]["y"];
        $realPos[Constant::TEXT_ALIGN_MIDDLEMIDDLE]["x"] = ($realPos[1]["x"] - $realPos[3]["x"]) / 2 + $realPos[3]["x"];
        $realPos[Constant::TEXT_ALIGN_MIDDLEMIDDLE]["y"] = ($realPos[0]["y"] - $realPos[2]["y"]) / 2 + $realPos[2]["y"];
        return $realPos;
    }

    /**
     * Set current font properties
     * @param array $format
     */
    public function setFontProperties($format = []) {
        $r = isset($format["r"]) ? $format["r"] : -1;
        $g = isset($format["g"]) ? $format["g"] : -1;
        $b = isset($format["b"]) ? $format["b"] : -1;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $fontName = isset($format["FontName"]) ? $format["FontName"] : null;
        $fontSize = isset($format["FontSize"]) ? $format["FontSize"] : null;
        if ($r != -1) {
            $this->fontColorR = $r;
        }
        if ($g != -1) {
            $this->fontColorG = $g;
        }
        if ($b != -1) {
            $this->fontColorB = $b;
        }
        if ($alpha != null) {
            $this->fontColorA = $alpha;
        }
        if ($fontName != null) {
            $this->fontName = $this->loadFont($fontName, 'fonts');
        }
        if ($fontSize != null) {
            $this->fontSize = $fontSize;
        }
    }

    /**
     * Returns the 1st decimal values (used to correct AA bugs)
     * @param mixed $value
     * @return mixed
     */
    public function getFirstDecimal($value) {
        $values = preg_split("/\./", $value);
        if (isset($values[1])) {
            return substr($values[1], 0, 1);
        } else {
            return 0;
        }
    }

    /**
     * Attach a dataset to your pChart Object
     * @param Data $dataSet
     */
    public function setDataSet(Data $dataSet) {
        $this->dataSet = $dataSet;
    }

    /**
     * Print attached dataset contents to STDOUT
     */
    public function printDataSet() {
        print_r($this->dataSet);
    }

    /**
     * Initialise the image map methods
     * @param string $name
     * @param int $storageMode
     * @param string $UniqueID
     * @param string $storageFolder
     */
    public function initialiseImageMap(
    $name = "pChart", $storageMode = IMAGE_MAP_STORAGE_SESSION, $UniqueID = "imageMap", $storageFolder = "tmp"
    ) {
        $this->imageMapIndex = $name;
        $this->imageMapStorageMode = $storageMode;
        if ($storageMode == IMAGE_MAP_STORAGE_SESSION) {
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION[$this->imageMapIndex] = null;
        } elseif ($storageMode == IMAGE_MAP_STORAGE_FILE) {
            $this->imageMapFileName = $UniqueID;
            $this->imageMapStorageFolder = $storageFolder;
            $path = sprintf("%s/%s.map", $storageFolder, $UniqueID);
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }

    /**
     * Add a zone to the image map
     *
     * @param string $Type
     * @param string $Plots
     * @param string|null $Color
     * @param string $Title
     * @param string $Message
     * @param boolean $hTMLEncode
     */
    public function addToImageMap(
    $Type, $Plots, $Color = null, $Title = null, $Message = null, $hTMLEncode = false
    ) {
        if ($this->imageMapStorageMode == null) {
            $this->initialiseImageMap();
        }
        /* Encode the characters in the imagemap in HTML standards */
        $Title = htmlentities(
                str_replace("&#8364;", "\u20AC", $Title), ENT_QUOTES, "ISO-8859-15"
        );
        if ($hTMLEncode) {
            $Message = str_replace(
                    "&gt;", ">", str_replace(
                            "&lt;", "<", htmlentities($Message, ENT_QUOTES, "ISO-8859-15")
                    )
            );
        }
        if ($this->imageMapStorageMode == IMAGE_MAP_STORAGE_SESSION) {
            if (!isset($_SESSION)) {
                $this->initialiseImageMap();
            }
            $_SESSION[$this->imageMapIndex][] = [$Type, $Plots, $Color, $Title, $Message];
        } elseif ($this->imageMapStorageMode == IMAGE_MAP_STORAGE_FILE) {
            $handle = fopen(
                    sprintf("%s/%s.map", $this->imageMapStorageFolder, $this->imageMapFileName), 'a'
            );
            fwrite(
                    $handle, sprintf(
                            "%s%s%s%s%s%s%s%s%s\r\n", $Type, IMAGE_MAP_DELIMITER, $Plots, IMAGE_MAP_DELIMITER, $Color, IMAGE_MAP_DELIMITER, $Title, IMAGE_MAP_DELIMITER, $Message
                    )
            );
            fclose($handle);
        }
    }

    /**
     * Remove VOID values from an imagemap custom values array
     * @param string $serieName
     * @param array $values
     * @return array
     */
    public function removeVOIDFromArray($serieName, array $values) {
        if (!isset($this->dataSet->data["series"][$serieName])) {
            return -1;
        }
        $result = [];
        foreach ($this->dataSet->data["series"][$serieName]["data"] as $key => $value) {
            if ($value != Constant::VOID && isset($values[$key])) {
                $result[] = $values[$key];
            }
        }
        return $result;
    }

    /**
     * Replace the title of one image map serie
     * @param string $oldTitle
     * @param string|array $newTitle
     * @return null|int
     */
    public function replaceImageMapTitle($oldTitle, $newTitle) {
        if ($this->imageMapStorageMode == null) {
            return -1;
        }
        if (is_array($newTitle)) {
            $newTitle = $this->removeVOIDFromArray($oldTitle, $newTitle);
        }
        if ($this->imageMapStorageMode == IMAGE_MAP_STORAGE_SESSION) {
            if (!isset($_SESSION)) {
                return -1;
            }
            if (is_array($newTitle)) {
                $ID = 0;
                foreach ($_SESSION[$this->imageMapIndex] as $key => $settings) {
                    if ($settings[3] == $oldTitle && isset($newTitle[$ID])) {
                        $_SESSION[$this->imageMapIndex][$key][3] = $newTitle[$ID];
                        $ID++;
                    }
                }
            } else {
                foreach ($_SESSION[$this->imageMapIndex] as $key => $settings) {
                    if ($settings[3] == $oldTitle) {
                        $_SESSION[$this->imageMapIndex][$key][3] = $newTitle;
                    }
                }
            }
        } elseif ($this->imageMapStorageMode == IMAGE_MAP_STORAGE_FILE) {
            $TempArray = [];
            $handle = $this->openFileHandle();
            if ($handle) {
                while (($buffer = fgets($handle, 4096)) !== false) {
                    $fields = preg_split(
                            sprintf("/%s/", IMAGE_MAP_DELIMITER), str_replace([chr(10), chr(13)], "", $buffer)
                    );
                    $TempArray[] = [$fields[0], $fields[1], $fields[2], $fields[3], $fields[4]];
                }
                fclose($handle);
                if (is_array($newTitle)) {
                    $ID = 0;
                    foreach ($TempArray as $key => $settings) {
                        if ($settings[3] == $oldTitle && isset($newTitle[$ID])) {
                            $TempArray[$key][3] = $newTitle[$ID];
                            $ID++;
                        }
                    }
                } else {
                    foreach ($TempArray as $key => $settings) {
                        if ($settings[3] == $oldTitle) {
                            $TempArray[$key][3] = $newTitle;
                        }
                    }
                }
                $handle = $this->openFileHandle("w");
                foreach ($TempArray as $key => $settings) {
                    fwrite(
                            $handle, sprintf(
                                    "%s%s%s%s%s%s%s%s%s\r\n", $settings[0], IMAGE_MAP_DELIMITER, $settings[1], IMAGE_MAP_DELIMITER, $settings[2], IMAGE_MAP_DELIMITER, $settings[3], IMAGE_MAP_DELIMITER, $settings[4]
                            )
                    );
                }
                fclose($handle);
            }
        }
    }

    /**
     * Replace the values of the image map contents
     * @param string $Title
     * @param array $values
     * @return null|int
     */
    public function replaceImageMapValues($Title, array $values) {
        if ($this->imageMapStorageMode == null) {
            return -1;
        }
        $values = $this->removeVOIDFromArray($Title, $values);
        $ID = 0;
        if ($this->imageMapStorageMode == IMAGE_MAP_STORAGE_SESSION) {
            if (!isset($_SESSION)) {
                return -1;
            }
            foreach ($_SESSION[$this->imageMapIndex] as $key => $settings) {
                if ($settings[3] == $Title) {
                    if (isset($values[$ID])) {
                        $_SESSION[$this->imageMapIndex][$key][4] = $values[$ID];
                    }
                    $ID++;
                }
            }
        } elseif ($this->imageMapStorageMode == IMAGE_MAP_STORAGE_FILE) {
            $TempArray = [];
            $handle = $this->openFileHandle();
            if ($handle) {
                while (($buffer = fgets($handle, 4096)) !== false) {
                    $fields = preg_split(
                            "/" . IMAGE_MAP_DELIMITER . "/", str_replace([chr(10), chr(13)], "", $buffer)
                    );
                    $TempArray[] = [$fields[0], $fields[1], $fields[2], $fields[3], $fields[4]];
                }
                fclose($handle);
                foreach ($TempArray as $key => $settings) {
                    if ($settings[3] == $Title) {
                        if (isset($values[$ID])) {
                            $TempArray[$key][4] = $values[$ID];
                        }
                        $ID++;
                    }
                }
                $handle = $this->openFileHandle("w");
                foreach ($TempArray as $key => $settings) {
                    fwrite(
                            $handle, sprintf(
                                    "%s%s%s%s%s%s%s%s%s\r\n", $settings[0], IMAGE_MAP_DELIMITER, $settings[1], IMAGE_MAP_DELIMITER, $settings[2], IMAGE_MAP_DELIMITER, $settings[3], IMAGE_MAP_DELIMITER, $settings[4]
                            )
                    );
                }
                fclose($handle);
            }
        }
    }

    /**
     * Dump the image map
     * @param string $name
     * @param int $storageMode
     * @param string $UniqueID
     * @param string $storageFolder
     */
    public function dumpImageMap(
    $name = "pChart", $storageMode = IMAGE_MAP_STORAGE_SESSION, $UniqueID = "imageMap", $storageFolder = "tmp"
    ) {
        $this->imageMapIndex = $name;
        $this->imageMapStorageMode = $storageMode;
        if ($this->imageMapStorageMode == IMAGE_MAP_STORAGE_SESSION) {
            if (!isset($_SESSION)) {
                session_start();
            }
            if ($_SESSION[$name] != null) {
                foreach ($_SESSION[$name] as $key => $Params) {
                    echo $Params[0] . IMAGE_MAP_DELIMITER . $Params[1]
                    . IMAGE_MAP_DELIMITER . $Params[2] . IMAGE_MAP_DELIMITER
                    . $Params[3] . IMAGE_MAP_DELIMITER . $Params[4] . "\r\n";
                }
            }
        } elseif ($this->imageMapStorageMode == IMAGE_MAP_STORAGE_FILE) {
            if (file_exists($storageFolder . "/" . $UniqueID . ".map")) {
                $handle = @fopen($storageFolder . "/" . $UniqueID . ".map", "r");
                if ($handle) {
                    while (($buffer = fgets($handle, 4096)) !== false) {
                        echo $buffer;
                    }
                }
                fclose($handle);
                if ($this->imageMapAutoDelete) {
                    unlink($storageFolder . "/" . $UniqueID . ".map");
                }
            }
        }
        /* When the image map is returned to the client, the script ends */
        exit();
    }

    /**
     * Return the HTML converted color from the RGB composite values
     * @param int $r
     * @param int $g
     * @param int $b
     * @return string
     */
    public function toHTMLColor($r, $g, $b) {
        $r = intval($r);
        $g = intval($g);
        $b = intval($b);
        $r = dechex($r < 0 ? 0 : ($r > 255 ? 255 : $r));
        $g = dechex($g < 0 ? 0 : ($g > 255 ? 255 : $g));
        $b = dechex($b < 0 ? 0 : ($b > 255 ? 255 : $b));
        $Color = "#" . (strlen($r) < 2 ? '0' : '') . $r;
        $Color .= (strlen($g) < 2 ? '0' : '') . $g;
        $Color .= (strlen($b) < 2 ? '0' : '') . $b;
        return $Color;
    }

    /**
     * Reverse an array of points
     * @param array $Plots
     * @return array
     */
    public function reversePlots(array $Plots) {
        $result = [];
        for ($i = count($Plots) - 2; $i >= 0; $i = $i - 2) {
            $result[] = $Plots[$i];
            $result[] = $Plots[$i + 1];
        }
        return $result;
    }

    /**
     * Mirror Effect
     * @param int $x
     * @param int $y
     * @param int $Width
     * @param int $height
     * @param array $format
     */
    public function drawAreaMirror($x, $y, $Width, $height, array $format = []) {
        $startAlpha = isset($format["startAlpha"]) ? $format["startAlpha"] : 80;
        $endAlpha = isset($format["endAlpha"]) ? $format["endAlpha"] : 0;
        $alphaStep = ($startAlpha - $endAlpha) / $height;
        $Picture = imagecreatetruecolor($this->xSize, $this->ySize);
        imagecopy($Picture, $this->picture, 0, 0, 0, 0, $this->xSize, $this->ySize);
        for ($i = 1; $i <= $height; $i++) {
            if ($y + ($i - 1) < $this->ySize && $y - $i > 0) {
                imagecopymerge(
                        $Picture, $this->picture, $x, $y + ($i - 1), $x, $y - $i, $Width, 1, $startAlpha - $alphaStep * $i
                );
            }
        }
        imagecopy($this->picture, $Picture, 0, 0, 0, 0, $this->xSize, $this->ySize);
    }

    /**
     * Open a handle to image storage file.
     * @param string $mode
     * @return resource
     */
    private function openFileHandle($mode = "r") {
        return @fopen(
                        sprintf("%s/%s.map", $this->imageMapStorageFolder, $this->imageMapFileName), $mode
        );
    }

}

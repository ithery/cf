<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 30, 2019, 2:17:49 AM
 */
use CImage_Chart_Data as Data;
use CImage_Chart_Constant as Constant;

class CImage_Chart_Image extends CImage_Chart_Draw {
    protected $imageMapStorageFolder;

    protected $imageMapFileName;

    /**
     * @param int  $xSize
     * @param int  $ySize
     * @param Data $dataSet
     * @param bool $transparentBackground
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
                $this->picture,
                0,
                0,
                $xSize,
                $ySize,
                imagecolorallocatealpha($this->picture, 255, 255, 255, 127)
            );
            imagealphablending($this->picture, true);
            imagesavealpha($this->picture, true);
        } else {
            $C_White = $this->allocateColor($this->picture, 255, 255, 255);
            imagefilledrectangle($this->picture, 0, 0, $xSize, $ySize, $C_White);
        }
    }

    /**
     * Enable / Disable and set shadow properties.
     *
     * @param bool  $enabled
     * @param array $format
     */
    public function setShadow($enabled = true, array $format = []) {
        $x = isset($format['x']) ? $format['x'] : 2;
        $y = isset($format['y']) ? $format['y'] : 2;
        $r = isset($format['r']) ? $format['r'] : 0;
        $g = isset($format['g']) ? $format['g'] : 0;
        $b = isset($format['b']) ? $format['b'] : 0;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 10;
        $this->shadow = $enabled;
        $this->shadowX = $x;
        $this->shadowY = $y;
        $this->shadowR = $r;
        $this->shadowG = $g;
        $this->shadowB = $b;
        $this->shadowA = $alpha;
    }

    /**
     * Set the graph area position.
     *
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     *
     * @return null|int
     */
    public function setGraphArea($x1, $y1, $x2, $y2) {
        if ($x2 < $x1 || $x1 == $x2 || $y2 < $y1 || $y1 == $y2) {
            return -1;
        }
        $this->graphAreaX1 = $x1;
        $this->dataSet->data['graphArea']['x1'] = $x1;
        $this->graphAreaY1 = $y1;
        $this->dataSet->data['graphArea']['y1'] = $y1;
        $this->graphAreaX2 = $x2;
        $this->dataSet->data['graphArea']['x2'] = $x2;
        $this->graphAreaY2 = $y2;
        $this->dataSet->data['graphArea']['y2'] = $y2;
    }

    /**
     * Return the width of the picture.
     *
     * @return int
     */
    public function getWidth() {
        return $this->xSize;
    }

    /**
     * Return the heigth of the picture.
     *
     * @return int
     */
    public function getHeight() {
        return $this->ySize;
    }

    /**
     * Render the picture to a file.
     *
     * @param string $fileName
     */
    public function render($fileName) {
        if ($this->transparentBackground) {
            imagealphablending($this->picture, false);
            imagesavealpha($this->picture, true);
        }
        imagepng($this->picture, $fileName);
    }

    public function toData() {
        if ($this->transparentBackground) {
            imagealphablending($this->picture, false);
            imagesavealpha($this->picture, true);
        }
        ob_start();
        imagepng($this->picture);

        return ob_get_clean();
    }
    public function __toString() {
        return $this->toData();
    }

    public function toDataURI() {
        return 'data:image/png;base64,' . base64_encode($this->__toString());
    }

    /**
     * Render the picture to a web browser stream.
     *
     * @param bool $browserExpire
     */
    public function stroke($browserExpire = false) {
        if ($this->transparentBackground) {
            imagealphablending($this->picture, false);
            imagesavealpha($this->picture, true);
        }
        if ($browserExpire) {
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Cache-Control: no-cache');
            header('Pragma: no-cache');
        }
        header('Content-type: image/png');
        imagepng($this->picture);
    }

    /**
     * Automatic output method based on the calling interface.
     *
     * @param string $fileName
     */
    public function autoOutput($fileName = 'output.png') {
        if (php_sapi_name() == 'cli') {
            $this->render($fileName);
        } else {
            $this->stroke();
        }
    }

    /**
     * Attach a dataset to your pChart Object.
     *
     * @param Data $dataSet
     */
    public function setDataSet(Data $dataSet) {
        $this->dataSet = $dataSet;
    }

    /**
     * Print attached dataset contents to STDOUT.
     */
    public function printDataSet() {
        print_r($this->dataSet);
    }

    /**
     * Remove VOID values from an imagemap custom values array.
     *
     * @param string $serieName
     * @param array  $values
     *
     * @return array
     */
    public function removeVOIDFromArray($serieName, array $values) {
        if (!isset($this->dataSet->data['series'][$serieName])) {
            return -1;
        }
        $result = [];
        foreach ($this->dataSet->data['series'][$serieName]['data'] as $key => $value) {
            if ($value != Constant::VOID && isset($values[$key])) {
                $result[] = $values[$key];
            }
        }

        return $result;
    }

    /**
     * Replace the title of one image map serie.
     *
     * @param string       $oldTitle
     * @param string|array $newTitle
     *
     * @return null|int
     */
    public function replaceImageMapTitle($oldTitle, $newTitle) {
        if ($this->imageMapStorageMode == null) {
            return -1;
        }
        if (is_array($newTitle)) {
            $newTitle = $this->removeVOIDFromArray($oldTitle, $newTitle);
        }
        if ($this->imageMapStorageMode == Constant::IMAGE_MAP_STORAGE_SESSION) {
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
        } elseif ($this->imageMapStorageMode == Constant::IMAGE_MAP_STORAGE_FILE) {
            $tempArray = [];
            $handle = $this->openFileHandle();
            if ($handle) {
                while (($buffer = fgets($handle, 4096)) !== false) {
                    $fields = preg_split(
                        sprintf('/%s/', Constant::imageMapDelimiter()),
                        str_replace([chr(10), chr(13)], '', $buffer)
                    );
                    $tempArray[] = [$fields[0], $fields[1], $fields[2], $fields[3], $fields[4]];
                }
                fclose($handle);
                if (is_array($newTitle)) {
                    $ID = 0;
                    foreach ($tempArray as $key => $settings) {
                        if ($settings[3] == $oldTitle && isset($newTitle[$ID])) {
                            $tempArray[$key][3] = $newTitle[$ID];
                            $ID++;
                        }
                    }
                } else {
                    foreach ($tempArray as $key => $settings) {
                        if ($settings[3] == $oldTitle) {
                            $tempArray[$key][3] = $newTitle;
                        }
                    }
                }
                $handle = $this->openFileHandle('w');
                foreach ($tempArray as $key => $settings) {
                    fwrite(
                        $handle,
                        sprintf(
                            "%s%s%s%s%s%s%s%s%s\r\n",
                            $settings[0],
                            Constant::imageMapDelimiter(),
                            $settings[1],
                            Constant::imageMapDelimiter(),
                            $settings[2],
                            Constant::imageMapDelimiter(),
                            $settings[3],
                            Constant::imageMapDelimiter(),
                            $settings[4]
                        )
                    );
                }
                fclose($handle);
            }
        }
    }

    /**
     * Replace the values of the image map contents.
     *
     * @param string $Title
     * @param array  $values
     *
     * @return null|int
     */
    public function replaceImageMapValues($Title, array $values) {
        if ($this->imageMapStorageMode == null) {
            return -1;
        }
        $values = $this->removeVOIDFromArray($Title, $values);
        $ID = 0;
        if ($this->imageMapStorageMode == Constant::IMAGE_MAP_STORAGE_SESSION) {
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
        } elseif ($this->imageMapStorageMode == Constant::IMAGE_MAP_STORAGE_FILE) {
            $tempArray = [];
            $handle = $this->openFileHandle();
            if ($handle) {
                while (($buffer = fgets($handle, 4096)) !== false) {
                    $fields = preg_split(
                        '/' . Constant::imageMapDelimiter() . '/',
                        str_replace([chr(10), chr(13)], '', $buffer)
                    );
                    $tempArray[] = [$fields[0], $fields[1], $fields[2], $fields[3], $fields[4]];
                }
                fclose($handle);
                foreach ($tempArray as $key => $settings) {
                    if ($settings[3] == $Title) {
                        if (isset($values[$ID])) {
                            $tempArray[$key][4] = $values[$ID];
                        }
                        $ID++;
                    }
                }
                $handle = $this->openFileHandle('w');
                foreach ($tempArray as $key => $settings) {
                    fwrite(
                        $handle,
                        sprintf(
                            "%s%s%s%s%s%s%s%s%s\r\n",
                            $settings[0],
                            Constant::imageMapDelimiter(),
                            $settings[1],
                            Constant::imageMapDelimiter(),
                            $settings[2],
                            Constant::imageMapDelimiter(),
                            $settings[3],
                            Constant::imageMapDelimiter(),
                            $settings[4]
                        )
                    );
                }
                fclose($handle);
            }
        }
    }

    /**
     * Dump the image map.
     *
     * @param string $name
     * @param int    $storageMode
     * @param string $UniqueID
     * @param string $storageFolder
     */
    public function dumpImageMap(
        $name = 'pChart',
        $storageMode = Constant::IMAGE_MAP_STORAGE_SESSION,
        $UniqueID = 'imageMap',
        $storageFolder = 'tmp'
    ) {
        $this->imageMapIndex = $name;
        $this->imageMapStorageMode = $storageMode;
        if ($this->imageMapStorageMode == Constant::IMAGE_MAP_STORAGE_SESSION) {
            if (!isset($_SESSION)) {
                session_start();
            }
            if ($_SESSION[$name] != null) {
                foreach ($_SESSION[$name] as $key => $Params) {
                    echo $Params[0] . Constant::imageMapDelimiter() . $Params[1]
                    . Constant::imageMapDelimiter() . $Params[2] . Constant::imageMapDelimiter()
                    . $Params[3] . Constant::imageMapDelimiter() . $Params[4] . "\r\n";
                }
            }
        } elseif ($this->imageMapStorageMode == Constant::IMAGE_MAP_STORAGE_FILE) {
            if (file_exists($storageFolder . '/' . $UniqueID . '.map')) {
                $handle = @fopen($storageFolder . '/' . $UniqueID . '.map', 'r');
                if ($handle) {
                    while (($buffer = fgets($handle, 4096)) !== false) {
                        echo $buffer;
                    }
                }
                fclose($handle);
                if ($this->imageMapAutoDelete) {
                    unlink($storageFolder . '/' . $UniqueID . '.map');
                }
            }
        }
        /* When the image map is returned to the client, the script ends */
        exit();
    }

    /**
     * Mirror Effect.
     *
     * @param int   $x
     * @param int   $y
     * @param int   $Width
     * @param int   $height
     * @param array $format
     */
    public function drawAreaMirror($x, $y, $Width, $height, array $format = []) {
        $startAlpha = isset($format['startAlpha']) ? $format['startAlpha'] : 80;
        $endAlpha = isset($format['endAlpha']) ? $format['endAlpha'] : 0;
        $alphaStep = ($startAlpha - $endAlpha) / $height;
        $Picture = imagecreatetruecolor($this->xSize, $this->ySize);
        imagecopy($Picture, $this->picture, 0, 0, 0, 0, $this->xSize, $this->ySize);
        for ($i = 1; $i <= $height; $i++) {
            if ($y + ($i - 1) < $this->ySize && $y - $i > 0) {
                imagecopymerge(
                    $Picture,
                    $this->picture,
                    $x,
                    $y + ($i - 1),
                    $x,
                    $y - $i,
                    $Width,
                    1,
                    $startAlpha - $alphaStep * $i
                );
            }
        }
        imagecopy($this->picture, $Picture, 0, 0, 0, 0, $this->xSize, $this->ySize);
    }

    /**
     * Open a handle to image storage file.
     *
     * @param string $mode
     *
     * @return resource
     */
    private function openFileHandle($mode = 'r') {
        return @fopen(
            sprintf('%s/%s.map', $this->imageMapStorageFolder, $this->imageMapFileName),
            $mode
        );
    }
}

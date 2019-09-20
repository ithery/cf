<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 30, 2019, 2:57:57 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CImage_Chart_Constant as Constant;

class CImage_Chart_BaseDraw {

    /**
     * Width of the picture
     * @var int
     */
    public $xSize;

    /**
     * Height of the picture
     * @var int
     */
    public $ySize;

    /**
     * GD picture object
     * @var resource
     */
    public $picture;

    /**
     * Turn antialias on or off
     * @var boolean
     */
    public $antialias = true;

    /**
     * Quality of the antialiasing implementation (0-1)
     * @var int
     */
    public $antialiasQuality = 0;

    /**
     * Already drawn pixels mask (Filled circle implementation)
     * @var array
     */
    public $mask = [];

    /**
     * Just to know if we need to flush the alpha channels when rendering
     * @var boolean
     */
    public $transparentBackground = false;

    /**
     * Graph area X origin
     * @var int
     */
    public $graphAreaX1;

    /**
     * Graph area Y origin
     * @var int
     */
    public $graphAreaY1;

    /**
     * Graph area bottom right X position
     * @var int
     */
    public $graphAreaX2;

    /**
     * Graph area bottom right Y position
     * @var int
     */
    public $graphAreaY2;

    /**
     * Minimum height for scale divs
     * @var int
     */
    public $scaleMinDivHeight = 20;

    /**
     * @var string
     */
    public $fontName = "GeosansLight.ttf";

    /**
     * @var int
     */
    public $fontSize = 12;

    /**
     * Return the bounding box of the last written string
     * @var array
     */
    public $fontBox;

    /**
     * @var int
     */
    public $fontColorR = 0;

    /**
     * @var int
     */
    public $fontColorG = 0;

    /**
     * @var int
     */
    public $fontColorB = 0;

    /**
     * @var int
     */
    public $fontColorA = 100;

    /**
     * Turn shadows on or off
     * @var boolean
     */
    public $shadow = false;

    /**
     * X Offset of the shadow
     * @var int
     */
    public $shadowX;

    /**
     * Y Offset of the shadow
     * @var int
     */
    public $shadowY;

    /**
     * R component of the shadow
     * @var int
     */
    public $shadowR;

    /**
     * G component of the shadow
     * @var int
     */
    public $shadowG;

    /**
     * B component of the shadow
     * @var int
     */
    public $shadowB;

    /**
     * Alpha level of the shadow
     * @var int
     */
    public $shadowA;

    /**
     * Array containing the image map
     * @var array
     */
    public $imageMap = [];

    /**
     * Name of the session array
     * @var int
     */
    public $imageMapIndex = "pChart";

    /**
     * Save the current imagemap storage mode
     * @var int
     */
    public $imageMapStorageMode;

    /**
     * Automatic deletion of the image map temp files
     * @var boolean
     */
    public $imageMapAutoDelete = true;

    /**
     * Attached dataset
     * @var Data
     */
    public $dataSet;

    /**
     * Last generated chart info
     * Last layout : regular or stacked
     * @var int
     */
    public $lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;

    /**
     * @var string
     */
    private $resourcePath;

    public function __construct() {
        $this->resourcePath = DOCROOT . 'modules/cresenity/media';
        $this->fontName = $this->loadFont($this->fontName, 'font');
    }

    /**
     * Set the path to the folder containing library resources (fonts, data, palettes).
     *
     * @param string $path
     * @throws Exception
     */
    public function setResourcePath($path) {
        $escapedPath = rtrim($path, '/');
        if (!file_exists($escapedPath)) {
            throw new Exception(sprintf(
                    "The path '%s' to resources' folder does not exist!", $escapedPath
            ));
        }
        $this->resourcePath = $escapedPath;
    }

    /**
     * Check if requested resource exists and return the path to it if yes.
     * @param string $name
     * @param string $type
     * @return string
     * @throws Exception
     */
    protected function loadFont($name, $type) {
        if (file_exists($name)) {
            return $name;
        }
        $path = sprintf('%s/%s/%s', $this->resourcePath, $type, $name);
        if (file_exists($path)) {
            return $path;
        }
        throw new Exception(
        sprintf('The requested resource %s (%s) has not been found!', $name, $type)
        );
    }

    /**
     * Allocate a color with transparency
     * @param resource $picture
     * @param int $r
     * @param int $g
     * @param int $b
     * @param int $alpha
     * @return int
     */
    public function allocateColor($picture, $r, $g, $b, $alpha = 100) {
        if ($r < 0) {
            $r = 0;
        } if ($r > 255) {
            $r = 255;
        }
        if ($g < 0) {
            $g = 0;
        } if ($g > 255) {
            $g = 255;
        }
        if ($b < 0) {
            $b = 0;
        } if ($b > 255) {
            $b = 255;
        }
        if ($alpha < 0) {
            $alpha = 0;
        }
        if ($alpha > 100) {
            $alpha = 100;
        }
        $alpha = $this->convertAlpha($alpha);
        return imagecolorallocatealpha($picture, $r, $g, $b, $alpha);
    }

    /**
     * Convert apha to base 10
     * @param int|float $alphaValue
     * @return integer
     */
    public function convertAlpha($alphaValue) {
        return (127 / 100) * (100 - $alphaValue);
    }

    /**
     * @param string $fileName
     * @return array
     */
    public function getPicInfo($fileName) {
        $infos = getimagesize($fileName);
        $width = $infos[0];
        $height = $infos[1];
        $type = $infos["mime"];
        if ($type == "image/png") {
            $type = 1;
        }
        if ($type == "image/gif") {
            $type = 2;
        }
        if ($type == "image/jpeg ") {
            $type = 3;
        }
        return [$width, $height, $type];
    }

    /**
     * Compute the scale, check for the best visual factors
     * @param int $xMin
     * @param int $xMax
     * @param int $maxDivs
     * @param array $factors
     * @param int $axisID
     * @return mixed
     */
    public function computeScale($xMin, $xMax, $maxDivs, array $factors, $axisID = 0) {
        /* Compute each factors */
        $results = [];
        foreach ($factors as $key => $factor) {
            $results[$factor] = $this->processScale($xMin, $xMax, $maxDivs, [$factor], $axisID);
        }
        /* Remove scales that are creating to much decimals */
        $goodScaleFactors = [];
        foreach ($results as $key => $result) {
            $decimals = preg_split("/\./", $result["rowHeight"]);
            if ((!isset($decimals[1])) || (strlen($decimals[1]) < 6)) {
                $goodScaleFactors[] = $key;
            }
        }
        /* Found no correct scale, shame,... returns the 1st one as default */
        if (!count($goodScaleFactors)) {
            return $results[$factors[0]];
        }
        /* Find the factor that cause the maximum number of Rows */
        $maxRows = 0;
        $bestFactor = 0;
        foreach ($goodScaleFactors as $key => $factor) {
            if ($results[$factor]["rows"] > $maxRows) {
                $maxRows = $results[$factor]["rows"];
                $bestFactor = $factor;
            }
        }
        /* Return the best visual scale */
        return $results[$bestFactor];
    }

    /**
     * Compute the best matching scale based on size & factors
     * @param int $xMin
     * @param int $xMax
     * @param int $maxDivs
     * @param array $factors
     * @param int $axisID
     * @return array
     */
    public function processScale($xMin, $xMax, $maxDivs, array $factors, $axisID) {
        $scaleHeight = abs(ceil($xMax) - floor($xMin));
        $format = null;
        if (isset($this->dataSet->data["axis"][$axisID]["format"])) {
            $format = $this->dataSet->data["axis"][$axisID]["format"];
        }
        $mode = Constant::AXIS_FORMAT_DEFAULT;
        if (isset($this->dataSet->data["axis"][$axisID]["display"])) {
            $mode = $this->dataSet->data["axis"][$axisID]["display"];
        }
        $scale = [];
        if ($xMin != $xMax) {
            $found = false;
            $rescaled = false;
            $scaled10Factor = .0001;
            $result = 0;
            while (!$found) {
                foreach ($factors as $key => $factor) {
                    if (!$found) {
                        $xMinRescaled = $xMin;
                        if (!($this->modulo($xMin, $factor * $scaled10Factor) == 0) || ($xMin != floor($xMin))
                        ) {
                            $xMinRescaled = floor($xMin / ($factor * $scaled10Factor)) * $factor * $scaled10Factor
                            ;
                        }
                        $xMaxRescaled = $xMax;
                        if (!($this->modulo($xMax, $factor * $scaled10Factor) == 0) || ($xMax != floor($xMax))
                        ) {
                            $xMaxRescaled = floor($xMax / ($factor * $scaled10Factor)) * $factor * $scaled10Factor + ($factor * $scaled10Factor)
                            ;
                        }
                        $scaleHeightRescaled = abs($xMaxRescaled - $xMinRescaled);
                        if (!$found && floor($scaleHeightRescaled / ($factor * $scaled10Factor)) <= $maxDivs
                        ) {
                            $found = true;
                            $rescaled = true;
                            $result = $factor * $scaled10Factor;
                        }
                    }
                }
                $scaled10Factor = $scaled10Factor * 10;
            }
            /* ReCall Min / Max / Height */
            if ($rescaled) {
                $xMin = $xMinRescaled;
                $xMax = $xMaxRescaled;
                $scaleHeight = $scaleHeightRescaled;
            }
            /* Compute rows size */
            $rows = floor($scaleHeight / $result);
            if ($rows == 0) {
                $rows = 1;
            }
            $rowHeight = $scaleHeight / $rows;
            /* Return the results */
            $scale["rows"] = $rows;
            $scale["rowHeight"] = $rowHeight;
            $scale["xMin"] = $xMin;
            $scale["xMax"] = $xMax;
            /* Compute the needed decimals for the metric view to avoid repetition of the same X Axis labels */
            if ($mode == Constant::AXIS_FORMAT_METRIC && $format == null) {
                $done = false;
                $goodDecimals = 0;
                for ($decimals = 0; $decimals <= 10; $decimals++) {
                    if (!$done) {
                        $lastLabel = "zob";
                        $scaleOK = true;
                        for ($i = 0; $i <= $rows; $i++) {
                            $value = $xMin + $i * $rowHeight;
                            $label = $this->scaleFormat($value, Constant::AXIS_FORMAT_METRIC, $decimals);
                            if ($lastLabel == $label) {
                                $scaleOK = false;
                            }
                            $lastLabel = $label;
                        }
                        if ($scaleOK) {
                            $done = true;
                            $goodDecimals = $decimals;
                        }
                    }
                }
                $scale["format"] = $goodDecimals;
            }
        } else {
            /* If all values are the same we keep a +1/-1 scale */
            $rows = 2;
            $xMin = $xMax - 1;
            $xMax = $xMax + 1;
            $rowHeight = 1;
            /* Return the results */
            $scale["rows"] = $rows;
            $scale["rowHeight"] = $rowHeight;
            $scale["xMin"] = $xMin;
            $scale["xMax"] = $xMax;
        }
        return $scale;
    }

    /**
     *
     * @param int|float $value1
     * @param int|float $value2
     * @return double
     */
    public function modulo($value1, $value2) {
        if (floor($value2) == 0) {
            return 0;
        }
        if (floor($value2) != 0) {
            return $value1 % $value2;
        }
        $minValue = min($value1, $value2);
        $factor = 10;
        while (floor($minValue * $factor) == 0) {
            $factor = $factor * 10;
        }
        return ($value1 * $factor) % ($value2 * $factor);
    }

    /**
     * @param mixed $value
     * @param mixed $lastValue
     * @param integer $labelingMethod
     * @param integer $iD
     * @param boolean $labelSkip
     * @return boolean
     */
    public function isValidLabel($value, $lastValue, $labelingMethod, $iD, $labelSkip) {
        if ($labelingMethod == Constant::LABELING_DIFFERENT && $value != $lastValue) {
            return true;
        }
        if ($labelingMethod == Constant::LABELING_DIFFERENT && $value == $lastValue) {
            return false;
        }
        if ($labelingMethod == Constant::LABELING_ALL && $labelSkip == 0) {
            return true;
        }
        if ($labelingMethod == Constant::LABELING_ALL && ($iD + $labelSkip) % ($labelSkip + 1) != 1) {
            return false;
        }
        return true;
    }

    /**
     * Returns the number of drawable series
     * @return int
     */
    public function countDrawableSeries() {
        $count = 0;
        $data = $this->dataSet->getData();
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["abscissa"]) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Fix box coordinates
     * @param int $xa
     * @param int $ya
     * @param int $xb
     * @param int $yb
     * @return integer[]
     */
    public function fixBoxCoordinates($xa, $ya, $xb, $yb) {
        return [min($xa, $xb), min($ya, $yb), max($xa, $xb), max($ya, $yb)];
    }

    /**
     * Apply AALias correction to the rounded box boundaries
     * @param int|float $value
     * @param int $mode
     * @return int|float
     */
    public function offsetCorrection($value, $mode) {
        $value = round($value, 1);
        if ($value == 0 && $mode != 1) {
            return 0;
        }
        if ($mode == 1) {
            if ($value == .5) {
                return .5;
            }
            if ($value == .8) {
                return .6;
            }
            if (in_array($value, [.4, .7])) {
                return .7;
            }
            if (in_array($value, [.2, .3, .6])) {
                return .8;
            }
            if (in_array($value, [0, 1, .1, .9])) {
                return .9;
            }
        }
        if ($mode == 2) {
            if ($value == .1) {
                return .1;
            }
            if ($value == .2) {
                return .2;
            }
            if ($value == .3) {
                return .3;
            }
            if ($value == .4) {
                return .4;
            }
            if ($value == .5) {
                return .5;
            }
            if ($value == .7) {
                return .7;
            }
            if (in_array($value, [.6, .8])) {
                return .8;
            }
            if (in_array($value, [1, .9])) {
                return .9;
            }
        }
        if ($mode == 3) {
            if (in_array($value, [1, .1])) {
                return .1;
            }
            if ($value == .2) {
                return .2;
            }
            if ($value == .3) {
                return .3;
            }
            if (in_array($value, [.4, .8])) {
                return .4;
            }
            if ($value == .5) {
                return .9;
            }
            if ($value == .6) {
                return .6;
            }
            if ($value == .7) {
                return .7;
            }
            if ($value == .9) {
                return .5;
            }
        }
        if ($mode == 4) {
            if ($value == 1) {
                return -1;
            }
            if (in_array($value, [.1, .4, .7, .8, .9])) {
                return .1;
            }
            if ($value == .2) {
                return .2;
            }
            if ($value == .3) {
                return .3;
            }
            if ($value == .5) {
                return -.1;
            }
            if ($value == .6) {
                return .8;
            }
        }
    }

    /**
     * Get the legend box size
     * @param array $format
     * @return array
     */
    public function getLegendSize(array $format = []) {
        $fontName = isset($format["fontName"]) ? $this->loadFont($format["fontName"], 'fonts') : $this->FontName;
        $fontSize = isset($format["fontSize"]) ? $format["fontSize"] : $this->FontSize;
        $margin = isset($format["margin"]) ? $format["margin"] : 5;
        $mode = isset($format["mode"]) ? $format["mode"] : LEGEND_VERTICAL;
        $boxWidth = isset($format["boxWidth"]) ? $format["boxWidth"] : 5;
        $boxHeight = isset($format["boxHeight"]) ? $format["boxHeight"] : 5;
        $iconAreaWidth = isset($format["iconAreaWidth"]) ? $format["iconAreaWidth"] : $boxWidth;
        $iconAreaHeight = isset($format["iconAreaHeight"]) ? $format["iconAreaHeight"] : $boxHeight;
        $xSpacing = isset($format["xSpacing"]) ? $format["xSpacing"] : 5;
        $data = $this->dataSet->getData();
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["abscissa"] && isset($serie["picture"])
            ) {
                list($picWidth, $picHeight) = $this->getPicInfo($serie["picture"]);
                if ($iconAreaWidth < $picWidth) {
                    $iconAreaWidth = $picWidth;
                }
                if ($iconAreaHeight < $picHeight) {
                    $iconAreaHeight = $picHeight;
                }
            }
        }
        $yStep = max($this->FontSize, $iconAreaHeight) + 5;
        $xStep = $iconAreaWidth + 5;
        $xStep = $xSpacing;
        $x = 100;
        $y = 100;
        $boundaries = [];
        $boundaries["l"] = $x;
        $boundaries["t"] = $y;
        $boundaries["r"] = 0;
        $boundaries["b"] = 0;
        $vY = $y;
        $vX = $x;
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["abscissa"]) {
                if ($mode == LEGEND_VERTICAL) {
                    $boxArray = $this->getTextBox(
                            $vX + $iconAreaWidth + 4, $vY + $iconAreaHeight / 2, $fontName, $fontSize, 0, $serie["description"]
                    );
                    if ($boundaries["t"] > $boxArray[2]["y"] + $iconAreaHeight / 2) {
                        $boundaries["t"] = $boxArray[2]["y"] + $iconAreaHeight / 2;
                    }
                    if ($boundaries["r"] < $boxArray[1]["x"] + 2) {
                        $boundaries["r"] = $boxArray[1]["x"] + 2;
                    }
                    if ($boundaries["b"] < $boxArray[1]["y"] + 2 + $iconAreaHeight / 2) {
                        $boundaries["b"] = $boxArray[1]["y"] + 2 + $iconAreaHeight / 2;
                    }
                    $lines = preg_split("/\n/", $serie["description"]);
                    $vY = $vY + max($this->FontSize * count($lines), $iconAreaHeight) + 5;
                } elseif ($mode == Constant::LEGEND_HORIZONTAL) {
                    $lines = preg_split("/\n/", $serie["description"]);
                    $width = [];
                    foreach ($lines as $key => $value) {
                        $boxArray = $this->getTextBox(
                                $vX + $iconAreaWidth + 6, $y + $iconAreaHeight / 2 + (($this->FontSize + 3) * $key), $fontName, $fontSize, 0, $value
                        );
                        if ($boundaries["t"] > $boxArray[2]["y"] + $iconAreaHeight / 2) {
                            $boundaries["t"] = $boxArray[2]["y"] + $iconAreaHeight / 2;
                        }
                        if ($boundaries["r"] < $boxArray[1]["x"] + 2) {
                            $boundaries["r"] = $boxArray[1]["x"] + 2;
                        }
                        if ($boundaries["b"] < $boxArray[1]["y"] + 2 + $iconAreaHeight / 2) {
                            $boundaries["b"] = $boxArray[1]["y"] + 2 + $iconAreaHeight / 2;
                        }
                        $width[] = $boxArray[1]["x"];
                    }
                    $vX = max($width) + $xStep;
                }
            }
        }
        $vY = $vY - $yStep;
        $vX = $vX - $xStep;
        $topOffset = $y - $boundaries["t"];
        if ($boundaries["b"] - ($vY + $iconAreaHeight) < $topOffset) {
            $boundaries["b"] = $vY + $iconAreaHeight + $topOffset;
        }
        $width = ($boundaries["r"] + $margin) - ($boundaries["l"] - $margin);
        $height = ($boundaries["b"] + $margin) - ($boundaries["t"] - $margin);
        return ["Width" => $width, "Height" => $height];
    }

    /**
     * Return the abscissa margin
     * @param array $data
     * @return int
     */
    public function getAbscissaMargin(array $data) {
        foreach ($data["axis"] as $values) {
            if ($values["identity"] == AXIS_X) {
                return $values["margin"];
            }
        }
        return 0;
    }

    /**
     * Returns a random color
     * @param int $alpha
     * @return array
     */
    public function getRandomColor($alpha = 100) {
        return [
            "r" => rand(0, 255),
            "g" => rand(0, 255),
            "b" => rand(0, 255),
            "alpha" => $alpha
        ];
    }

    /**
     * Validate a palette
     * @param mixed $Colors
     * @param int|float $surrounding
     * @return array
     */
    public function validatePalette($Colors, $surrounding = null) {
        $result = [];
        if (!is_array($Colors)) {
            return $this->getRandomColor();
        }
        foreach ($Colors as $key => $values) {
            if (isset($values["r"])) {
                $result[$key]["r"] = $values["r"];
            } else {
                $result[$key]["r"] = rand(0, 255);
            }
            if (isset($values["g"])) {
                $result[$key]["g"] = $values["g"];
            } else {
                $result[$key]["g"] = rand(0, 255);
            }
            if (isset($values["b"])) {
                $result[$key]["b"] = $values["b"];
            } else {
                $result[$key]["b"] = rand(0, 255);
            }
            if (isset($values["alpha"])) {
                $result[$key]["alpha"] = $values["alpha"];
            } else {
                $result[$key]["alpha"] = 100;
            }
            if (null !== $surrounding) {
                $result[$key]["borderR"] = $result[$key]["r"] + $surrounding;
                $result[$key]["borderG"] = $result[$key]["g"] + $surrounding;
                $result[$key]["borderB"] = $result[$key]["b"] + $surrounding;
            } else {
                if (isset($values["borderR"])) {
                    $result[$key]["borderR"] = $values["borderR"];
                } else {
                    $result[$key]["borderR"] = $result[$key]["r"];
                }
                if (isset($values["borderG"])) {
                    $result[$key]["borderG"] = $values["borderG"];
                } else {
                    $result[$key]["borderG"] = $result[$key]["g"];
                }
                if (isset($values["borderB"])) {
                    $result[$key]["borderB"] = $values["borderB"];
                } else {
                    $result[$key]["borderB"] = $result[$key]["b"];
                }
                if (isset($values["borderAlpha"])) {
                    $result[$key]["borderAlpha"] = $values["borderAlpha"];
                } else {
                    $result[$key]["borderAlpha"] = $result[$key]["alpha"];
                }
            }
        }
        return $result;
    }

    /**
     * @param mixed $values
     * @param array $option
     * @param boolean $returnOnly0Height
     * @return int|float|array
     */
    public function scaleComputeY($values, array $option = [], $returnOnly0Height = false) {
        $axisID = isset($option["axisID"]) ? $option["axisID"] : 0;
        $serieName = isset($option["serieName"]) ? $option["serieName"] : null;
        $data = $this->dataSet->getData();
        if (!isset($data["axis"][$axisID])) {
            return -1;
        }
        if ($serieName != null) {
            $axisID = $data["series"][$serieName]["axis"];
        }
        if (!is_array($values)) {
            $tmp = $values;
            $values = [];
            $values[0] = $tmp;
        }
        $result = [];
        if ($data["orientation"] == Constant::SCALE_POS_LEFTRIGHT) {
            $height = ($this->graphAreaY2 - $this->graphAreaY1) - $data["axis"][$axisID]["margin"] * 2;
            $scaleHeight = $data["axis"][$axisID]["scaleMax"] - $data["axis"][$axisID]["scaleMin"];
            $step = $height / $scaleHeight;
            if ($returnOnly0Height) {
                foreach ($values as $key => $value) {
                    if ($value == Constant::VOID) {
                        $result[] = Constant::VOID;
                    } else {
                        $result[] = $step * $value;
                    }
                }
            } else {
                foreach ($values as $key => $value) {
                    if ($value == Constant::VOID) {
                        $result[] = Constant::VOID;
                    } else {
                        $result[] = $this->graphAreaY2 - $data["axis"][$axisID]["margin"] - ($step * ($value - $data["axis"][$axisID]["scaleMin"]))
                        ;
                    }
                }
            }
        } else {
            $width = ($this->graphAreaX2 - $this->graphAreaX1) - $data["axis"][$axisID]["margin"] * 2;
            $scaleWidth = $data["axis"][$axisID]["scaleMax"] - $data["axis"][$axisID]["scaleMin"];
            $step = $width / $scaleWidth;
            if ($returnOnly0Height) {
                foreach ($values as $key => $value) {
                    if ($value == Constant::VOID) {
                        $result[] = Constant::VOID;
                    } else {
                        $result[] = $step * $value;
                    }
                }
            } else {
                foreach ($values as $key => $value) {
                    if ($value == Constant::VOID) {
                        $result[] = Constant::VOID;
                    } else {
                        $result[] = $this->graphAreaX1 + $data["axis"][$axisID]["margin"] + ($step * ($value - $data["axis"][$axisID]["scaleMin"]))
                        ;
                    }
                }
            }
        }
        return count($result) == 1 ? reset($result) : $result;
    }

    /**
     * Format the axis values
     * @param mixed $value
     * @param int $mode
     * @param array $format
     * @param string $unit
     * @return string
     */
    public function scaleFormat($value, $mode = null, $format = null, $unit = null) {
        if ($value == Constant::VOID) {
            return "";
        }
        if ($mode == Constant::AXIS_FORMAT_TRAFFIC) {
            if ($value == 0) {
                return "0B";
            }
            $units = ["b", "KB", "MB", "GB", "TB", "PB"];
            $sign = "";
            if ($value < 0) {
                $value = abs($value);
                $sign = "-";
            }
            $value = number_format($value / pow(1024, ($scale = floor(log($value, 1024)))), 2, ",", ".");
            return $sign . $value . " " . $units[$scale];
        }
        if ($mode == Constant::AXIS_FORMAT_CUSTOM) {
            if (is_callable($format)) {
                return call_user_func($format, $value);
            }
        }
        if ($mode == Constant::AXIS_FORMAT_DATE) {
            $pattern = "d/m/Y";
            if ($format !== null) {
                $pattern = $format;
            }
            return gmdate($pattern, $value);
        }
        if ($mode == Constant::AXIS_FORMAT_TIME) {
            $pattern = "H:i:s";
            if ($format !== null) {
                $pattern = $format;
            }
            return gmdate($pattern, $value);
        }
        if ($mode == Constant::AXIS_FORMAT_CURRENCY) {
            return $format . number_format($value, 2);
        }
        if ($mode == Constant::AXIS_FORMAT_METRIC) {
            if (abs($value) > 1000000000) {
                return round($value / 1000000000, $format) . "g" . $unit;
            }
            if (abs($value) > 1000000) {
                return round($value / 1000000, $format) . "m" . $unit;
            } elseif (abs($value) >= 1000) {
                return round($value / 1000, $format) . "k" . $unit;
            }
        }
        return $value . $unit;
    }

    /**
     * @return array|null
     */
    public function scaleGetXSettings() {
        $data = $this->dataSet->getData();
        foreach ($data["axis"] as $settings) {
            if ($settings["identity"] == Constant::AXIS_X) {
                return [$settings["margin"], $settings["rows"]];
            }
        }
    }

    /**
     * Write Max value on a chart
     * @param int $type
     * @param array $format
     */
    public function writeBounds($type = BOUND_BOTH, $format = null) {
        $maxLabelTxt = isset($format["maxLabelTxt"]) ? $format["maxLabelTxt"] : "max=";
        $minLabelTxt = isset($format["minLabelTxt"]) ? $format["minLabelTxt"] : "min=";
        $decimals = isset($format["decimals"]) ? $format["decimals"] : 1;
        $ExcludedSeries = isset($format["excludedSeries"]) ? $format["excludedSeries"] : "";
        $displayOffset = isset($format["displayOffset"]) ? $format["displayOffset"] : 4;
        $displayColor = isset($format["displayColor"]) ? $format["displayColor"] : DISPLAY_MANUAL;
        $maxDisplayR = isset($format["maxDisplayR"]) ? $format["maxDisplayR"] : 0;
        $maxDisplayG = isset($format["maxDisplayG"]) ? $format["maxDisplayG"] : 0;
        $maxDisplayB = isset($format["maxDisplayB"]) ? $format["maxDisplayB"] : 0;
        $minDisplayR = isset($format["minDisplayR"]) ? $format["minDisplayR"] : 255;
        $minDisplayG = isset($format["minDisplayG"]) ? $format["minDisplayG"] : 255;
        $minDisplayB = isset($format["minDisplayB"]) ? $format["minDisplayB"] : 255;
        $minLabelPos = isset($format["minLabelPos"]) ? $format["minLabelPos"] : BOUND_LABEL_POS_AUTO;
        $maxLabelPos = isset($format["maxLabelPos"]) ? $format["maxLabelPos"] : BOUND_LABEL_POS_AUTO;
        $drawBox = isset($format["drawBox"]) ? $format["drawBox"] : true;
        $drawBoxBorder = isset($format["drawBoxBorder"]) ? $format["drawBoxBorder"] : false;
        $borderOffset = isset($format["borderOffset"]) ? $format["borderOffset"] : 5;
        $boxRounded = isset($format["boxRounded"]) ? $format["boxRounded"] : true;
        $roundedRadius = isset($format["roundedRadius"]) ? $format["roundedRadius"] : 3;
        $boxR = isset($format["boxR"]) ? $format["boxR"] : 0;
        $boxG = isset($format["boxG"]) ? $format["boxG"] : 0;
        $boxB = isset($format["boxB"]) ? $format["boxB"] : 0;
        $boxAlpha = isset($format["boxAlpha"]) ? $format["boxAlpha"] : 20;
        $boxSurrounding = isset($format["boxSurrounding"]) ? $format["boxSurrounding"] : "";
        $boxBorderR = isset($format["boxBorderR"]) ? $format["boxBorderR"] : 255;
        $boxBorderG = isset($format["boxBorderG"]) ? $format["boxBorderG"] : 255;
        $boxBorderB = isset($format["boxBorderB"]) ? $format["boxBorderB"] : 255;
        $boxBorderAlpha = isset($format["boxBorderAlpha"]) ? $format["boxBorderAlpha"] : 100;
        $CaptionSettings = [
            "DrawBox" => $drawBox,
            "DrawBoxBorder" => $drawBoxBorder,
            "BorderOffset" => $borderOffset,
            "BoxRounded" => $boxRounded,
            "RoundedRadius" => $roundedRadius,
            "BoxR" => $boxR,
            "BoxG" => $boxG,
            "BoxB" => $boxB,
            "BoxAlpha" => $boxAlpha,
            "BoxSurrounding" => $boxSurrounding,
            "BoxBorderR" => $boxBorderR,
            "BoxBorderG" => $boxBorderG,
            "BoxBorderB" => $boxBorderB,
            "BoxBorderAlpha" => $boxBorderAlpha
        ];
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        $data = $this->dataSet->getData();
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["abscissa"] && !isset($ExcludedSeries[$serieName])
            ) {
                $r = $serie["color"]["r"];
                $g = $serie["color"]["g"];
                $b = $serie["color"]["b"];
                $minValue = $this->dataSet->getMin($serieName);
                $maxValue = $this->dataSet->getMax($serieName);
                $minPos = VOID;
                $maxPos = VOID;
                foreach ($serie["data"] as $key => $value) {
                    if ($value == $minValue && $minPos == VOID) {
                        $minPos = $key;
                    }
                    if ($value == $maxValue) {
                        $maxPos = $key;
                    }
                }
                $axisID = $serie["axis"];
                $mode = $data["axis"][$axisID]["display"];
                $format = $data["axis"][$axisID]["format"];
                $unit = $data["axis"][$axisID]["unit"];
                $posArray = $this->scaleComputeY(
                        $serie["data"], ["axisID" => $serie["axis"]]
                );
                if ($data["orientation"] == Constant::SCALE_POS_LEFTRIGHT) {
                    $xStep = ($this->graphAreaX2 - $this->graphAreaX1 - $xMargin * 2) / $xDivs;
                    $x = $this->graphAreaX1 + $xMargin;
                    $serieOffset = isset($serie["xOffset"]) ? $serie["xOffset"] : 0;
                    if ($type == Constant::BOUND_MAX || $type == Constant::BOUND_BOTH) {
                        if ($maxLabelPos == Constant::BOUND_LABEL_POS_TOP || ($maxLabelPos == Constant::BOUND_LABEL_POS_AUTO && $maxValue >= 0)
                        ) {
                            $yPos = $posArray[$maxPos] - $displayOffset + 2;
                            $align = Constant::TEXT_ALIGN_BOTTOMMIDDLE;
                        }
                        if ($maxLabelPos == Constant::BOUND_LABEL_POS_BOTTOM || ($maxLabelPos == Constant::BOUND_LABEL_POS_AUTO && $maxValue < 0)
                        ) {
                            $yPos = $posArray[$maxPos] + $displayOffset + 2;
                            $align = Constant::TEXT_ALIGN_TOPMIDDLE;
                        }
                        $xPos = $x + $maxPos * $xStep + $serieOffset;
                        $label = sprintf(
                                '%s%s', $maxLabelTxt, $this->scaleFormat(round($maxValue, $decimals), $mode, $format, $unit)
                        );
                        $txtPos = $this->getTextBox($xPos, $yPos, $this->FontName, $this->FontSize, 0, $label);
                        $xOffset = 0;
                        $yOffset = 0;
                        if ($txtPos[0]["x"] < $this->graphAreaX1) {
                            $xOffset = (($this->graphAreaX1 - $txtPos[0]["x"]) / 2);
                        }
                        if ($txtPos[1]["x"] > $this->graphAreaX2) {
                            $xOffset = -(($txtPos[1]["x"] - $this->graphAreaX2) / 2);
                        }
                        if ($txtPos[2]["y"] < $this->graphAreaY1) {
                            $yOffset = $this->graphAreaY1 - $txtPos[2]["y"];
                        }
                        if ($txtPos[0]["y"] > $this->graphAreaY2) {
                            $yOffset = -($txtPos[0]["y"] - $this->graphAreaY2);
                        }
                        $CaptionSettings["r"] = $maxDisplayR;
                        $CaptionSettings["g"] = $maxDisplayG;
                        $CaptionSettings["b"] = $maxDisplayB;
                        $CaptionSettings["align"] = $align;
                        $this->drawText($xPos + $xOffset, $yPos + $yOffset, $label, $CaptionSettings);
                    }
                    if ($type == Constant::BOUND_MIN || $type == Constant::BOUND_BOTH) {
                        if ($minLabelPos == Constant::BOUND_LABEL_POS_TOP || ($minLabelPos == Constant::BOUND_LABEL_POS_AUTO && $minValue >= 0)
                        ) {
                            $yPos = $posArray[$minPos] - $displayOffset + 2;
                            $align = Constant::TEXT_ALIGN_BOTTOMMIDDLE;
                        }
                        if ($minLabelPos == Constant::BOUND_LABEL_POS_BOTTOM || ($minLabelPos == Constant::BOUND_LABEL_POS_AUTO && $minValue < 0)
                        ) {
                            $yPos = $posArray[$minPos] + $displayOffset + 2;
                            $align = Constant::TEXT_ALIGN_TOPMIDDLE;
                        }
                        $xPos = $x + $minPos * $xStep + $serieOffset;
                        $label = sprintf(
                                '%s%s', $minLabelTxt, $this->scaleFormat(round($minValue, $decimals), $mode, $format, $unit)
                        );
                        $txtPos = $this->getTextBox($xPos, $yPos, $this->FontName, $this->FontSize, 0, $label);
                        $xOffset = 0;
                        $yOffset = 0;
                        if ($txtPos[0]["x"] < $this->graphAreaX1) {
                            $xOffset = (($this->graphAreaX1 - $txtPos[0]["x"]) / 2);
                        }
                        if ($txtPos[1]["x"] > $this->graphAreaX2) {
                            $xOffset = -(($txtPos[1]["x"] - $this->graphAreaX2) / 2);
                        }
                        if ($txtPos[2]["y"] < $this->graphAreaY1) {
                            $yOffset = $this->graphAreaY1 - $txtPos[2]["y"];
                        }
                        if ($txtPos[0]["y"] > $this->graphAreaY2) {
                            $yOffset = -($txtPos[0]["y"] - $this->graphAreaY2);
                        }
                        $CaptionSettings["r"] = $minDisplayR;
                        $CaptionSettings["g"] = $minDisplayG;
                        $CaptionSettings["b"] = $minDisplayB;
                        $CaptionSettings["align"] = $align;
                        $this->drawText(
                                $xPos + $xOffset, $yPos - $displayOffset + $yOffset, $label, $CaptionSettings
                        );
                    }
                } else {
                    $xStep = ($this->graphAreaY2 - $this->graphAreaY1 - $xMargin * 2) / $xDivs;
                    $x = $this->graphAreaY1 + $xMargin;
                    $serieOffset = isset($serie["xOffset"]) ? $serie["xOffset"] : 0;
                    if ($type == Constant::BOUND_MAX || $type == Constant::BOUND_BOTH) {
                        if ($maxLabelPos == Constant::BOUND_LABEL_POS_TOP || ($maxLabelPos == Constant::BOUND_LABEL_POS_AUTO && $maxValue >= 0)
                        ) {
                            $yPos = $posArray[$maxPos] + $displayOffset + 2;
                            $align = Constant::TEXT_ALIGN_MIDDLELEFT;
                        }
                        if ($maxLabelPos == Constant::BOUND_LABEL_POS_BOTTOM || ($maxLabelPos == Constant::BOUND_LABEL_POS_AUTO && $maxValue < 0)
                        ) {
                            $yPos = $posArray[$maxPos] - $displayOffset + 2;
                            $align = Constant::TEXT_ALIGN_MIDDLERIGHT;
                        }
                        $xPos = $x + $maxPos * $xStep + $serieOffset;
                        $label = $maxLabelTxt . $this->scaleFormat($maxValue, $mode, $format, $unit);
                        $txtPos = $this->getTextBox($yPos, $xPos, $this->FontName, $this->FontSize, 0, $label);
                        $xOffset = 0;
                        $yOffset = 0;
                        if ($txtPos[0]["x"] < $this->graphAreaX1) {
                            $xOffset = $this->graphAreaX1 - $txtPos[0]["x"];
                        }
                        if ($txtPos[1]["x"] > $this->graphAreaX2) {
                            $xOffset = -($txtPos[1]["x"] - $this->graphAreaX2);
                        }
                        if ($txtPos[2]["y"] < $this->graphAreaY1) {
                            $yOffset = ($this->graphAreaY1 - $txtPos[2]["y"]) / 2;
                        }
                        if ($txtPos[0]["y"] > $this->graphAreaY2) {
                            $yOffset = -(($txtPos[0]["y"] - $this->graphAreaY2) / 2);
                        }
                        $CaptionSettings["r"] = $maxDisplayR;
                        $CaptionSettings["g"] = $maxDisplayG;
                        $CaptionSettings["b"] = $maxDisplayB;
                        $CaptionSettings["align"] = $align;
                        $this->drawText($yPos + $xOffset, $xPos + $yOffset, $label, $CaptionSettings);
                    }
                    if ($type == Constant::BOUND_MIN || $type == Constant::BOUND_BOTH) {
                        if ($minLabelPos == Constant::BOUND_LABEL_POS_TOP || ($minLabelPos == Constant::BOUND_LABEL_POS_AUTO && $minValue >= 0)
                        ) {
                            $yPos = $posArray[$minPos] + $displayOffset + 2;
                            $align = Constant::TEXT_ALIGN_MIDDLELEFT;
                        }
                        if ($minLabelPos == Constant::BOUND_LABEL_POS_BOTTOM || ($minLabelPos == Constant::BOUND_LABEL_POS_AUTO && $minValue < 0)
                        ) {
                            $yPos = $posArray[$minPos] - $displayOffset + 2;
                            $align = Constant::TEXT_ALIGN_MIDDLERIGHT;
                        }
                        $xPos = $x + $minPos * $xStep + $serieOffset;
                        $label = $minLabelTxt . $this->scaleFormat($minValue, $mode, $format, $unit);
                        $txtPos = $this->getTextBox($yPos, $xPos, $this->FontName, $this->FontSize, 0, $label);
                        $xOffset = 0;
                        $yOffset = 0;
                        if ($txtPos[0]["x"] < $this->graphAreaX1) {
                            $xOffset = $this->graphAreaX1 - $txtPos[0]["x"];
                        }
                        if ($txtPos[1]["x"] > $this->graphAreaX2) {
                            $xOffset = -($txtPos[1]["x"] - $this->graphAreaX2);
                        }
                        if ($txtPos[2]["y"] < $this->graphAreaY1) {
                            $yOffset = ($this->graphAreaY1 - $txtPos[2]["y"]) / 2;
                        }
                        if ($txtPos[0]["y"] > $this->graphAreaY2) {
                            $yOffset = -(($txtPos[0]["y"] - $this->graphAreaY2) / 2);
                        }
                        $CaptionSettings["r"] = $minDisplayR;
                        $CaptionSettings["g"] = $minDisplayG;
                        $CaptionSettings["b"] = $minDisplayB;
                        $CaptionSettings["align"] = $align;
                        $this->drawText($yPos + $xOffset, $xPos + $yOffset, $label, $CaptionSettings);
                    }
                }
            }
        }
    }

    /**
     * Write labels
     * @param string $seriesName
     * @param array $indexes
     * @param array $format
     */
    public function writeLabel($seriesName, $indexes, array $format = []) {
        $overrideTitle = isset($format["overrideTitle"]) ? $format["overrideTitle"] : null;
        $forceLabels = isset($format["forceLabels"]) ? $format["forceLabels"] : null;
        $drawPoint = isset($format["drawPoint"]) ? $format["drawPoint"] : LABEL_POINT_BOX;
        $drawVerticalLine = isset($format["drawVerticalLine"]) ? $format["drawVerticalLine"] : false;
        $verticalLineR = isset($format["verticalLineR"]) ? $format["verticalLineR"] : 0;
        $verticalLineG = isset($format["verticalLineG"]) ? $format["verticalLineG"] : 0;
        $verticalLineB = isset($format["verticalLineB"]) ? $format["verticalLineB"] : 0;
        $verticalLineAlpha = isset($format["verticalLineAlpha"]) ? $format["verticalLineAlpha"] : 40;
        $verticalLineTicks = isset($format["verticalLineTicks"]) ? $format["verticalLineTicks"] : 2;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        if (!is_array($indexes)) {
            $index = $indexes;
            $indexes = [];
            $indexes[] = $index;
        }
        if (!is_array($seriesName)) {
            $serieName = $seriesName;
            $seriesName = [];
            $seriesName[] = $serieName;
        }
        if ($forceLabels != null && !is_array($forceLabels)) {
            $forceLabel = $forceLabels;
            $forceLabels = [];
            $forceLabels[] = $forceLabel;
        }
        foreach ($indexes as $key => $index) {
            $series = [];
            if ($data["orientation"] == SCALE_POS_LEFTRIGHT) {
                if ($xDivs == 0) {
                    $xStep = ($this->graphAreaX2 - $this->graphAreaX1) / 4;
                } else {
                    $xStep = ($this->graphAreaX2 - $this->graphAreaX1 - $xMargin * 2) / $xDivs;
                }
                $x = $this->graphAreaX1 + $xMargin + $index * $xStep;
                if ($drawVerticalLine) {
                    $this->drawLine(
                            $x, $this->graphAreaY1 + $data["yMargin"], $x, $this->graphAreaY2 - $data["yMargin"], [
                        "r" => $verticalLineR,
                        "g" => $verticalLineG,
                        "b" => $verticalLineB,
                        "alpha" => $verticalLineAlpha,
                        "ticks" => $verticalLineTicks
                            ]
                    );
                }
                $minY = $this->graphAreaY2;
                foreach ($seriesName as $serieName) {
                    if (isset($data["series"][$serieName]["data"][$index])) {
                        $axisID = $data["series"][$serieName]["axis"];
                        $xAxisMode = $data["xAxisDisplay"];
                        $xAxisFormat = $data["xAxisFormat"];
                        $xAxisUnit = $data["xAxisUnit"];
                        $axisMode = $data["axis"][$axisID]["display"];
                        $axisFormat = $data["axis"][$axisID]["format"];
                        $axisUnit = $data["axis"][$axisID]["unit"];
                        $xLabel = "";
                        if (isset($data["abscissa"]) && isset($data["series"][$data["abscissa"]]["data"][$index])
                        ) {
                            $xLabel = $this->scaleFormat(
                                    $data["series"][$data["abscissa"]]["data"][$index], $xAxisMode, $xAxisFormat, $xAxisUnit
                            );
                        }
                        if ($overrideTitle != null) {
                            $description = $overrideTitle;
                        } elseif (count($seriesName) == 1) {
                            $description = $data["series"][$serieName]["description"] . " - " . $xLabel;
                        } elseif (isset($data["abscissa"]) && isset($data["series"][$data["abscissa"]]["data"][$index])
                        ) {
                            $description = $xLabel;
                        }
                        $serie = [
                            "r" => $data["series"][$serieName]["color"]["r"],
                            "g" => $data["series"][$serieName]["color"]["g"],
                            "b" => $data["series"][$serieName]["color"]["b"],
                            "alpha" => $data["series"][$serieName]["color"]["alpha"]
                        ];
                        if (count($seriesName) == 1 && isset($data["series"][$serieName]["xOffset"])
                        ) {
                            $serieOffset = $data["series"][$serieName]["xOffset"];
                        } else {
                            $serieOffset = 0;
                        }
                        $value = $data["series"][$serieName]["data"][$index];
                        if ($value == VOID) {
                            $value = "NaN";
                        }
                        if ($forceLabels != null) {
                            $Caption = isset($forceLabels[$key]) ? $forceLabels[$key] : "Not set";
                        } else {
                            $Caption = $this->scaleFormat($value, $axisMode, $axisFormat, $axisUnit);
                        }
                        if ($this->lastChartLayout == Constant::CHART_LAST_LAYOUT_STACKED) {
                            if ($value >= 0) {
                                $lookFor = "+";
                            } else {
                                $lookFor = "-";
                            }
                            $value = 0;
                            $done = false;
                            foreach ($data["series"] as $name => $serieLookup) {
                                if ($serieLookup["isDrawable"] == true && $name != $data["abscissa"] && !$done
                                ) {
                                    if (isset($data["series"][$name]["data"][$index]) && $data["series"][$name]["data"][$index] != VOID
                                    ) {
                                        if ($data["series"][$name]["data"][$index] >= 0 && $lookFor == "+") {
                                            $value = $value + $data["series"][$name]["data"][$index];
                                        }
                                        if ($data["series"][$name]["data"][$index] < 0 && $lookFor == "-") {
                                            $value = $value - $data["series"][$name]["data"][$index];
                                        }
                                        if ($name == $serieName) {
                                            $done = true;
                                        }
                                    }
                                }
                            }
                        }
                        $x = floor($this->graphAreaX1 + $xMargin + $index * $xStep + $serieOffset);
                        $y = floor($this->scaleComputeY($value, ["axisID" => $axisID]));
                        if ($y < $minY) {
                            $minY = $y;
                        }
                        if ($drawPoint == LABEL_POINT_CIRCLE) {
                            $this->drawFilledCircle(
                                    $x, $y, 3, [
                                "r" => 255,
                                "g" => 255,
                                "b" => 255,
                                "BorderR" => 0,
                                "BorderG" => 0,
                                "BorderB" => 0
                                    ]
                            );
                        } elseif ($drawPoint == LABEL_POINT_BOX) {
                            $this->drawFilledRectangle(
                                    $x - 2, $y - 2, $x + 2, $y + 2, [
                                "r" => 255,
                                "g" => 255,
                                "b" => 255,
                                "BorderR" => 0,
                                "BorderG" => 0,
                                "BorderB" => 0
                                    ]
                            );
                        }
                        $series[] = ["format" => $serie, "Caption" => $Caption];
                    }
                }
                $this->drawLabelBox($x, $minY - 3, $description, $series, $format);
            } else {
                if ($xDivs == 0) {
                    $xStep = ($this->graphAreaY2 - $this->graphAreaY1) / 4;
                } else {
                    $xStep = ($this->graphAreaY2 - $this->graphAreaY1 - $xMargin * 2) / $xDivs;
                }
                $y = $this->graphAreaY1 + $xMargin + $index * $xStep;
                if ($drawVerticalLine) {
                    $this->drawLine(
                            $this->graphAreaX1 + $data["yMargin"], $y, $this->graphAreaX2 - $data["yMargin"], $y, [
                        "r" => $verticalLineR,
                        "g" => $verticalLineG,
                        "b" => $verticalLineB,
                        "alpha" => $verticalLineAlpha,
                        "ticks" => $verticalLineTicks
                            ]
                    );
                }
                $minX = $this->graphAreaX2;
                foreach ($seriesName as $key => $serieName) {
                    if (isset($data["series"][$serieName]["data"][$index])) {
                        $axisID = $data["series"][$serieName]["axis"];
                        $xAxisMode = $data["xAxisDisplay"];
                        $xAxisFormat = $data["xAxisFormat"];
                        $xAxisUnit = $data["xAxisUnit"];
                        $axisMode = $data["axis"][$axisID]["display"];
                        $axisFormat = $data["axis"][$axisID]["format"];
                        $axisUnit = $data["axis"][$axisID]["unit"];
                        $xLabel = "";
                        if (isset($data["abscissa"]) && isset($data["series"][$data["abscissa"]]["data"][$index])
                        ) {
                            $xLabel = $this->scaleFormat(
                                    $data["series"][$data["abscissa"]]["data"][$index], $xAxisMode, $xAxisFormat, $xAxisUnit
                            );
                        }
                        if ($overrideTitle != null) {
                            $description = $overrideTitle;
                        } elseif (count($seriesName) == 1) {
                            if (isset($data["abscissa"]) && isset($data["series"][$data["abscissa"]]["data"][$index])
                            ) {
                                $description = $data["series"][$serieName]["description"] . " - " . $xLabel;
                            }
                        } elseif (isset($data["abscissa"]) && isset($data["series"][$data["abscissa"]]["data"][$index])
                        ) {
                            $description = $xLabel;
                        }
                        $serie = [];
                        if (isset($data["extended"]["palette"][$index])) {
                            $serie["r"] = $data["extended"]["palette"][$index]["r"];
                            $serie["g"] = $data["extended"]["palette"][$index]["g"];
                            $serie["b"] = $data["extended"]["palette"][$index]["b"];
                            $serie["alpha"] = $data["extended"]["palette"][$index]["alpha"];
                        } else {
                            $serie["r"] = $data["series"][$serieName]["color"]["r"];
                            $serie["g"] = $data["series"][$serieName]["color"]["g"];
                            $serie["b"] = $data["series"][$serieName]["color"]["b"];
                            $serie["alpha"] = $data["series"][$serieName]["color"]["alpha"];
                        }
                        if (count($seriesName) == 1 && isset($data["series"][$serieName]["xOffset"])) {
                            $serieOffset = $data["series"][$serieName]["xOffset"];
                        } else {
                            $serieOffset = 0;
                        }
                        $value = $data["series"][$serieName]["data"][$index];
                        if ($forceLabels != null) {
                            $Caption = isset($forceLabels[$key]) ? $forceLabels[$key] : "Not set";
                        } else {
                            $Caption = $this->scaleFormat($value, $axisMode, $axisFormat, $axisUnit);
                        }
                        if ($value == Constant::VOID) {
                            $value = "NaN";
                        }
                        if ($this->lastChartLayout == Constant::CHART_LAST_LAYOUT_STACKED) {
                            if ($value >= 0) {
                                $lookFor = "+";
                            } else {
                                $lookFor = "-";
                            }
                            $value = 0;
                            $done = false;
                            foreach ($data["series"] as $name => $serieLookup) {
                                if ($serieLookup["isDrawable"] == true && $name != $data["abscissa"] && !$done
                                ) {
                                    if (isset($data["series"][$name]["data"][$index]) && $data["series"][$name]["data"][$index] != VOID
                                    ) {
                                        if ($data["series"][$name]["data"][$index] >= 0 && $lookFor == "+") {
                                            $value = $value + $data["series"][$name]["data"][$index];
                                        }
                                        if ($data["series"][$name]["data"][$index] < 0 && $lookFor == "-") {
                                            $value = $value - $data["series"][$name]["data"][$index];
                                        }
                                        if ($name == $serieName) {
                                            $done = true;
                                        }
                                    }
                                }
                            }
                        }
                        $x = floor($this->scaleComputeY($value, ["axisID" => $axisID]));
                        $y = floor($this->graphAreaY1 + $xMargin + $index * $xStep + $serieOffset);
                        if ($x < $minX) {
                            $minX = $x;
                        }
                        if ($drawPoint == LABEL_POINT_CIRCLE) {
                            $this->drawFilledCircle(
                                    $x, $y, 3, [
                                "r" => 255,
                                "g" => 255,
                                "b" => 255,
                                "BorderR" => 0,
                                "BorderG" => 0,
                                "BorderB" => 0
                                    ]
                            );
                        } elseif ($drawPoint == LABEL_POINT_BOX) {
                            $this->drawFilledRectangle(
                                    $x - 2, $y - 2, $x + 2, $y + 2, [
                                "r" => 255,
                                "g" => 255,
                                "b" => 255,
                                "BorderR" => 0,
                                "BorderG" => 0,
                                "BorderB" => 0
                                    ]
                            );
                        }
                        $series[] = ["format" => $serie, "Caption" => $Caption];
                    }
                }
                $this->drawLabelBox($minX, $y - 3, $description, $series, $format);
            }
        }
    }

}

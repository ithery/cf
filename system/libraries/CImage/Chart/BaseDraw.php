<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 30, 2019, 2:57:57 AM
 */
use CImage_Chart_Constant as Constant;

class CImage_Chart_BaseDraw {
    /**
     * Width of the picture.
     *
     * @var int
     */
    public $xSize;

    /**
     * Height of the picture.
     *
     * @var int
     */
    public $ySize;

    /**
     * GD picture object.
     *
     * @var GdImage|resource
     */
    public $picture;

    /**
     * Turn antialias on or off.
     *
     * @var bool
     */
    public $antialias = true;

    /**
     * Quality of the antialiasing implementation (0-1).
     *
     * @var int
     */
    public $antialiasQuality = 0;

    /**
     * Already drawn pixels mask (Filled circle implementation).
     *
     * @var array
     */
    public $mask = [];

    /**
     * Just to know if we need to flush the alpha channels when rendering.
     *
     * @var bool
     */
    public $transparentBackground = false;

    /**
     * Graph area X origin.
     *
     * @var int
     */
    public $graphAreaX1;

    /**
     * Graph area Y origin.
     *
     * @var int
     */
    public $graphAreaY1;

    /**
     * Graph area bottom right X position.
     *
     * @var int
     */
    public $graphAreaX2;

    /**
     * Graph area bottom right Y position.
     *
     * @var int
     */
    public $graphAreaY2;

    /**
     * Minimum height for scale divs.
     *
     * @var int
     */
    public $scaleMinDivHeight = 20;

    /**
     * @var string
     */
    public $fontName = 'verdana.ttf';

    /**
     * @var int
     */
    public $fontSize = 12;

    /**
     * Return the bounding box of the last written string.
     *
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
     * Turn shadows on or off.
     *
     * @var bool
     */
    public $shadow = false;

    /**
     * X Offset of the shadow.
     *
     * @var int
     */
    public $shadowX;

    /**
     * Y Offset of the shadow.
     *
     * @var int
     */
    public $shadowY;

    /**
     * R component of the shadow.
     *
     * @var int
     */
    public $shadowR;

    /**
     * G component of the shadow.
     *
     * @var int
     */
    public $shadowG;

    /**
     * B component of the shadow.
     *
     * @var int
     */
    public $shadowB;

    /**
     * Alpha level of the shadow.
     *
     * @var int
     */
    public $shadowA;

    /**
     * Array containing the image map.
     *
     * @var array
     */
    public $imageMap = [];

    /**
     * Name of the session array.
     *
     * @var int
     */
    public $imageMapIndex = 'pChart';

    /**
     * Save the current imagemap storage mode.
     *
     * @var int
     */
    public $imageMapStorageMode;

    /**
     * Automatic deletion of the image map temp files.
     *
     * @var bool
     */
    public $imageMapAutoDelete = true;

    /**
     * Attached dataset.
     *
     * @var CImage_Chart_Data
     */
    public $dataSet;

    /**
     * Last generated chart info
     * Last layout : regular or stacked.
     *
     * @var int
     */
    public $lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;

    /**
     * @var string
     */
    private $resourcePath;

    public function __construct() {
        $this->resourcePath = DOCROOT . 'system/media';
        $this->fontName = $this->loadFont($this->fontName, 'font');
    }

    /**
     * Set the path to the folder containing library resources (fonts, data, palettes).
     *
     * @param string $path
     *
     * @throws Exception
     */
    public function setResourcePath($path) {
        $escapedPath = rtrim($path, '/');
        if (!file_exists($escapedPath)) {
            throw new Exception(sprintf(
                "The path '%s' to resources' folder does not exist!",
                $escapedPath
            ));
        }
        $this->resourcePath = $escapedPath;
    }

    /**
     * Check if requested resource exists and return the path to it if yes.
     *
     * @param string $name
     * @param string $type
     *
     * @throws Exception
     *
     * @return string
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
     * Allocate a color with transparency.
     *
     * @param GdImage|resource $picture
     * @param int              $r
     * @param int              $g
     * @param int              $b
     * @param int              $alpha
     *
     * @return int
     */
    public function allocateColor($picture, $r, $g, $b, $alpha = 100) {
        if ($r < 0) {
            $r = 0;
        }
        if ($r > 255) {
            $r = 255;
        }
        if ($g < 0) {
            $g = 0;
        }
        if ($g > 255) {
            $g = 255;
        }
        if ($b < 0) {
            $b = 0;
        }
        if ($b > 255) {
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
     * Convert apha to base 10.
     *
     * @param int|float $alphaValue
     *
     * @return int
     */
    public function convertAlpha($alphaValue) {
        return (127 / 100) * (100 - $alphaValue);
    }

    /**
     * @param string $fileName
     *
     * @return array
     */
    public function getPicInfo($fileName) {
        $infos = getimagesize($fileName);
        $width = $infos[0];
        $height = $infos[1];
        $type = $infos['mime'];
        if ($type == 'image/png') {
            $type = 1;
        }
        if ($type == 'image/gif') {
            $type = 2;
        }
        if ($type == 'image/jpeg ') {
            $type = 3;
        }

        return [$width, $height, $type];
    }

    /**
     * Compute the scale, check for the best visual factors.
     *
     * @param int   $xMin
     * @param int   $xMax
     * @param int   $maxDivs
     * @param array $factors
     * @param int   $axisID
     *
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
            $decimals = preg_split("/\./", $result['rowHeight']);
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
            if ($results[$factor]['rows'] > $maxRows) {
                $maxRows = $results[$factor]['rows'];
                $bestFactor = $factor;
            }
        }
        /* Return the best visual scale */
        return $results[$bestFactor];
    }

    /**
     * Compute the best matching scale based on size & factors.
     *
     * @param int   $xMin
     * @param int   $xMax
     * @param int   $maxDivs
     * @param array $factors
     * @param int   $axisID
     *
     * @return array
     */
    public function processScale($xMin, $xMax, $maxDivs, array $factors, $axisID) {
        $scaleHeight = abs(ceil($xMax) - floor($xMin));
        $format = null;
        if (isset($this->dataSet->data['axis'][$axisID]['format'])) {
            $format = $this->dataSet->data['axis'][$axisID]['format'];
        }
        $mode = Constant::AXIS_FORMAT_DEFAULT;
        if (isset($this->dataSet->data['axis'][$axisID]['display'])) {
            $mode = $this->dataSet->data['axis'][$axisID]['display'];
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
                            $xMinRescaled = floor($xMin / ($factor * $scaled10Factor)) * $factor * $scaled10Factor;
                        }
                        $xMaxRescaled = $xMax;
                        if (!($this->modulo($xMax, $factor * $scaled10Factor) == 0) || ($xMax != floor($xMax))
                        ) {
                            $xMaxRescaled = floor($xMax / ($factor * $scaled10Factor)) * $factor * $scaled10Factor + ($factor * $scaled10Factor);
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
            $scale['rows'] = $rows;
            $scale['rowHeight'] = $rowHeight;
            $scale['xMin'] = $xMin;
            $scale['xMax'] = $xMax;
            /* Compute the needed decimals for the metric view to avoid repetition of the same X Axis labels */
            if ($mode == Constant::AXIS_FORMAT_METRIC && $format == null) {
                $done = false;
                $goodDecimals = 0;
                for ($decimals = 0; $decimals <= 10; $decimals++) {
                    if (!$done) {
                        $lastLabel = 'zob';
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
                $scale['format'] = $goodDecimals;
            }
        } else {
            /* If all values are the same we keep a +1/-1 scale */
            $rows = 2;
            $xMin = $xMax - 1;
            $xMax = $xMax + 1;
            $rowHeight = 1;
            /* Return the results */
            $scale['rows'] = $rows;
            $scale['rowHeight'] = $rowHeight;
            $scale['xMin'] = $xMin;
            $scale['xMax'] = $xMax;
        }

        return $scale;
    }

    /**
     * @param int|float $value1
     * @param int|float $value2
     *
     * @return float
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
     * @param int   $labelingMethod
     * @param int   $iD
     * @param bool  $labelSkip
     *
     * @return bool
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
     * Returns the number of drawable series.
     *
     * @return int
     */
    public function countDrawableSeries() {
        $count = 0;
        $data = $this->dataSet->getData();
        foreach ($data['series'] as $serieName => $serie) {
            if ($serie['isDrawable'] == true && $serieName != $data['abscissa']) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Fix box coordinates.
     *
     * @param int $xa
     * @param int $ya
     * @param int $xb
     * @param int $yb
     *
     * @return int[]
     */
    public function fixBoxCoordinates($xa, $ya, $xb, $yb) {
        return [min($xa, $xb), min($ya, $yb), max($xa, $xb), max($ya, $yb)];
    }

    /**
     * Apply AALias correction to the rounded box boundaries.
     *
     * @param int|float $value
     * @param int       $mode
     *
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
     * Return the abscissa margin.
     *
     * @param array $data
     *
     * @return int
     */
    public function getAbscissaMargin(array $data) {
        foreach ($data['axis'] as $values) {
            if ($values['identity'] == Constant::AXIS_X) {
                return $values['margin'];
            }
        }

        return 0;
    }

    /**
     * Returns a random color.
     *
     * @param int $alpha
     *
     * @return array
     */
    public function getRandomColor($alpha = 100) {
        return [
            'r' => rand(0, 255),
            'g' => rand(0, 255),
            'b' => rand(0, 255),
            'alpha' => $alpha
        ];
    }

    /**
     * Validate a palette.
     *
     * @param mixed     $Colors
     * @param int|float $surrounding
     *
     * @return array
     */
    public function validatePalette($Colors, $surrounding = null) {
        $result = [];
        if (!is_array($Colors)) {
            return $this->getRandomColor();
        }
        foreach ($Colors as $key => $values) {
            if (isset($values['r'])) {
                $result[$key]['r'] = $values['r'];
            } else {
                $result[$key]['r'] = rand(0, 255);
            }
            if (isset($values['g'])) {
                $result[$key]['g'] = $values['g'];
            } else {
                $result[$key]['g'] = rand(0, 255);
            }
            if (isset($values['b'])) {
                $result[$key]['b'] = $values['b'];
            } else {
                $result[$key]['b'] = rand(0, 255);
            }
            if (isset($values['alpha'])) {
                $result[$key]['alpha'] = $values['alpha'];
            } else {
                $result[$key]['alpha'] = 100;
            }
            if (null !== $surrounding) {
                $result[$key]['borderR'] = $result[$key]['r'] + $surrounding;
                $result[$key]['borderG'] = $result[$key]['g'] + $surrounding;
                $result[$key]['borderB'] = $result[$key]['b'] + $surrounding;
            } else {
                if (isset($values['borderR'])) {
                    $result[$key]['borderR'] = $values['borderR'];
                } else {
                    $result[$key]['borderR'] = $result[$key]['r'];
                }
                if (isset($values['borderG'])) {
                    $result[$key]['borderG'] = $values['borderG'];
                } else {
                    $result[$key]['borderG'] = $result[$key]['g'];
                }
                if (isset($values['borderB'])) {
                    $result[$key]['borderB'] = $values['borderB'];
                } else {
                    $result[$key]['borderB'] = $result[$key]['b'];
                }
                if (isset($values['borderAlpha'])) {
                    $result[$key]['borderAlpha'] = $values['borderAlpha'];
                } else {
                    $result[$key]['borderAlpha'] = $result[$key]['alpha'];
                }
            }
        }

        return $result;
    }

    /**
     * @param mixed $values
     * @param array $option
     * @param bool  $returnOnly0Height
     *
     * @return int|float|array
     */
    public function scaleComputeY($values, array $option = [], $returnOnly0Height = false) {
        $axisID = isset($option['axisID']) ? $option['axisID'] : 0;
        $serieName = isset($option['serieName']) ? $option['serieName'] : null;
        $data = $this->dataSet->getData();
        if (!isset($data['axis'][$axisID])) {
            return -1;
        }
        if ($serieName != null) {
            $axisID = $data['series'][$serieName]['axis'];
        }
        if (!is_array($values)) {
            $tmp = $values;
            $values = [];
            $values[0] = $tmp;
        }
        $result = [];
        if (carr::get($data, 'orientation') == Constant::SCALE_POS_LEFTRIGHT) {
            $height = ($this->graphAreaY2 - $this->graphAreaY1) - $data['axis'][$axisID]['margin'] * 2;
            $scaleHeight = $data['axis'][$axisID]['scaleMax'] - $data['axis'][$axisID]['scaleMin'];
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
                        $result[] = $this->graphAreaY2 - $data['axis'][$axisID]['margin'] - ($step * ($value - $data['axis'][$axisID]['scaleMin']));
                    }
                }
            }
        } else {
            $width = ($this->graphAreaX2 - $this->graphAreaX1) - (carr::get($data, 'axis.'.$axisID.'.margin', 0) * 2);
            $scaleWidth = $data['axis'][$axisID]['scaleMax'] - $data['axis'][$axisID]['scaleMin'];
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
                        $result[] = $this->graphAreaX1 + $data['axis'][$axisID]['margin'] + ($step * ($value - $data['axis'][$axisID]['scaleMin']));
                    }
                }
            }
        }

        return count($result) == 1 ? reset($result) : $result;
    }

    /**
     * Format the axis values.
     *
     * @param mixed          $value
     * @param int            $mode
     * @param null|array|int $format
     * @param string         $unit
     *
     * @return string
     */
    public function scaleFormat($value, $mode = null, $format = null, $unit = null) {
        if ($value == Constant::VOID) {
            return '';
        }
        if ($mode == Constant::AXIS_FORMAT_TRAFFIC) {
            if ($value == 0) {
                return '0B';
            }
            $units = ['b', 'KB', 'MB', 'GB', 'TB', 'PB'];
            $sign = '';
            if ($value < 0) {
                $value = abs($value);
                $sign = '-';
            }
            $value = number_format($value / pow(1024, ($scale = floor(log($value, 1024)))), 2, ',', '.');

            return $sign . $value . ' ' . $units[$scale];
        }
        if ($mode == Constant::AXIS_FORMAT_CUSTOM) {
            if (is_callable($format)) {
                return call_user_func($format, $value);
            }
        }
        if ($mode == Constant::AXIS_FORMAT_DATE) {
            $pattern = 'd/m/Y';
            if ($format !== null) {
                $pattern = $format;
            }

            return gmdate($pattern, $value);
        }
        if ($mode == Constant::AXIS_FORMAT_TIME) {
            $pattern = 'H:i:s';
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
                return round($value / 1000000000, $format) . 'g' . $unit;
            }
            if (abs($value) > 1000000) {
                return round($value / 1000000, $format) . 'm' . $unit;
            } elseif (abs($value) >= 1000) {
                return round($value / 1000, $format) . 'k' . $unit;
            }
        }

        return $value . $unit;
    }

    /**
     * @return null|array
     */
    public function scaleGetXSettings() {
        $data = $this->dataSet->getData();
        foreach ($data['axis'] as $settings) {
            if ($settings['identity'] == Constant::AXIS_X) {
                return [$settings['margin'], $settings['rows']];
            }
        }
    }

    /**
     * Return the HTML converted color from the RGB composite values.
     *
     * @param int $r
     * @param int $g
     * @param int $b
     *
     * @return string
     */
    public function toHTMLColor($r, $g, $b) {
        $r = intval($r);
        $g = intval($g);
        $b = intval($b);
        $r = dechex($r < 0 ? 0 : ($r > 255 ? 255 : $r));
        $g = dechex($g < 0 ? 0 : ($g > 255 ? 255 : $g));
        $b = dechex($b < 0 ? 0 : ($b > 255 ? 255 : $b));
        $Color = '#' . (strlen($r) < 2 ? '0' : '') . $r;
        $Color .= (strlen($g) < 2 ? '0' : '') . $g;
        $Color .= (strlen($b) < 2 ? '0' : '') . $b;

        return $Color;
    }

    /**
     * Return the orientation of a line.
     *
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     *
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
     * Return the length between two points.
     *
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     *
     * @return float
     */
    public function getLength($x1, $y1, $x2, $y2) {
        return sqrt(
            pow(max($x1, $x2) - min($x1, $x2), 2) + pow(max($y1, $y2) - min($y1, $y2), 2)
        );
    }

    /**
     * Add a zone to the image map.
     *
     * @param string      $Type
     * @param string      $Plots
     * @param null|string $Color
     * @param string      $Title
     * @param string      $Message
     * @param bool        $hTMLEncode
     */
    public function addToImageMap(
        $Type,
        $Plots,
        $Color = null,
        $Title = null,
        $Message = null,
        $hTMLEncode = false
    ) {
        if ($this->imageMapStorageMode == null) {
            $this->initialiseImageMap();
        }
        /* Encode the characters in the imagemap in HTML standards */
        $Title = htmlentities(
            str_replace('&#8364;', "\u20AC", $Title),
            ENT_QUOTES,
            'ISO-8859-15'
        );
        if ($hTMLEncode) {
            $Message = str_replace(
                '&gt;',
                '>',
                str_replace(
                    '&lt;',
                    '<',
                    htmlentities($Message, ENT_QUOTES, 'ISO-8859-15')
                )
            );
        }
        if ($this->imageMapStorageMode == Constant::IMAGE_MAP_STORAGE_SESSION) {
            if (!isset($_SESSION)) {
                $this->initialiseImageMap();
            }
            $_SESSION[$this->imageMapIndex][] = [$Type, $Plots, $Color, $Title, $Message];
        } elseif ($this->imageMapStorageMode == Constant::IMAGE_MAP_STORAGE_FILE) {
            $handle = fopen(
                sprintf('%s/%s.map', $this->imageMapStorageFolder, $this->imageMapFileName),
                'a'
            );
            fwrite(
                $handle,
                sprintf(
                    "%s%s%s%s%s%s%s%s%s\r\n",
                    $Type,
                    Constant::imageMapDelimiter(),
                    $Plots,
                    Constant::imageMapDelimiter(),
                    $Color,
                    Constant::imageMapDelimiter(),
                    $Title,
                    Constant::imageMapDelimiter(),
                    $Message
                )
            );
            fclose($handle);
        }
    }

    /**
     * Initialise the image map methods.
     *
     * @param string $name
     * @param int    $storageMode
     * @param string $UniqueID
     * @param string $storageFolder
     */
    public function initialiseImageMap(
        $name = 'pChart',
        $storageMode = Constant::IMAGE_MAP_STORAGE_SESSION,
        $UniqueID = 'imageMap',
        $storageFolder = 'tmp'
    ) {
        $this->imageMapIndex = $name;
        $this->imageMapStorageMode = $storageMode;
        if ($storageMode == Constant::IMAGE_MAP_STORAGE_SESSION) {
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION[$this->imageMapIndex] = null;
        } elseif ($storageMode == Constant::IMAGE_MAP_STORAGE_FILE) {
            $this->imageMapFileName = $UniqueID;
            $this->imageMapStorageFolder = $storageFolder;
            $path = sprintf('%s/%s.map', $storageFolder, $UniqueID);
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }

    /**
     * Set current font properties.
     *
     * @param array $format
     */
    public function setFontProperties($format = []) {
        $r = isset($format['r']) ? $format['r'] : -1;
        $g = isset($format['g']) ? $format['g'] : -1;
        $b = isset($format['b']) ? $format['b'] : -1;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 100;
        $fontName = isset($format['fontName']) ? $format['fontName'] : null;
        $fontSize = isset($format['fontSize']) ? $format['fontSize'] : null;
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
     * Reverse an array of points.
     *
     * @param array $Plots
     *
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
}

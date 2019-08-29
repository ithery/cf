<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 30, 2019, 2:18:02 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CImage_Chart_Constant as Constant;

class CImage_Chart_Data {

    /**
     * @var array
     */
    public $data = [];

    /**
     * @var array
     */
    public $palette = [
        "0" => ["r" => 188, "g" => 224, "b" => 46, "alpha" => 100],
        "1" => ["r" => 224, "g" => 100, "b" => 46, "alpha" => 100],
        "2" => ["r" => 224, "g" => 214, "b" => 46, "alpha" => 100],
        "3" => ["r" => 46, "g" => 151, "b" => 224, "alpha" => 100],
        "4" => ["r" => 176, "g" => 46, "b" => 224, "alpha" => 100],
        "5" => ["r" => 224, "g" => 46, "b" => 117, "alpha" => 100],
        "6" => ["r" => 92, "g" => 224, "b" => 46, "alpha" => 100],
        "7" => ["r" => 224, "g" => 176, "b" => 46, "alpha" => 100]
    ];

    /**
     * Initialise a given serie
     * @param string $serie
     */
    public function initialise($serie) {
        $id = 0;
        if (isset($this->data["series"])) {
            $id = count($this->data["series"]);
        }
        $this->data["series"][$serie]["description"] = $serie;
        $this->data["series"][$serie]["isDrawable"] = true;
        $this->data["series"][$serie]["picture"] = null;
        $this->data["series"][$serie]["max"] = null;
        $this->data["series"][$serie]["min"] = null;
        $this->data["series"][$serie]["axis"] = 0;
        $this->data["series"][$serie]["ticks"] = 0;
        $this->data["series"][$serie]["weight"] = 0;
        $this->data["series"][$serie]["shape"] = Constant::SERIE_SHAPE_FILLEDCIRCLE;
        if (isset($this->palette[$id])) {
            $this->data["series"][$serie]["color"] = $this->palette[$id];
        } else {
            $this->data["series"][$serie]["color"]["r"] = rand(0, 255);
            $this->data["series"][$serie]["color"]["g"] = rand(0, 255);
            $this->data["series"][$serie]["color"]["b"] = rand(0, 255);
            $this->data["series"][$serie]["color"]["alpha"] = 100;
        }
        $this->data["series"][$serie]["data"] = [];
    }

    /**
     * Add a single point or an array to the given serie
     * @param mixed $values
     * @param string $serieName
     * @return int
     */
    public function addPoints($values, $serieName = "Serie1") {
        if (!isset($this->data["series"][$serieName])) {
            $this->initialise($serieName);
        }
        if (is_array($values)) {
            foreach ($values as $value) {
                $this->data["series"][$serieName]["data"][] = $value;
            }
        } else {
            $this->data["series"][$serieName]["data"][] = $values;
        }
        if ($values != Constant::VOID) {
            $strippedData = $this->stripVOID($this->data["series"][$serieName]["data"]);
            if (empty($strippedData)) {
                $this->data["series"][$serieName]["max"] = 0;
                $this->data["series"][$serieName]["min"] = 0;
                return 0;
            }
            $this->data["series"][$serieName]["max"] = max($strippedData);
            $this->data["series"][$serieName]["min"] = min($strippedData);
        }
    }

    /**
     * Strip VOID values
     * @param mixed $Values
     * @return array
     */
    public function stripVOID($values) {
        if (!is_array($values)) {
            return [];
        }
        $result = [];
        foreach ($values as $value) {
            if ($value != Constant::VOID) {
                $result[] = $value;
            }
        }
        return $result;
    }

    /**
     * Associate a name to an axis
     * @param int $axisId
     * @param string $name
     */
    public function setAxisName($axisId, $name) {
        if (isset($this->data["axis"][$axisId])) {
            $this->data["axis"][$axisId]["name"] = $name;
        }
    }

    /**
     * Design an axis as X or Y member
     * @param int $axisId
     * @param int $identity
     */
    public function setAxisXY($axisId, $identity = Constant::AXIS_Y) {
        if (isset($this->data["axis"][$axisId])) {
            $this->data["axis"][$axisId]["identity"] = $identity;
        }
    }

    /**
     * Set the position of an Axis
     * @param int $axisId
     * @param int $position
     */
    public function setAxisPosition($axisId, $position = Constant::AXIS_POSITION_LEFT) {
        if (isset($this->data["axis"][$axisId])) {
            $this->data["axis"][$axisId]["position"] = $position;
        }
    }

    /**
     * Associate one data serie with one axis
     * @param mixed $series
     * @param int $axisId
     */
    public function setSerieOnAxis($series, $axisId) {
        if (!is_array($series)) {
            $series = $this->convertToArray($series);
        }
        foreach ($series as $serie) {
            $previousAxis = $this->data["series"][$serie]["axis"];
            /* Create missing axis */
            if (!isset($this->data["axis"][$axisId])) {
                $this->data["axis"][$axisId]["position"] = Constant::AXIS_POSITION_LEFT;
                $this->data["axis"][$axisId]["identity"] = Constant::AXIS_Y;
            }
            $this->data["series"][$serie]["axis"] = $axisId;
            /* Cleanup unused axis */
            $found = false;
            foreach ($this->data["series"] as $values) {
                if ($values["axis"] == $previousAxis) {
                    $found = true;
                }
            }
            if (!$found) {
                unset($this->data["axis"][$previousAxis]);
            }
        }
    }

    /**
     * Convert a string to a single elements array
     * @param mixed $value
     * @return array
     */
    public function convertToArray($value) {
        return [$value];
    }

    /**
     * Associate an unit to an axis
     * @param int $axisId
     * @param string $unit
     */
    public function setAxisUnit($axisId, $unit) {
        if (isset($this->data["axis"][$axisId])) {
            $this->data["axis"][$axisId]["unit"] = $unit;
        }
    }

    /**
     * Create a scatter group specified in X and Y data series
     * @param string $serieX
     * @param string $serieY
     * @param int $id
     */
    public function setScatterSerie($serieX, $serieY, $id = 0) {
        if (isset($this->data["series"][$serieX]) && isset($this->data["series"][$serieY])) {
            $this->initScatterSerie($id);
            $this->data["scatterSeries"][$id]["x"] = $serieX;
            $this->data["scatterSeries"][$id]["y"] = $serieY;
        }
    }

    /**
     * Initialise a given scatter serie
     * @param int $id
     * @return null
     */
    public function initScatterSerie($id) {
        if (isset($this->data["scatterSeries"][$id])) {
            return null;
        }
        $this->data["scatterSeries"][$id]["description"] = "Scatter " . $id;
        $this->data["scatterSeries"][$id]["isDrawable"] = true;
        $this->data["scatterSeries"][$id]["picture"] = null;
        $this->data["scatterSeries"][$id]["ticks"] = 0;
        $this->data["scatterSeries"][$id]["weight"] = 0;
        if (isset($this->Palette[$id])) {
            $this->data["scatterSeries"][$id]["color"] = $this->palette[$id];
        } else {
            $this->data["scatterSeries"][$id]["color"]["r"] = rand(0, 255);
            $this->data["scatterSeries"][$id]["color"]["g"] = rand(0, 255);
            $this->data["scatterSeries"][$id]["color"]["b"] = rand(0, 255);
            $this->data["scatterSeries"][$id]["color"]["alpha"] = 100;
        }
    }

    /**
     * Set the description of a given scatter serie
     * @param int $id
     * @param string $description
     */
    public function setScatterSerieDescription($id, $description = "My serie") {
        if (isset($this->data["scatterSeries"][$id])) {
            $this->data["scatterSeries"][$id]["description"] = $description;
        }
    }

    /**
     * Define if a scatter serie should be draw with ticks
     * @param int $id
     * @param int $width
     */
    public function setScatterSerieTicks($id, $width = 0) {
        if (isset($this->data["scatterSeries"][$id])) {
            $this->data["scatterSeries"][$id]["ticks"] = $width;
        }
    }

    /**
     * Define if a scatter serie should be draw with a special weight
     * @param int $id
     * @param int $weight
     */
    public function setScatterSerieWeight($id, $weight = 0) {
        if (isset($this->data["scatterSeries"][$id])) {
            $this->data["scatterSeries"][$id]["weight"] = $weight;
        }
    }

    /**
     * Associate a color to a scatter serie
     * @param int $id
     * @param array $format
     */
    public function setScatterSerieColor($id, array $format) {
        $r = isset($format["r"]) ? $format["r"] : 0;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        if (isset($this->data["scatterSeries"][$id])) {
            $this->data["scatterSeries"][$id]["color"]["r"] = $r;
            $this->data["scatterSeries"][$id]["color"]["g"] = $g;
            $this->data["scatterSeries"][$id]["color"]["b"] = $b;
            $this->data["scatterSeries"][$id]["color"]["alpha"] = $alpha;
        }
    }
    
     /**
     * Draw a filled rectangle
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @param array $format
     */
    public function drawFilledRectangle($x1, $y1, $X2, $y2, array $format = [])
    {
        $R = isset($format["r"]) ? $format["r"] : 0;
        $G = isset($format["G"]) ? $format["G"] : 0;
        $B = isset($format["B"]) ? $format["B"] : 0;
        $Alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $BorderR = isset($format["borderR"]) ? $format["borderR"] : -1;
        $BorderG = isset($format["borderG"]) ? $format["borderG"] : -1;
        $BorderB = isset($format["borderB"]) ? $format["borderB"] : -1;
        $BorderAlpha = isset($format["borderAlpha"]) ? $format["borderAlpha"] : $Alpha;
        $Surrounding = isset($format["surrounding"]) ? $format["surrounding"] : null;
        $Ticks = isset($format["ticks"]) ? $format["ticks"] : null;
        $NoAngle = isset($format["boAngle"]) ? $format["noAngle"] : null;
        $Dash = isset($format["dash"]) ? $format["Dash"] : false;
        $DashStep = isset($format["dashStep"]) ? $format["DashStep"] : 4;
        $DashR = isset($format["dashR"]) ? $format["dashR"] : 0;
        $DashG = isset($format["dashG"]) ? $format["dashG"] : 0;
        $DashB = isset($format["dashB"]) ? $format["dashB"] : 0;
        $NoBorder = isset($format["NoBorder"]) ? $format["NoBorder"] : false;
        if ($Surrounding != null) {
            $BorderR = $R + $Surrounding;
            $BorderG = $G + $Surrounding;
            $BorderB = $B + $Surrounding;
        }
        if ($X1 > $X2) {
            list($X1, $X2) = [$X2, $X1];
        }
        if ($Y1 > $Y2) {
            list($Y1, $Y2) = [$Y2, $Y1];
        }
        $RestoreShadow = $this->Shadow;
        if ($this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0) {
            $this->Shadow = false;
            $this->drawFilledRectangle(
                $X1 + $this->ShadowX,
                $Y1 + $this->ShadowY,
                $X2 + $this->ShadowX,
                $Y2 + $this->ShadowY,
                [
                    "r" => $this->ShadowR,
                    "G" => $this->ShadowG,
                    "B" => $this->ShadowB,
                    "Alpha" => $this->Shadowa,
                    "Ticks" => $Ticks,
                    "NoAngle" => $NoAngle
                ]
            );
        }
        $Color = $this->allocateColor($this->Picture, $R, $G, $B, $Alpha);
        if ($NoAngle) {
            imagefilledrectangle($this->Picture, ceil($X1) + 1, ceil($Y1), floor($X2) - 1, floor($Y2), $Color);
            imageline($this->Picture, ceil($X1), ceil($Y1) + 1, ceil($X1), floor($Y2) - 1, $Color);
            imageline($this->Picture, floor($X2), ceil($Y1) + 1, floor($X2), floor($Y2) - 1, $Color);
        } else {
            imagefilledrectangle($this->Picture, ceil($X1), ceil($Y1), floor($X2), floor($Y2), $Color);
        }
        if ($Dash) {
            if ($BorderR != -1) {
                $iX1 = $X1 + 1;
                $iY1 = $Y1 + 1;
                $iX2 = $X2 - 1;
                $iY2 = $Y2 - 1;
            } else {
                $iX1 = $X1;
                $iY1 = $Y1;
                $iX2 = $X2;
                $iY2 = $Y2;
            }
            $Color = $this->allocateColor($this->Picture, $DashR, $DashG, $DashB, $Alpha);
            $Y = $iY1 - $DashStep;
            for ($X = $iX1; $X <= $iX2 + ($iY2 - $iY1); $X = $X + $DashStep) {
                $Y = $Y + $DashStep;
                if ($X > $iX2) {
                    $Xa = $X - ($X - $iX2);
                    $Ya = $iY1 + ($X - $iX2);
                } else {
                    $Xa = $X;
                    $Ya = $iY1;
                }
                if ($Y > $iY2) {
                    $Xb = $iX1 + ($Y - $iY2);
                    $Yb = $Y - ($Y - $iY2);
                } else {
                    $Xb = $iX1;
                    $Yb = $Y;
                }
                imageline($this->Picture, $Xa, $Ya, $Xb, $Yb, $Color);
            }
        }
        if ($this->Antialias && !$NoBorder) {
            if ($X1 < ceil($X1)) {
                $AlphaA = $Alpha * (ceil($X1) - $X1);
                $Color = $this->allocateColor($this->Picture, $R, $G, $B, $AlphaA);
                imageline($this->Picture, ceil($X1) - 1, ceil($Y1), ceil($X1) - 1, floor($Y2), $Color);
            }
            if ($Y1 < ceil($Y1)) {
                $AlphaA = $Alpha * (ceil($Y1) - $Y1);
                $Color = $this->allocateColor($this->Picture, $R, $G, $B, $AlphaA);
                imageline($this->Picture, ceil($X1), ceil($Y1) - 1, floor($X2), ceil($Y1) - 1, $Color);
            }
            if ($X2 > floor($X2)) {
                $AlphaA = $Alpha * (.5 - ($X2 - floor($X2)));
                $Color = $this->allocateColor($this->Picture, $R, $G, $B, $AlphaA);
                imageline($this->Picture, floor($X2) + 1, ceil($Y1), floor($X2) + 1, floor($Y2), $Color);
            }
            if ($Y2 > floor($Y2)) {
                $AlphaA = $Alpha * (.5 - ($Y2 - floor($Y2)));
                $Color = $this->allocateColor($this->Picture, $R, $G, $B, $AlphaA);
                imageline($this->Picture, ceil($X1), floor($Y2) + 1, floor($X2), floor($Y2) + 1, $Color);
            }
        }
        if ($BorderR != -1) {
            $this->drawRectangle(
                $X1,
                $Y1,
                $X2,
                $Y2,
                [
                    "r" => $BorderR,
                    "G" => $BorderG,
                    "B" => $BorderB,
                    "Alpha" => $BorderAlpha,
                    "Ticks" => $Ticks,
                    "NoAngle" => $NoAngle
                ]
            );
        }
        $this->Shadow = $RestoreShadow;
    }

}

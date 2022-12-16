<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 30, 2019, 2:18:02 AM
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
        '0' => ['r' => 188, 'g' => 224, 'b' => 46, 'alpha' => 100],
        '1' => ['r' => 224, 'g' => 100, 'b' => 46, 'alpha' => 100],
        '2' => ['r' => 224, 'g' => 214, 'b' => 46, 'alpha' => 100],
        '3' => ['r' => 46, 'g' => 151, 'b' => 224, 'alpha' => 100],
        '4' => ['r' => 176, 'g' => 46, 'b' => 224, 'alpha' => 100],
        '5' => ['r' => 224, 'g' => 46, 'b' => 117, 'alpha' => 100],
        '6' => ['r' => 92, 'g' => 224, 'b' => 46, 'alpha' => 100],
        '7' => ['r' => 224, 'g' => 176, 'b' => 46, 'alpha' => 100]
    ];

    public function __construct() {
        $this->data['xAxisDisplay'] = Constant::AXIS_FORMAT_DEFAULT;
        $this->data['xAxisFormat'] = null;
        $this->data['xAxisName'] = null;
        $this->data['xAxisUnit'] = null;
        $this->data['abscissa'] = null;
        $this->data['absicssaPosition'] = Constant::AXIS_POSITION_BOTTOM;
        $this->data['axis'][0]['display'] = Constant::AXIS_FORMAT_DEFAULT;
        $this->data['axis'][0]['position'] = Constant::AXIS_POSITION_LEFT;
        $this->data['axis'][0]['identity'] = Constant::AXIS_Y;
    }

    /**
     * Add a single point or an array to the given serie.
     *
     * @param mixed  $values
     * @param string $serieName
     *
     * @return int
     */
    public function addPoints($values, $serieName = 'Serie1') {
        if (!isset($this->data['series'][$serieName])) {
            $this->initialise($serieName);
        }
        if (is_array($values)) {
            foreach ($values as $value) {
                $this->data['series'][$serieName]['data'][] = $value;
            }
        } else {
            $this->data['series'][$serieName]['data'][] = $values;
        }
        if ($values != Constant::VOID) {
            $strippedData = $this->stripVOID($this->data['series'][$serieName]['data']);
            if (empty($strippedData)) {
                $this->data['series'][$serieName]['max'] = 0;
                $this->data['series'][$serieName]['min'] = 0;

                return 0;
            }
            $this->data['series'][$serieName]['max'] = max($strippedData);
            $this->data['series'][$serieName]['min'] = min($strippedData);
        }
    }

    /**
     * Strip VOID values.
     *
     * @param mixed $values
     *
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
     * Return the number of values contained in a given serie.
     *
     * @param string $serie
     *
     * @return int
     */
    public function getSerieCount($serie) {
        if (isset($this->data['series'][$serie]['data'])) {
            return sizeof($this->data['series'][$serie]['data']);
        }

        return 0;
    }

    /**
     * Remove a serie from the pData object.
     *
     * @param mixed $series
     */
    public function removeSerie($series) {
        if (!is_array($series)) {
            $series = $this->convertToArray($series);
        }
        foreach ($series as $serie) {
            if (isset($this->data['series'][$serie])) {
                unset($this->data['series'][$serie]);
            }
        }
    }

    /**
     * Return a value from given serie & index.
     *
     * @param string $serie
     * @param int    $Index
     *
     * @return mixed
     */
    public function getValueAt($serie, $Index = 0) {
        if (isset($this->data['series'][$serie]['data'][$Index])) {
            return $this->data['series'][$serie]['data'][$Index];
        }

        return null;
    }

    /**
     * Return the values array.
     *
     * @param string $serie
     *
     * @return mixed
     */
    public function getValues($serie) {
        if (isset($this->data['series'][$serie]['data'])) {
            return $this->data['series'][$serie]['data'];
        }

        return null;
    }

    /**
     * Reverse the values in the given serie.
     *
     * @param mixed $series
     */
    public function reverseSerie($series) {
        if (!is_array($series)) {
            $series = $this->convertToArray($series);
        }
        foreach ($series as $serie) {
            if (isset($this->data['series'][$serie]['data'])) {
                $this->data['series'][$serie]['data'] = array_reverse(
                    $this->data['series'][$serie]['data']
                );
            }
        }
    }

    /**
     * Return the sum of the serie values.
     *
     * @param string $serie
     *
     * @return null|int
     */
    public function getSum($serie) {
        if (isset($this->data['series'][$serie])) {
            return array_sum($this->data['series'][$serie]['data']);
        }

        return null;
    }

    /**
     * Return the max value of a given serie.
     *
     * @param string $serie
     *
     * @return mixed
     */
    public function getMax($serie) {
        if (isset($this->data['series'][$serie]['max'])) {
            return $this->data['series'][$serie]['max'];
        }

        return null;
    }

    /**
     * @param string $serie
     *
     * @return mixed
     */
    public function getMin($serie) {
        if (isset($this->data['series'][$serie]['min'])) {
            return $this->data['series'][$serie]['min'];
        }

        return null;
    }

    /**
     * Set the description of a given serie.
     *
     * @param mixed  $series
     * @param string $shape
     */
    public function setSerieShape($series, $shape = Constant::SERIE_SHAPE_FILLEDCIRCLE) {
        if (!is_array($series)) {
            $series = $this->convertToArray($series);
        }
        foreach ($series as $serie) {
            if (isset($this->data['series'][$serie])) {
                $this->data['series'][$serie]['shape'] = $shape;
            }
        }
    }

    /**
     * Set the description of a given serie.
     *
     * @param string|array $series
     * @param string       $description
     */
    public function setSerieDescription($series, $description = 'My serie') {
        if (!is_array($series)) {
            $series = $this->convertToArray($series);
        }
        foreach ($series as $serie) {
            if (isset($this->data['series'][$serie])) {
                $this->data['series'][$serie]['description'] = $description;
            }
        }
    }

    /**
     * Set a serie as "drawable" while calling a rendering public function.
     *
     * @param string|array $series
     * @param bool         $drawable
     */
    public function setSerieDrawable($series, $drawable = true) {
        if (!is_array($series)) {
            $series = $this->convertToArray($series);
        }
        foreach ($series as $serie) {
            if (isset($this->data['series'][$serie])) {
                $this->data['series'][$serie]['isDrawable'] = $drawable;
            }
        }
    }

    /**
     * Set the icon associated to a given serie.
     *
     * @param mixed $series
     * @param mixed $picture
     */
    public function setSeriePicture($series, $picture = null) {
        if (!is_array($series)) {
            $series = $this->convertToArray($series);
        }
        foreach ($series as $serie) {
            if (isset($this->data['series'][$serie])) {
                $this->data['series'][$serie]['picture'] = $picture;
            }
        }
    }

    /**
     * Set the name of the X Axis.
     *
     * @param string $name
     */
    public function setXAxisName($name) {
        $this->data['xAxisName'] = $name;
    }

    /**
     * Set the display mode of the  X Axis.
     *
     * @param int   $mode
     * @param array $format
     */
    public function setXAxisDisplay($mode, $format = null) {
        $this->data['xAxisDisplay'] = $mode;
        $this->data['xAxisFormat'] = $format;
    }

    /**
     * Set the unit that will be displayed on the X axis.
     *
     * @param string $Unit
     */
    public function setXAxisUnit($Unit) {
        $this->data['xAxisUnit'] = $Unit;
    }

    /**
     * Set the serie that will be used as abscissa.
     *
     * @param string $serie
     */
    public function setAbscissa($serie) {
        if (isset($this->data['series'][$serie])) {
            $this->data['abscissa'] = $serie;
        }
    }

    /**
     * Set the position of the abscissa axis.
     *
     * @param int $position
     */
    public function setAbsicssaPosition($position = Constant::AXIS_POSITION_BOTTOM) {
        $this->data['absicssaPosition'] = $position;
    }

    /**
     * Set the name of the abscissa axis.
     *
     * @param string $name
     */
    public function setAbscissaName($name) {
        $this->data['abscissaName'] = $name;
    }

    /**
     * Create a scatter group specified in X and Y data series.
     *
     * @param string $serieX
     * @param string $serieY
     * @param int    $id
     */
    public function setScatterSerie($serieX, $serieY, $id = 0) {
        if (isset($this->data['series'][$serieX], $this->data['series'][$serieY])) {
            $this->initScatterSerie($id);
            $this->data['scatterSeries'][$id]['x'] = $serieX;
            $this->data['scatterSeries'][$id]['y'] = $serieY;
        }
    }

    /**
     *  Set the shape of a given sctatter serie.
     *
     * @param int $id
     * @param int $shape
     */
    public function setScatterSerieShape($id, $shape = Constant::SERIE_SHAPE_FILLEDCIRCLE) {
        if (isset($this->data['scatterSeries'][$id])) {
            $this->data['scatterSeries'][$id]['shape'] = $shape;
        }
    }

    /**
     * Set the description of a given scatter serie.
     *
     * @param int    $id
     * @param string $description
     */
    public function setScatterSerieDescription($id, $description = 'My serie') {
        if (isset($this->data['scatterSeries'][$id])) {
            $this->data['scatterSeries'][$id]['description'] = $description;
        }
    }

    /**
     * Set the icon associated to a given scatter serie.
     *
     * @param int   $id
     * @param mixed $picture
     */
    public function setScatterSeriePicture($id, $picture = null) {
        if (isset($this->data['scatterSeries'][$id])) {
            $this->data['scatterSeries'][$id]['picture'] = $picture;
        }
    }

    /**
     * Set a scatter serie as "drawable" while calling a rendering public function.
     *
     * @param int  $id
     * @param bool $drawable
     */
    public function setScatterSerieDrawable($id, $drawable = true) {
        if (isset($this->data['scatterSeries'][$id])) {
            $this->data['scatterSeries'][$id]['isDrawable'] = $drawable;
        }
    }

    /**
     * Define if a scatter serie should be draw with ticks.
     *
     * @param int $id
     * @param int $Width
     */
    public function setScatterSerieTicks($id, $Width = 0) {
        if (isset($this->data['scatterSeries'][$id])) {
            $this->data['scatterSeries'][$id]['ticks'] = $Width;
        }
    }

    /**
     * Define if a scatter serie should be draw with a special weight.
     *
     * @param int $id
     * @param int $Weight
     */
    public function setScatterSerieWeight($id, $Weight = 0) {
        if (isset($this->data['scatterSeries'][$id])) {
            $this->data['scatterSeries'][$id]['weight'] = $Weight;
        }
    }

    /**
     * Associate a color to a scatter serie.
     *
     * @param int   $id
     * @param array $format
     */
    public function setScatterSerieColor($id, array $format) {
        $r = isset($format['r']) ? $format['r'] : 0;
        $g = isset($format['g']) ? $format['g'] : 0;
        $b = isset($format['b']) ? $format['b'] : 0;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 100;
        if (isset($this->data['scatterSeries'][$id])) {
            $this->data['scatterSeries'][$id]['color']['r'] = $r;
            $this->data['scatterSeries'][$id]['color']['g'] = $g;
            $this->data['scatterSeries'][$id]['color']['b'] = $b;
            $this->data['scatterSeries'][$id]['color']['alpha'] = $alpha;
        }
    }

    /**
     * Compute the series limits for an individual and global point of view.
     *
     * @return array
     */
    public function limits() {
        $globalMin = Constant::ABSOLUTE_MAX;
        $globalMax = Constant::ABSOLUTE_MIN;
        foreach (array_keys($this->data['series']) as $key) {
            if ($this->data['abscissa'] != $key && $this->data['series'][$key]['isDrawable'] == true
            ) {
                if ($globalMin > $this->data['series'][$key]['min']) {
                    $globalMin = $this->data['series'][$key]['min'];
                }
                if ($globalMax < $this->data['series'][$key]['max']) {
                    $globalMax = $this->data['series'][$key]['max'];
                }
            }
        }
        $this->data['min'] = $globalMin;
        $this->data['max'] = $globalMax;

        return [$globalMin, $globalMax];
    }

    /**
     * Mark all series as drawable.
     */
    public function drawAll() {
        foreach (array_keys($this->data['series']) as $key) {
            if ($this->data['abscissa'] != $key) {
                $this->data['series'][$key]['isDrawable'] = true;
            }
        }
    }

    /**
     * Return the average value of the given serie.
     *
     * @param string $serie
     *
     * @return null|int
     */
    public function getSerieAverage($serie) {
        if (isset($this->data['series'][$serie])) {
            $serieData = $this->stripVOID($this->data['series'][$serie]['data']);

            return array_sum($serieData) / sizeof($serieData);
        }

        return null;
    }

    /**
     * Return the geometric mean of the given serie.
     *
     * @param string $serie
     *
     * @return null|int
     */
    public function getGeometricMean($serie) {
        if (isset($this->data['series'][$serie])) {
            $serieData = $this->stripVOID($this->data['series'][$serie]['data']);
            $seriesum = 1;
            foreach ($serieData as $value) {
                $seriesum = $seriesum * $value;
            }

            return pow($seriesum, 1 / sizeof($serieData));
        }

        return null;
    }

    /**
     * Return the harmonic mean of the given serie.
     *
     * @param string $serie
     *
     * @return null|int
     */
    public function getHarmonicMean($serie) {
        if (isset($this->data['series'][$serie])) {
            $serieData = $this->stripVOID($this->data['series'][$serie]['data']);
            $seriesum = 0;
            foreach ($serieData as $value) {
                $seriesum = $seriesum + 1 / $value;
            }

            return sizeof($serieData) / $seriesum;
        }

        return null;
    }

    /**
     * Return the standard deviation of the given serie.
     *
     * @param string $serie
     *
     * @return null|float
     */
    public function getStandardDeviation($serie) {
        if (isset($this->data['series'][$serie])) {
            $average = $this->getSerieAverage($serie);
            $serieData = $this->stripVOID($this->data['series'][$serie]['data']);
            $deviationSum = 0;
            foreach ($serieData as $key => $value) {
                $deviationSum = $deviationSum + ($value - $average) * ($value - $average);
            }

            return sqrt($deviationSum / count($serieData));
        }

        return null;
    }

    /**
     * Return the Coefficient of variation of the given serie.
     *
     * @param string $serie
     *
     * @return null|float
     */
    public function getCoefficientOfVariation($serie) {
        if (isset($this->data['series'][$serie])) {
            $average = $this->getSerieAverage($serie);
            $standardDeviation = $this->getStandardDeviation($serie);
            if ($standardDeviation != 0) {
                return $standardDeviation / $average;
            }
        }

        return null;
    }

    /**
     * Return the median value of the given serie.
     *
     * @param string $serie
     *
     * @return int|float
     */
    public function getSerieMedian($serie) {
        if (isset($this->data['series'][$serie])) {
            $serieData = $this->stripVOID($this->data['series'][$serie]['data']);
            sort($serieData);
            $serieCenter = floor(sizeof($serieData) / 2);
            if (isset($serieData[$serieCenter])) {
                return $serieData[$serieCenter];
            }
        }

        return null;
    }

    /**
     * Return the x th percentil of the given serie.
     *
     * @param string $serie
     * @param int    $percentil
     *
     * @return null|int|float
     */
    public function getSeriePercentile($serie = 'Serie1', $percentil = 95) {
        if (!isset($this->data['series'][$serie]['data'])) {
            return null;
        }
        $values = count($this->data['series'][$serie]['data']) - 1;
        if ($values < 0) {
            $values = 0;
        }
        $percentilID = floor(($values / 100) * $percentil + .5);
        $sortedValues = $this->data['series'][$serie]['data'];
        sort($sortedValues);
        if (is_numeric($sortedValues[$percentilID])) {
            return $sortedValues[$percentilID];
        }

        return null;
    }

    /**
     * Add random values to a given serie.
     *
     * @param string $serieName
     * @param array  $options
     */
    public function addRandomValues($serieName = 'Serie1', array $options = []) {
        $values = isset($options['Values']) ? $options['Values'] : 20;
        $min = isset($options['min']) ? $options['min'] : 0;
        $max = isset($options['max']) ? $options['max'] : 100;
        $withFloat = isset($options['withFloat']) ? $options['withFloat'] : false;
        for ($i = 0; $i <= $values; $i++) {
            $value = $withFloat ? rand($min * 100, $max * 100) / 100 : rand($min, $max);
            $this->addPoints($value, $serieName);
        }
    }

    /**
     * Test if we have valid data.
     *
     * @return null|bool
     */
    public function containsData() {
        if (!isset($this->data['series'])) {
            return false;
        }
        foreach (array_keys($this->data['series']) as $key) {
            if ($this->data['abscissa'] != $key && $this->data['series'][$key]['isDrawable'] == true
            ) {
                return true;
            }
        }

        return null;
    }

    /**
     * Set the display mode of an Axis.
     *
     * @param int   $axisID
     * @param int   $mode
     * @param array $format
     */
    public function setAxisDisplay($axisID, $mode = Constant::AXIS_FORMAT_DEFAULT, $format = null) {
        if (isset($this->data['axis'][$axisID])) {
            $this->data['axis'][$axisID]['display'] = $mode;
            if ($format != null) {
                $this->data['axis'][$axisID]['Format'] = $format;
            }
        }
    }

    /**
     * Set the position of an Axis.
     *
     * @param int $axisID
     * @param int $position
     */
    public function setAxisPosition($axisID, $position = Constant::AXIS_POSITION_LEFT) {
        if (isset($this->data['axis'][$axisID])) {
            $this->data['axis'][$axisID]['position'] = $position;
        }
    }

    /**
     * Associate an unit to an axis.
     *
     * @param int    $axisID
     * @param string $Unit
     */
    public function setAxisUnit($axisID, $Unit) {
        if (isset($this->data['axis'][$axisID])) {
            $this->data['axis'][$axisID]['unit'] = $Unit;
        }
    }

    /**
     * Associate a name to an axis.
     *
     * @param int    $axisID
     * @param string $name
     */
    public function setAxisName($axisID, $name) {
        if (isset($this->data['axis'][$axisID])) {
            $this->data['axis'][$axisID]['Name'] = $name;
        }
    }

    /**
     * Associate a color to an axis.
     *
     * @param int   $axisID
     * @param array $format
     */
    public function setAxisColor($axisID, array $format) {
        $r = isset($format['r']) ? $format['r'] : 0;
        $g = isset($format['g']) ? $format['g'] : 0;
        $b = isset($format['b']) ? $format['b'] : 0;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 100;
        if (isset($this->data['axis'][$axisID])) {
            $this->data['axis'][$axisID]['color']['r'] = $r;
            $this->data['axis'][$axisID]['color']['g'] = $g;
            $this->data['axis'][$axisID]['color']['b'] = $b;
            $this->data['axis'][$axisID]['color']['alpha'] = $alpha;
        }
    }

    /**
     * Design an axis as X or Y member.
     *
     * @param int $axisID
     * @param int $Identity
     */
    public function setAxisXY($axisID, $Identity = Constant::AXIS_Y) {
        if (isset($this->data['axis'][$axisID])) {
            $this->data['axis'][$axisID]['identity'] = $Identity;
        }
    }

    /**
     * Associate one data serie with one axis.
     *
     * @param mixed $series
     * @param int   $axisID
     */
    public function setSerieOnAxis($series, $axisID) {
        if (!is_array($series)) {
            $series = $this->convertToArray($series);
        }
        foreach ($series as $serie) {
            $previousAxis = $this->data['series'][$serie]['axis'];
            /* Create missing axis */
            if (!isset($this->data['axis'][$axisID])) {
                $this->data['axis'][$axisID]['position'] = Constant::AXIS_POSITION_LEFT;
                $this->data['axis'][$axisID]['identity'] = Constant::AXIS_Y;
            }
            $this->data['series'][$serie]['axis'] = $axisID;
            /* Cleanup unused axis */
            $found = false;
            foreach ($this->data['series'] as $values) {
                if ($values['axis'] == $previousAxis) {
                    $found = true;
                }
            }
            if (!$found) {
                unset($this->data['axis'][$previousAxis]);
            }
        }
    }

    /**
     * Define if a serie should be draw with ticks.
     *
     * @param mixed $series
     * @param int   $Width
     */
    public function setSerieTicks($series, $Width = 0) {
        if (!is_array($series)) {
            $series = $this->convertToArray($series);
        }
        foreach ($series as $serie) {
            if (isset($this->data['series'][$serie])) {
                $this->data['series'][$serie]['ticks'] = $Width;
            }
        }
    }

    /**
     * Define if a serie should be draw with a special weight.
     *
     * @param mixed $series
     * @param int   $Weight
     */
    public function setSerieWeight($series, $Weight = 0) {
        if (!is_array($series)) {
            $series = $this->convertToArray($series);
        }
        foreach ($series as $serie) {
            if (isset($this->data['series'][$serie])) {
                $this->data['series'][$serie]['weight'] = $Weight;
            }
        }
    }

    /**
     * Returns the palette of the given serie.
     *
     * @param type $serie
     *
     * @return null
     */
    public function getSeriePalette($serie) {
        if (!isset($this->data['series'][$serie])) {
            return null;
        }
        $result = [];
        $result['r'] = $this->data['series'][$serie]['color']['r'];
        $result['g'] = $this->data['series'][$serie]['color']['g'];
        $result['b'] = $this->data['series'][$serie]['color']['b'];
        $result['alpha'] = $this->data['series'][$serie]['color']['alpha'];

        return $result;
    }

    /**
     * Set the color of one serie.
     *
     * @param mixed $series
     * @param array $format
     */
    public function setPalette($series, array $format = []) {
        if (!is_array($series)) {
            $series = $this->convertToArray($series);
        }
        foreach ($series as $key => $serie) {
            $r = isset($format['r']) ? $format['r'] : 0;
            $g = isset($format['g']) ? $format['g'] : 0;
            $b = isset($format['b']) ? $format['b'] : 0;
            $alpha = isset($format['alpha']) ? $format['alpha'] : 100;
            if (isset($this->data['series'][$serie])) {
                $oldR = $this->data['series'][$serie]['color']['r'];
                $oldG = $this->data['series'][$serie]['color']['g'];
                $oldB = $this->data['series'][$serie]['color']['b'];
                $this->data['series'][$serie]['color']['r'] = $r;
                $this->data['series'][$serie]['color']['g'] = $g;
                $this->data['series'][$serie]['color']['b'] = $b;
                $this->data['series'][$serie]['color']['alpha'] = $alpha;
                /* Do reverse processing on the internal palette array */
                foreach ($this->palette as $key => $value) {
                    if ($value['r'] == $oldR && $value['g'] == $oldG && $value['b'] == $oldB) {
                        $this->palette[$key]['r'] = $r;
                        $this->palette[$key]['g'] = $g;
                        $this->palette[$key]['b'] = $b;
                        $this->palette[$key]['alpha'] = $alpha;
                    }
                }
            }
        }
    }

    /**
     * Load a palette file.
     *
     * @param string $fileName
     * @param bool   $overwrite
     *
     * @throws Exception
     */
    public function loadPalette($fileName, $overwrite = false) {
        $path = file_exists($fileName) ? $fileName : sprintf('%s/../resources/palettes/%s', __DIR__, ltrim($fileName, '/'));
        $fileHandle = @fopen($path, 'r');
        if (!$fileHandle) {
            throw new Exception(sprintf(
                'The requested palette "%s" was not found at path "%s"!',
                $fileName,
                $path
            ));
        }
        if ($overwrite) {
            $this->palette = [];
        }
        while (!feof($fileHandle)) {
            $line = fgets($fileHandle, 4096);
            if (false === $line) {
                continue;
            }
            $row = explode(',', $line);
            if (empty($row)) {
                continue;
            }
            if (count($row) !== 4) {
                throw new RuntimeException(sprintf(
                    'A palette row must supply R, G, B and Alpha components, %s given!',
                    var_export($row, true)
                ));
            }
            list($r, $g, $b, $alpha) = $row;
            $id = count($this->palette);
            $this->palette[$id] = [
                'r' => trim($r),
                'g' => trim($g),
                'b' => trim($b),
                'alpha' => trim($alpha)
            ];
        }
        fclose($fileHandle);
        /* Apply changes to current series */
        $id = 0;
        if (isset($this->data['series'])) {
            foreach ($this->data['series'] as $key => $value) {
                if (!isset($this->palette[$id])) {
                    $this->data['series'][$key]['color'] = ['r' => 0, 'g' => 0, 'b' => 0, 'alpha' => 0];
                } else {
                    $this->data['series'][$key]['color'] = $this->palette[$id];
                }
                $id++;
            }
        }
    }

    /**
     * Initialise a given scatter serie.
     *
     * @param int $id
     *
     * @return null
     */
    public function initScatterSerie($id) {
        if (isset($this->data['scatterSeries'][$id])) {
            return null;
        }
        $this->data['scatterSeries'][$id]['description'] = 'Scatter ' . $id;
        $this->data['scatterSeries'][$id]['isDrawable'] = true;
        $this->data['scatterSeries'][$id]['picture'] = null;
        $this->data['scatterSeries'][$id]['ticks'] = 0;
        $this->data['scatterSeries'][$id]['weight'] = 0;
        if (isset($this->palette[$id])) {
            $this->data['scatterSeries'][$id]['color'] = $this->palette[$id];
        } else {
            $this->data['scatterSeries'][$id]['color']['r'] = rand(0, 255);
            $this->data['scatterSeries'][$id]['color']['g'] = rand(0, 255);
            $this->data['scatterSeries'][$id]['color']['b'] = rand(0, 255);
            $this->data['scatterSeries'][$id]['color']['alpha'] = 100;
        }
    }

    /**
     * Initialise a given serie.
     *
     * @param string $serie
     */
    public function initialise($serie) {
        $id = 0;
        if (isset($this->data['series'])) {
            $id = count($this->data['series']);
        }
        $this->data['series'][$serie]['description'] = $serie;
        $this->data['series'][$serie]['isDrawable'] = true;
        $this->data['series'][$serie]['picture'] = null;
        $this->data['series'][$serie]['max'] = null;
        $this->data['series'][$serie]['min'] = null;
        $this->data['series'][$serie]['axis'] = 0;
        $this->data['series'][$serie]['ticks'] = 0;
        $this->data['series'][$serie]['weight'] = 0;
        $this->data['series'][$serie]['shape'] = Constant::SERIE_SHAPE_FILLEDCIRCLE;
        if (isset($this->palette[$id])) {
            $this->data['series'][$serie]['color'] = $this->palette[$id];
        } else {
            $this->data['series'][$serie]['color']['r'] = rand(0, 255);
            $this->data['series'][$serie]['color']['g'] = rand(0, 255);
            $this->data['series'][$serie]['color']['b'] = rand(0, 255);
            $this->data['series'][$serie]['color']['alpha'] = 100;
        }
        $this->data['series'][$serie]['data'] = [];
    }

    /**
     * @param int   $normalizationFactor
     * @param mixed $UnitChange
     * @param int   $round
     */
    public function normalize($normalizationFactor = 100, $UnitChange = null, $round = 1) {
        $abscissa = $this->data['abscissa'];
        $selectedSeries = [];
        $maxVal = 0;
        foreach (array_keys($this->data['axis']) as $axisID) {
            if ($UnitChange != null) {
                $this->data['axis'][$axisID]['unit'] = $UnitChange;
            }
            foreach ($this->data['series'] as $serieName => $serie) {
                if ($serie['axis'] == $axisID && $serie['isDrawable'] == true && $serieName != $abscissa
                ) {
                    $selectedSeries[$serieName] = $serieName;
                    if (count($serie['data']) > $maxVal) {
                        $maxVal = count($serie['data']);
                    }
                }
            }
        }
        for ($i = 0; $i <= $maxVal - 1; $i++) {
            $factor = 0;
            foreach ($selectedSeries as $key => $serieName) {
                $value = $this->data['series'][$serieName]['data'][$i];
                if ($value != Constant::VOID) {
                    $factor = $factor + abs($value);
                }
            }
            if ($factor != 0) {
                $factor = $normalizationFactor / $factor;
                foreach ($selectedSeries as $key => $serieName) {
                    $value = $this->data['series'][$serieName]['data'][$i];
                    if ($value != Constant::VOID && $factor != $normalizationFactor) {
                        $this->data['series'][$serieName]['data'][$i] = round(abs($value) * $factor, $round);
                    } elseif ($value == Constant::VOID || $value == 0) {
                        $this->data['series'][$serieName]['data'][$i] = Constant::VOID;
                    } elseif ($factor == $normalizationFactor) {
                        $this->data['series'][$serieName]['data'][$i] = $normalizationFactor;
                    }
                }
            }
        }
        foreach ($selectedSeries as $key => $serieName) {
            $this->data['series'][$serieName]['max'] = max(
                $this->stripVOID($this->data['series'][$serieName]['data'])
            );
            $this->data['series'][$serieName]['min'] = min(
                $this->stripVOID($this->data['series'][$serieName]['data'])
            );
        }
    }

    /**
     * Load data from a CSV (or similar) data source.
     *
     * @param string $fileName
     * @param array  $options
     */
    public function importFromCSV($fileName, array $options = []) {
        $delimiter = isset($options['delimiter']) ? $options['delimiter'] : ',';
        $gotHeader = isset($options['GotHeader']) ? $options['GotHeader'] : false;
        $skipColumns = isset($options['skipColumns']) ? $options['skipColumns'] : [-1];
        $defaultSerieName = isset($options['defaultSerieName']) ? $options['defaultSerieName'] : 'Serie';
        $Handle = @fopen($fileName, 'r');
        if ($Handle) {
            $HeaderParsed = false;
            $serieNames = [];
            while (!feof($Handle)) {
                $buffer = fgets($Handle, 4096);
                $buffer = str_replace(chr(10), '', $buffer);
                $buffer = str_replace(chr(13), '', $buffer);
                $values = preg_split('/' . $delimiter . '/', $buffer);
                if ($buffer != '') {
                    if ($gotHeader && !$HeaderParsed) {
                        foreach ($values as $key => $name) {
                            if (!in_array($key, $skipColumns)) {
                                $serieNames[$key] = $name;
                            }
                        }
                        $HeaderParsed = true;
                    } else {
                        if (!count($serieNames)) {
                            foreach ($values as $key => $name) {
                                if (!in_array($key, $skipColumns)) {
                                    $serieNames[$key] = $defaultSerieName . $key;
                                }
                            }
                        }
                        foreach ($values as $key => $value) {
                            if (!in_array($key, $skipColumns)) {
                                $this->addPoints($value, $serieNames[$key]);
                            }
                        }
                    }
                }
            }
            fclose($Handle);
        }
    }

    /**
     * Create a dataset based on a formula.
     *
     * @param string $serieName
     * @param string $formula
     * @param array  $options
     *
     * @return null
     */
    public function createFunctionSerie($serieName, $formula = '', array $options = []) {
        $minX = isset($options['minX']) ? $options['minX'] : -10;
        $maxX = isset($options['maxX']) ? $options['maxX'] : 10;
        $XStep = isset($options['xStep']) ? $options['xStep'] : 1;
        $autoDescription = isset($options['autoDescription']) ? $options['autoDescription'] : false;
        $recordAbscissa = isset($options['RecordAbscissa']) ? $options['RecordAbscissa'] : false;
        $abscissaSerie = isset($options['abscissaSerie']) ? $options['abscissaSerie'] : 'Abscissa';
        if ($formula == '') {
            return null;
        }
        $result = [];
        $abscissa = [];
        for ($i = $minX; $i <= $maxX; $i = $i + $XStep) {
            $expression = "\$return = '!'.(" . str_replace('z', $i, $formula) . ');';
            if (@eval($expression) === false) {
                $return = Constant::VOID;
            }
            if ($return == '!') {
                $return = Constant::VOID;
            } else {
                $return = $this->right($return, strlen($return) - 1);
            }
            if ($return == 'NAN') {
                $return = Constant::VOID;
            }
            if ($return == 'INF') {
                $return = Constant::VOID;
            }
            if ($return == '-INF') {
                $return = Constant::VOID;
            }
            $abscissa[] = $i;
            $result[] = $return;
        }
        $this->addPoints($result, $serieName);
        if ($autoDescription) {
            $this->setSerieDescription($serieName, $formula);
        }
        if ($recordAbscissa) {
            $this->addPoints($abscissa, $abscissaSerie);
        }
    }

    /**
     * @param mixed $series
     */
    public function negateValues($series) {
        if (!is_array($series)) {
            $series = $this->convertToArray($series);
        }
        foreach ($series as $key => $serieName) {
            if (isset($this->data['series'][$serieName])) {
                $data = [];
                foreach ($this->data['series'][$serieName]['data'] as $key => $value) {
                    if ($value == Constant::VOID) {
                        $data[] = Constant::VOID;
                    } else {
                        $data[] = -$value;
                    }
                }
                $this->data['series'][$serieName]['data'] = $data;
                $this->data['series'][$serieName]['max'] = max(
                    $this->stripVOID($this->data['series'][$serieName]['data'])
                );
                $this->data['series'][$serieName]['min'] = min(
                    $this->stripVOID($this->data['series'][$serieName]['data'])
                );
            }
        }
    }

    /**
     * Return the data & configuration of the series.
     *
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Save a palette element.
     *
     * @param int    $id
     * @param string $Color
     */
    public function savePalette($id, $Color) {
        $this->palette[$id] = $Color;
    }

    /**
     * Return the palette of the series.
     *
     * @return array
     */
    public function getPalette() {
        return $this->palette;
    }

    /**
     * Called by the scaling algorithm to save the config.
     *
     * @param mixed $axis
     */
    public function saveAxisConfig($axis) {
        $this->data['axis'] = $axis;
    }

    /**
     * Save the Y Margin if set.
     *
     * @param mixed $value
     */
    public function saveYMargin($value) {
        $this->data['yMargin'] = $value;
    }

    /**
     * Save extended configuration to the pData object.
     *
     * @param string $tag
     * @param mixed  $values
     */
    public function saveExtendedData($tag, $values) {
        $this->data['extended'][$tag] = $values;
    }

    /**
     * Called by the scaling algorithm to save the orientation of the scale.
     *
     * @param mixed $orientation
     */
    public function saveOrientation($orientation) {
        $this->data['orientation'] = $orientation;
    }

    /**
     * Convert a string to a single elements array.
     *
     * @param mixed $value
     *
     * @return array
     */
    public function convertToArray($value) {
        return [$value];
    }

    /**
     * Class string wrapper.
     *
     * @return string
     */
    public function __toString() {
        return 'pData object.';
    }

    /**
     * @param string $value
     * @param int    $nbChar
     *
     * @return string
     */
    public function left($value, $nbChar) {
        return substr($value, 0, $nbChar);
    }

    /**
     * @param string $value
     * @param int    $nbChar
     *
     * @return string
     */
    public function right($value, $nbChar) {
        return substr($value, strlen($value) - $nbChar, $nbChar);
    }

    /**
     * @param string $value
     * @param int    $depart
     * @param int    $nbChar
     *
     * @return string
     */
    public function mid($value, $depart, $nbChar) {
        return substr($value, $depart - 1, $nbChar);
    }
}

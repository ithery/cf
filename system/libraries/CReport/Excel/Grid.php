<?php

/**
 * Grid model for excel export, built from the draw operations recorded by the excel processor.
 *
 * Mimics the JasperReports xlsx exporter: every distinct element edge becomes a
 * column/row boundary (the "cuts" algorithm), each element is mapped to the cell
 * range it spans and merged, so the sheet layout follows the report layout.
 */
class CReport_Excel_Grid {
    /**
     * Column widths in points, keyed by 1-based column index.
     *
     * @var array
     */
    public $columnWidths = [];

    /**
     * Row heights in points, keyed by 1-based row index.
     *
     * @var array
     */
    public $rowHeights = [];

    /**
     * Text cells. Each item:
     * row, col, rowSpan, colSpan, value, numeric (bool), format (excel format code or null), style (array).
     *
     * @var array
     */
    public $cells = [];

    /**
     * Rectangle outlines. Each item: fromRow, fromCol, toRow, toCol, border (style array).
     *
     * @var array
     */
    public $outlines = [];

    /**
     * Line borders. Each item: row, fromCol, toCol (or col, fromRow, toRow), side, border (style array).
     *
     * @var array
     */
    public $edges = [];

    /**
     * Build a grid from recorded draw operations.
     *
     * @param array $ops operations recorded by CReport_Generator_Processor_ExcelProcessor
     *
     * @return static
     */
    public static function fromOps(array $ops) {
        $grid = new static();
        if (count($ops) == 0) {
            return $grid;
        }

        $xCuts = [];
        $yCuts = [];
        foreach ($ops as $op) {
            if ($op['type'] == 'line') {
                if ($op['width'] >= $op['height']) {
                    //horizontal line, only its y is a boundary
                    $xCuts[] = self::snap($op['x']);
                    $xCuts[] = self::snap($op['x'] + $op['width']);
                    $yCuts[] = self::snap($op['y']);
                } else {
                    $yCuts[] = self::snap($op['y']);
                    $yCuts[] = self::snap($op['y'] + $op['height']);
                    $xCuts[] = self::snap($op['x']);
                }

                continue;
            }
            $xCuts[] = self::snap($op['x']);
            $xCuts[] = self::snap($op['x'] + $op['width']);
            $yCuts[] = self::snap($op['y']);
            $yCuts[] = self::snap($op['y'] + $op['height']);
        }
        $xCuts = array_values(array_unique($xCuts, SORT_NUMERIC));
        $yCuts = array_values(array_unique($yCuts, SORT_NUMERIC));
        sort($xCuts, SORT_NUMERIC);
        sort($yCuts, SORT_NUMERIC);

        for ($i = 1; $i < count($xCuts); $i++) {
            $grid->columnWidths[$i] = $xCuts[$i] - $xCuts[$i - 1];
        }
        for ($i = 1; $i < count($yCuts); $i++) {
            $grid->rowHeights[$i] = $yCuts[$i] - $yCuts[$i - 1];
        }

        $occupied = [];
        foreach ($ops as $op) {
            if ($op['type'] == 'cell') {
                $colStart = self::cutIndex($xCuts, $op['x']) + 1;
                $colEnd = self::cutIndex($xCuts, $op['x'] + $op['width']);
                $rowStart = self::cutIndex($yCuts, $op['y']) + 1;
                $rowEnd = self::cutIndex($yCuts, $op['y'] + $op['height']);
                $colEnd = max($colStart, $colEnd);
                $rowEnd = max($rowStart, $rowEnd);
                $anchor = $rowStart . ':' . $colStart;
                if (isset($occupied[$anchor])) {
                    continue;
                }
                $occupied[$anchor] = true;
                $grid->cells[] = [
                    'row' => $rowStart,
                    'col' => $colStart,
                    'rowSpan' => $rowEnd - $rowStart + 1,
                    'colSpan' => $colEnd - $colStart + 1,
                    'value' => $op['text'],
                    'numeric' => $op['numeric'],
                    'format' => $op['format'],
                    'style' => $op['style'],
                ];
            } elseif ($op['type'] == 'rect') {
                $grid->outlines[] = [
                    'fromRow' => self::cutIndex($yCuts, $op['y']) + 1,
                    'fromCol' => self::cutIndex($xCuts, $op['x']) + 1,
                    'toRow' => max(self::cutIndex($yCuts, $op['y']) + 1, self::cutIndex($yCuts, $op['y'] + $op['height'])),
                    'toCol' => max(self::cutIndex($xCuts, $op['x']) + 1, self::cutIndex($xCuts, $op['x'] + $op['width'])),
                    'border' => $op['border'],
                ];
            } elseif ($op['type'] == 'line') {
                if ($op['width'] >= $op['height']) {
                    //horizontal line becomes a top border, or a bottom border when at the last boundary
                    $rowIndex = self::cutIndex($yCuts, $op['y']) + 1;
                    $side = 'top';
                    if ($rowIndex > count($grid->rowHeights)) {
                        $rowIndex = count($grid->rowHeights);
                        $side = 'bottom';
                    }
                    $grid->edges[] = [
                        'orientation' => 'horizontal',
                        'row' => $rowIndex,
                        'fromCol' => self::cutIndex($xCuts, $op['x']) + 1,
                        'toCol' => max(self::cutIndex($xCuts, $op['x']) + 1, self::cutIndex($xCuts, $op['x'] + $op['width'])),
                        'side' => $side,
                        'border' => $op['border'],
                    ];
                } else {
                    $colIndex = self::cutIndex($xCuts, $op['x']) + 1;
                    $side = 'left';
                    if ($colIndex > count($grid->columnWidths)) {
                        $colIndex = count($grid->columnWidths);
                        $side = 'right';
                    }
                    $grid->edges[] = [
                        'orientation' => 'vertical',
                        'col' => $colIndex,
                        'fromRow' => self::cutIndex($yCuts, $op['y']) + 1,
                        'toRow' => max(self::cutIndex($yCuts, $op['y']) + 1, self::cutIndex($yCuts, $op['y'] + $op['height'])),
                        'side' => $side,
                        'border' => $op['border'],
                    ];
                }
            }
        }

        return $grid;
    }

    /**
     * Normalize a coordinate so nearly equal edges share the same boundary.
     *
     * @param float $value
     *
     * @return float
     */
    protected static function snap($value) {
        return round($value, 1);
    }

    /**
     * Find the index of a coordinate in the sorted cuts, taking the nearest boundary.
     *
     * @param array $cuts
     * @param float $value
     *
     * @return int
     */
    protected static function cutIndex(array $cuts, $value) {
        $value = self::snap($value);
        $bestIndex = 0;
        $bestDistance = null;
        foreach ($cuts as $index => $cut) {
            $distance = abs($cut - $value);
            if ($bestDistance === null || $distance < $bestDistance) {
                $bestDistance = $distance;
                $bestIndex = $index;
            }
        }

        return $bestIndex;
    }
}

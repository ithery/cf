<?php

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Write a CReport_Excel_Grid onto a PhpSpreadsheet worksheet.
 */
class CReport_Excel_GridWriter {
    /**
     * Ratio to convert a width in points to excel character units (96/72 px per point, ~7 px per character).
     *
     * @var float
     */
    const POINT_TO_CHAR = 0.1905;

    /**
     * @var CReport_Excel_Grid
     */
    protected $grid;

    /**
     * @param CReport_Excel_Grid $grid
     */
    public function __construct(CReport_Excel_Grid $grid) {
        $this->grid = $grid;
    }

    /**
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet
     *
     * @return void
     */
    public function write(Worksheet $worksheet) {
        foreach ($this->grid->columnWidths as $col => $points) {
            $worksheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setWidth(max(0.5, $points * self::POINT_TO_CHAR));
        }
        foreach ($this->grid->rowHeights as $row => $points) {
            $worksheet->getRowDimension($row)->setRowHeight($points);
        }

        foreach ($this->grid->cells as $cell) {
            $coordinate = Coordinate::stringFromColumnIndex($cell['col']) . $cell['row'];
            if ($cell['colSpan'] > 1 || $cell['rowSpan'] > 1) {
                $endCoordinate = Coordinate::stringFromColumnIndex($cell['col'] + $cell['colSpan'] - 1) . ($cell['row'] + $cell['rowSpan'] - 1);
                $worksheet->mergeCells($coordinate . ':' . $endCoordinate);
            }
            if ($cell['numeric']) {
                $worksheet->getCell($coordinate)->setValueExplicit($cell['value'], DataType::TYPE_NUMERIC);
                if ($cell['format']) {
                    $worksheet->getStyle($coordinate)->getNumberFormat()->setFormatCode($cell['format']);
                }
            } else {
                $worksheet->getCell($coordinate)->setValueExplicit((string) $cell['value'], DataType::TYPE_STRING);
            }
            $styleArray = $this->buildStyleArray($cell['style']);
            if (count($styleArray)) {
                $worksheet->getStyle($coordinate)->applyFromArray($styleArray);
            }
        }

        foreach ($this->grid->outlines as $outline) {
            $range = Coordinate::stringFromColumnIndex($outline['fromCol']) . $outline['fromRow']
                . ':' . Coordinate::stringFromColumnIndex($outline['toCol']) . $outline['toRow'];
            $worksheet->getStyle($range)->applyFromArray([
                'borders' => ['outline' => $outline['border']],
            ]);
        }

        foreach ($this->grid->edges as $edge) {
            if ($edge['orientation'] == 'horizontal') {
                $range = Coordinate::stringFromColumnIndex($edge['fromCol']) . $edge['row']
                    . ':' . Coordinate::stringFromColumnIndex($edge['toCol']) . $edge['row'];
            } else {
                $range = Coordinate::stringFromColumnIndex($edge['col']) . $edge['fromRow']
                    . ':' . Coordinate::stringFromColumnIndex($edge['col']) . $edge['toRow'];
            }
            $worksheet->getStyle($range)->applyFromArray([
                'borders' => [$edge['side'] => $edge['border']],
            ]);
        }
    }

    /**
     * Convert a grid cell style into a PhpSpreadsheet applyFromArray structure.
     *
     * @param array $style
     *
     * @return array
     */
    protected function buildStyleArray(array $style) {
        $result = [];
        $font = [];
        if (isset($style['fontName']) && $style['fontName']) {
            $font['name'] = $style['fontName'];
        }
        if (isset($style['fontSize']) && $style['fontSize']) {
            $font['size'] = $style['fontSize'];
        }
        if (!empty($style['bold'])) {
            $font['bold'] = true;
        }
        if (!empty($style['italic'])) {
            $font['italic'] = true;
        }
        if (!empty($style['underline'])) {
            $font['underline'] = \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE;
        }
        if (!empty($style['strike'])) {
            $font['strikethrough'] = true;
        }
        if (isset($style['color']) && $style['color']) {
            $font['color'] = ['argb' => $style['color']];
        }
        if (count($font)) {
            $result['font'] = $font;
        }

        $alignment = ['wrapText' => true];
        if (isset($style['align'])) {
            $alignment['horizontal'] = $style['align'];
        }
        if (isset($style['valign'])) {
            $alignment['vertical'] = $style['valign'];
        }
        $result['alignment'] = $alignment;

        if (isset($style['fill']) && $style['fill']) {
            $result['fill'] = [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => $style['fill']],
            ];
        }
        if (isset($style['borders']) && count($style['borders'])) {
            $result['borders'] = $style['borders'];
        }

        return $result;
    }
}

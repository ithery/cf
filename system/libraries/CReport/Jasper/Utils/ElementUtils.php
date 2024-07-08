<?php

class CReport_Jasper_Utils_ElementUtils {
    public static function getLineHeightRatio(SimpleXMLElement $textElement, $default = 1) {
        $lineHeightRatio = $default;
        if (isset($textElement->paragraph['lineSpacing'])) {
            $lineSpacing = $textElement->paragraph['lineSpacing'];
            if ($lineSpacing) {
                if ((float) $lineSpacing) {
                    $lineHeightRatio = (float) $lineSpacing;
                }
                if ($lineSpacing == '1_1_2') {
                    $lineHeightRatio = 1.5;
                }
                if ($lineSpacing == 'Double') {
                    $lineHeightRatio = 1.5;
                }
                if ($lineSpacing == 'Proportional') {
                    $lineHeightRatio = $textElement->paragraph['lineSpacingSize'];
                }
            }
        }

        return $lineHeightRatio;
    }

    /**
     * Return format for a component of a box.
     *
     * @param SimpleXMLElement      $pen
     * @param null|SimpleXMLElement $box
     *
     * @return int[]|string[]|int[][]
     */
    public static function formatPen(SimpleXMLElement $pen, SimpleXMLElement $box = null) {
        $lineColor = $pen['lineColor'];
        if (!$lineColor && $box && $box->pen) {
            $lineColor = $box->pen['lineColor'];
        }//get default box
        if ($lineColor) {
            $drawcolor = [
                'r' => hexdec(substr($pen['lineColor'], 1, 2)),
                'g' => hexdec(substr($pen['lineColor'], 3, 2)),
                'b' => hexdec(substr($pen['lineColor'], 5, 2))
            ];
        }

        $dash = '';
        if (isset($pen['lineStyle'])) {
            if ($pen['lineStyle'] == 'Dotted') {
                $dash = '1,1';
            } elseif ($pen['lineStyle'] == 'Dashed') {
                $dash = '4,2';
            }

            // Dotted Dashed
        }

        return [
            'width' => $pen['lineWidth'] + 0,
            'cap' => 'butt',
            'join' => 'miter',
            'dash' => $dash,
            'phase' => 0,
            'color' => $drawcolor
        ];
    }

    /**
     * Returns patterns for all borders of a box.
     *
     * @param SimpleXMLElement $box
     *
     * @return array[]
     */
    public static function formatBorder(SimpleXMLElement $box) {
        $border = [];
        $borderset = '';
        if ($box->topPen['lineWidth'] > 0.0) {
            $border['T'] = self::formatPen($box->topPen);
        }
        if ($box->leftPen['lineWidth'] > 0.0) {
            $border['L'] = self::formatPen($box->leftPen);
        }
        if ($box->bottomPen['lineWidth'] > 0.0) {
            $border['B'] = self::formatPen($box->bottomPen);
        }
        if ($box->rightPen['lineWidth'] > 0.0) {
            $border['R'] = self::formatPen($box->rightPen);
        }

        return $border;
    }

    /**
     * @param SimpleXMLElement $box
     *
     * @return array
     */
    public static function formatBox(SimpleXMLElement $box) {
        $boxArray = [];
        if ($box->padding) {
            if ($box->padding['left']) {
                $boxArray['leftPadding'] = (float) $box->padding['left'];
            }
            if ($box->padding['top']) {
                $boxArray['topPadding'] = (float) $box->padding['top'];
            }
            if ($box->padding['right']) {
                $boxArray['rightPadding'] = (float) $box->padding['right'];
            }
            if ($box->padding['bottom']) {
                $boxArray['bottomPadding'] = (float) $box->padding['bottom'];
            }
        }

        return $boxArray;
    }
}

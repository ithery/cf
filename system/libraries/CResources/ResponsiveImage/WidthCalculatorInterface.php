<?php

interface CResources_ResponsiveImage_WidthCalculatorInterface {
    /**
     * @param string $imagePath
     *
     * @return CCollection
     */
    public function calculateWidthsFromFile($imagePath);

    /**
     * @param int $fileSize
     * @param int $width
     * @param int $height
     *
     * @return CCollection
     */
    public function calculateWidths($fileSize, $width, $height);
}

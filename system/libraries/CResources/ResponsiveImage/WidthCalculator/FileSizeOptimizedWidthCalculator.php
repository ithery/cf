<?php

class CResources_ResponsiveImage_WidthCalculator_FileSizeOptimizedWidthCalculator implements CResources_ResponsiveImage_WidthCalculatorInterface {
    /**
     * @param string $imagePath
     *
     * @return CCollection
     */
    public function calculateWidthsFromFile($imagePath) {
        $image = CResources_Helpers_ImageFactory::load($imagePath);

        $width = $image->getWidth();
        $height = $image->getHeight();
        $fileSize = filesize($imagePath);

        return $this->calculateWidths($fileSize, $width, $height);
    }

    /**
     * @param int $fileSize
     * @param int $width
     * @param int $height
     *
     * @return CCollection
     */
    public function calculateWidths($fileSize, $width, $height) {
        $targetWidths = c::collect();

        $targetWidths->push($width);

        $ratio = $height / $width;
        $area = $height * $width;

        $predictedFileSize = $fileSize;
        $pixelPrice = $predictedFileSize / $area;

        while (true) {
            $predictedFileSize *= 0.7;

            $newWidth = (int) floor(sqrt(($predictedFileSize / $pixelPrice) / $ratio));

            if ($this->finishedCalculating($predictedFileSize, $newWidth)) {
                return $targetWidths;
            }

            $targetWidths->push($newWidth);
        }
    }

    /**
     * @param int $predictedFileSize
     * @param int $newWidth
     *
     * @return bool
     */
    protected function finishedCalculating($predictedFileSize, $newWidth) {
        if ($newWidth < 20) {
            return true;
        }

        if ($predictedFileSize < (1024 * 10)) {
            return true;
        }

        return false;
    }
}

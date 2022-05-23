<?php

interface CResources_ResponsiveImage_TinyPlaceholderGeneratorInterface {
    /**
     * This function should generate a tiny jpg representation of the image
     * given in $sourceImage. The tiny jpg should be saved at $tinyImageDestination.
     *
     * @param string $sourceImage
     * @param string $tinyImageDestination
     *
     * @return void
     */
    public function generateTinyPlaceholder($sourceImage, $tinyImageDestination);
}

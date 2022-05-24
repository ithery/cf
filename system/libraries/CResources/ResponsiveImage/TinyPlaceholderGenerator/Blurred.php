<?php
class CResources_ResponsiveImage_TinyPlaceholderGenerator_Blurred implements CResources_ResponsiveImage_TinyPlaceholderGeneratorInterface {
    /**
     * @param string $sourceImagePath
     * @param string $tinyImageDestinationPath
     *
     * @return void
     */
    public function generateTinyPlaceholder($sourceImagePath, $tinyImageDestinationPath) {
        $sourceImage = CResources_Helpers_ImageFactory::load($sourceImagePath);

        $sourceImage->width(32)->blur(5)->save($tinyImageDestinationPath);
    }
}

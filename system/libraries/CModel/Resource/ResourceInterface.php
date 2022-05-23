<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 2, 2019, 12:53:18 AM
 */
interface CModel_Resource_ResourceInterface extends CInterface_Responsable, CInterface_Htmlable {
    const TYPE_OTHER = 'other';

    public function getExtensionAttribute();

    public function getDiskDriverName();

    public function getPath($conversionName = '');

    public function markAsConversionGenerated($conversionName, $generated);

    public function getImageGenerators();

    public function getResourceConversionNames();

    public function getCustomHeaders();
}

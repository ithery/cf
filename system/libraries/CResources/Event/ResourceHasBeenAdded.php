<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 2, 2019, 2:08:34 AM
 */
class CResources_Event_ResourceHasBeenAdded {
    use CQueue_Trait_SerializesModels;

    /**
     * @var \CModel_Resource_ResourceInterface
     */
    public $media;

    public function __construct(CModel_Resource_ResourceInterface $media) {
        $this->media = $media;
    }
}

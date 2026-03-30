<?php

defined('SYSPATH') or die('No direct access allowed.');

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

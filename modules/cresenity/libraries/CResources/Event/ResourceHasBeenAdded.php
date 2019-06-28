<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 2, 2019, 2:08:34 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CResources_Event_ResourceHasBeenAdded {

    use CQueue_Trait_SerializesModels;

    /** @var \CApp_Model_Interface_ResourceInterface */
    public $media;

    public function __construct(CApp_Model_Interface_ResourceInterface $media) {
        $this->media = $media;
    }

}

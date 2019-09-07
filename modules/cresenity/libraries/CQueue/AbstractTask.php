<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2019, 6:06:41 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CQueue_AbstractTask implements CQueue_ShouldQueueInterface {

    use CQueue_Trait_DispatchableTrait;
    use CQueue_Trait_QueueableTrait;
    use CQueue_Trait_InteractsWithQueue;
    use CQueue_Trait_SerializesModels;
}

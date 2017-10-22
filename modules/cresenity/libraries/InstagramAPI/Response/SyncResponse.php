<?php

/**
 * @method InstagramAPI_Response_Model_Experiment[] getExperiments()
 * @method bool isExperiments()
 * @method setExperiments(InstagramAPI_Response_Model_Experiment[] $value)
 */
class InstagramAPI_Response_SyncResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    /**
     * @var InstagramAPI_Response_Model_Experiment[]
     */
    public $experiments;

}

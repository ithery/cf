<?php

/**
 * @method InstagramAPI_Response_Model_BusinessEdges[] getEdges()
 * @method InstagramAPI_Response_Model_BusinessPageInfo getPageInfo()
 * @method bool isEdges()
 * @method bool isPageInfo()
 * @method setEdges(BusinessEdges[] $value)
 * @method setPageInfo(BusinessPageInfo $value)
 */
class InstagramAPI_Response_Model_SummaryPromotions extends InstagramAPI_AutoPropertyHandler {

    /**
     * @var InstagramAPI_Response_Model_BusinessEdges[]
     */
    public $edges;

    /**
     * @var InstagramAPI_Response_Model_BusinessPageInfo
     */
    public $page_info;

}

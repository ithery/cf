<?php

/**
 * @method InstagramAPI_Response_Model_Args getArgs()
 * @method InstagramAPI_Response_Model_Counts getCounts()
 * @method string getPk()
 * @method mixed getStoryType()
 * @method mixed getType()
 * @method bool isArgs()
 * @method bool isCounts()
 * @method bool isPk()
 * @method bool isStoryType()
 * @method bool isType()
 * @method setArgs(InstagramAPI_Response_Model_Args $value)
 * @method setCounts(InstagramAPI_Response_Model_Counts $value)
 * @method setPk(string $value)
 * @method setStoryType(mixed $value)
 * @method setType(mixed $value)
 */
class InstagramAPI_Response_Model_Story extends InstagramAPI_AutoPropertyHandler {

    /**
     * @var string
     */
    public $pk;

    /**
     * @var InstagramAPI_Response_Model_Counts
     */
    public $counts;

    /**
     * @var InstagramAPI_Response_Model_Args
     */
    public $args;
    public $type;
    public $story_type;

}

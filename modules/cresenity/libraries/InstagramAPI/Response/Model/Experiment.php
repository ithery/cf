<?php


/**
 * @method mixed getGroup()
 * @method mixed getName()
 * @method Param[] getParams()
 * @method bool isGroup()
 * @method bool isName()
 * @method bool isParams()
 * @method setGroup(mixed $value)
 * @method setName(mixed $value)
 * @method setParams(Param[] $value)
 */
class InstagramAPI_Response_Model_Experiment extends InstagramAPI_AutoPropertyHandler {

    /**
     * @var InstagramAPI_Response_Model_Param[]
     */
    public $params;
    public $group;
    public $name;

}

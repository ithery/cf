<?php

/**
 * @method mixed getEnd()
 * @method string getId()
 * @method InstagramAPI_Response_Model_LinkContext getLinkContext()
 * @method mixed getStart()
 * @method mixed getText()
 * @method mixed getType()
 * @method bool isEnd()
 * @method bool isId()
 * @method bool isLinkContext()
 * @method bool isStart()
 * @method bool isText()
 * @method bool isType()
 * @method setEnd(mixed $value)
 * @method setId(string $value)
 * @method setLinkContext(InstagramAPI_Response_Model_LinkContext $value)
 * @method setStart(mixed $value)
 * @method setText(mixed $value)
 * @method setType(mixed $value)
 */
class InstagramAPI_Response_Model_Link extends InstagramAPI_AutoPropertyHandler {

    public $start;
    public $end;

    /**
     * @var string
     */
    public $id;
    public $type;
    public $text;

    /**
     * @var InstagramAPI_Response_Model_LinkContext
     */
    public $link_context;

}

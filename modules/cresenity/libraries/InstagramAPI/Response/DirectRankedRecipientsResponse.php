<?php

/**
 * @method mixed getExpires()
 * @method mixed getFiltered()
 * @method mixed getRankToken()
 * @method InstagramAPI_Response_Model_DirectRankedRecipient[] getRankedRecipients()
 * @method string getRequestId()
 * @method bool isExpires()
 * @method bool isFiltered()
 * @method bool isRankToken()
 * @method bool isRankedRecipients()
 * @method bool isRequestId()
 * @method setExpires(mixed $value)
 * @method setFiltered(mixed $value)
 * @method setRankToken(mixed $value)
 * @method setRankedRecipients(InstagramAPI_Response_Model_DirectRankedRecipient[] $value)
 * @method setRequestId(string $value)
 */
class InstagramAPI_Response_DirectRankedRecipientsResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $expires;

    /**
     * @var InstagramAPI_Response_Model_DirectRankedRecipient[]
     */
    public $ranked_recipients;
    public $filtered;

    /**
     * @var string
     */
    public $request_id;
    public $rank_token;

}

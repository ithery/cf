<?php

/**
 * @method string[] getNewSuggestionIds()
 * @method mixed getShouldBadge()
 * @method bool isNewSuggestionIds()
 * @method bool isShouldBadge()
 * @method setNewSuggestionIds(string[] $value)
 * @method setShouldBadge(mixed $value)
 */
class InstagramAPI_Response_SuggestedUsersBadgeResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $should_badge;

    /**
     * @var string[]
     */
    public $new_suggestion_ids;

}

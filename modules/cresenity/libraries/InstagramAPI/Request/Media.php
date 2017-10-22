<?php

/**
 * Functions for interacting with media items from yourself and others.
 *
 * @see Usertag for functions that let you tag people in media.
 */
class InstagramAPI_Request_Media extends InstagramAPI_Request_RequestCollection {

    /**
     * Get detailed media information.
     *
     * @param string $mediaId The media ID in Instagram's internal format (ie "3482384834_43294").
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_MediaInfoResponse
     */
    public function getInfo($mediaId) {
        return $this->ig->request("media/{$mediaId}/info/")
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_uid', $this->ig->account_id)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->addPost('media_id', $mediaId)
                        ->getResponse(new InstagramAPI_Response_MediaInfoResponse());
    }

    /**
     * Delete a media item.
     *
     * @param string     $mediaId   The media ID in Instagram's internal format (ie "3482384834_43294").
     * @param string|int $mediaType The type of the media item you are deleting. One of: "PHOTO", "VIDEO"
     *                              "ALBUM", or the raw value of the Item's "getMediaType()" function.
     *
     * @throws \InvalidArgumentException
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_MediaDeleteResponse
     */
    public function delete($mediaId, $mediaType = 'PHOTO') {
        $mediaType = InstagramAPI_Utils::checkMediaType($mediaType);

        return $this->ig->request("media/{$mediaId}/delete/")
                        ->addParam('media_type', $mediaType)
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_uid', $this->ig->account_id)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->addPost('media_id', $mediaId)
                        ->getResponse(new InstagramAPI_Response_MediaDeleteResponse());
    }

    /**
     * Edit media.
     *
     * @param string     $mediaId     The media ID in Instagram's internal format (ie "3482384834_43294").
     * @param string     $captionText Caption to use for the media.
     * @param null|array $metadata    (optional) Associative array of optional metadata to edit:
     *                                "usertags" - special array with user tagging instructions,
     *                                if you want to modify the user tags;
     *                                "location" - a Location model object to set the media location,
     *                                or boolean FALSE to remove any location from the media.
     * @param string|int $mediaType   The type of the media item you are editing. One of: "PHOTO", "VIDEO"
     *                                "ALBUM", or the raw value of the Item's "getMediaType()" function.
     *
     * @throws \InvalidArgumentException
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_EditMediaResponse
     *
     * @see Usertag::tagMedia() for an example of proper "usertags" metadata formatting.
     * @see Usertag::untagMedia() for an example of proper "usertags" metadata formatting.
     */
    public function edit($mediaId, $captionText = '', array $metadata = null, $mediaType = 'PHOTO') {
        $mediaType = InstagramAPI_Utils::checkMediaType($mediaType);

        $request = $this->ig->request("media/{$mediaId}/edit_media/")
                ->addPost('_uuid', $this->ig->uuid)
                ->addPost('_uid', $this->ig->account_id)
                ->addPost('_csrftoken', $this->ig->client->getToken())
                ->addPost('caption_text', $captionText);

        if (isset($metadata['usertags'])) {
            Utils::throwIfInvalidUsertags($metadata['usertags']);
            $request->addPost('usertags', json_encode($metadata['usertags']));
        }

        if (isset($metadata['location'])) {
            if ($metadata['location'] === false) {
                // The user wants to remove the current location from the media.
                $request->addPost('location', '{}');
            } else {
                // The user wants to add/change the location of the media.
                if (!$metadata['location'] instanceof InstagramAPI_Response_Model\Location) {
                    throw new \InvalidArgumentException('The "location" metadata value must be an instance of InstagramAPI_Response_Model\Location.');
                }

                $request
                        ->addPost('location', Utils::buildMediaLocationJSON($metadata['location']))
                        ->addPost('geotag_enabled', '1')
                        ->addPost('posting_latitude', $metadata['location']->getLat())
                        ->addPost('posting_longitude', $metadata['location']->getLng())
                        ->addPost('media_latitude', $metadata['location']->getLat())
                        ->addPost('media_longitude', $metadata['location']->getLng());

                if ($mediaType === 'ALBUM') { // Albums need special handling.
                    $request
                            ->addPost('exif_latitude', 0.0)
                            ->addPost('exif_longitude', 0.0);
                } else { // All other types of media use "av_" instead of "exif_".
                    $request
                            ->addPost('av_latitude', 0.0)
                            ->addPost('av_longitude', 0.0);
                }
            }
        }

        return $request->getResponse(new InstagramAPI_Response_EditMediaResponse());
    }

    /**
     * Like a media item.
     *
     * @param string $mediaId   The media ID in Instagram's internal format (ie "3482384834_43294").
     * @param string $module    (optional) From which app module (page) you're performing this action.
     * @param array  $extraData (optional) Depending on the module name, additional data is required.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_GenericResponse
     */
    public function like($mediaId, $module = 'feed_contextual_post', array $extraData = []) {
        $request = $this->ig->request("media/{$mediaId}/like/")
                ->addPost('_uuid', $this->ig->uuid)
                ->addPost('_uid', $this->ig->account_id)
                ->addPost('_csrftoken', $this->ig->client->getToken())
                ->addPost('media_id', $mediaId)
                ->addPost('radio_type', 'wifi-none')
                ->addPost('module_name', $module);

        if (isset($extraData['doubleTap']) && $extraData['doubleTap']) {
            $request->addUnsignedPost('d', 1);
        } else {
            $request->addUnsignedPost('d', 0);
        }

        if ($module == 'feed_contextual_post' && isset($extraData['exploreToken'])) {
            $request->addPost('explore_source_token', $extraData['exploreToken']);
        } elseif ($module == 'photo_view_profile' && isset($extraData['username']) && isset($extraData['userid'])) {
            $request->addPost('username', $extraData['username'])
                    ->addPost('user_id', $extraData['userid']);
        } elseif ($module == 'feed_contextual_hashtag' && isset($extraData['hashtag'])) {
            $request->addPost('feed_contextual_hashtag', $extraData['hashtag']);
        } elseif ($module == 'feed_contextual_location' && isset($extraData['locationId'])) {
            $request->addPost('feed_contextual_location', $extraData['locationId']);
        }

        return $request->getResponse(new InstagramAPI_Response_GenericResponse());
    }

    /**
     * Unlike a media item.
     *
     * @param string $mediaId   The media ID in Instagram's internal format (ie "3482384834_43294").
     * @param string $module    (optional) From which app module (page) you're performing this action.
     * @param array  $extraData (optional) Depending on the module name, additional data is required.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_GenericResponse
     */
    public function unlike($mediaId, $module = 'feed_contextual_post', array $extraData = []) {
        $request = $this->ig->request("media/{$mediaId}/unlike/")
                ->addPost('_uuid', $this->ig->uuid)
                ->addPost('_uid', $this->ig->account_id)
                ->addPost('_csrftoken', $this->ig->client->getToken())
                ->addPost('media_id', $mediaId)
                ->addPost('radio_type', 'wifi-none')
                ->addPost('module_name', $module)
                ->addUnsignedPost('d', 0); // IG doesn't have "double-tap to unlike".

        if ($module == 'feed_contextual_post' && isset($extraData['exploreToken'])) {
            $request->addPost('explore_source_token', $extraData['exploreToken']);
        } elseif ($module == 'photo_view_profile' && isset($extraData['username']) && isset($extraData['userid'])) {
            $request->addPost('username', $extraData['username'])
                    ->addPost('user_id', $extraData['userid']);
        }

        return $request->getResponse(new InstagramAPI_Response_GenericResponse());
    }

    /**
     * Get feed of your liked media.
     *
     * @param null|string $maxId Next "maximum ID", used for pagination.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_LikeFeedResponse
     */
    public function getLikedFeed($maxId = null) {
        $request = $this->ig->request('feed/liked/');
        if ($maxId !== null) {
            $request->addParam('max_id', $maxId);
        }

        return $request->getResponse(new InstagramAPI_Response_LikeFeedResponse());
    }

    /**
     * Get list of users who liked a media item.
     *
     * @param string $mediaId The media ID in Instagram's internal format (ie "3482384834_43294").
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_MediaLikersResponse
     */
    public function getLikers($mediaId) {
        return $this->ig->request("media/{$mediaId}/likers/")->getResponse(new InstagramAPI_Response_MediaLikersResponse());
    }

    /**
     * Get a simplified, chronological list of users who liked a media item.
     *
     * WARNING! DANGEROUS! Although this function works, we don't know
     * whether it's used by Instagram's app right now. If it isn't used by
     * the app, then you can easily get BANNED for using this function!
     *
     * If you call this function, you do that AT YOUR OWN RISK and you
     * risk losing your Instagram account! This notice will be removed if
     * the function is safe to use. Otherwise this whole function will
     * be removed someday, if it wasn't safe.
     *
     * Only use this if you are OK with possibly losing your account!
     *
     * TODO: Research when/if the official app calls this function.
     *
     * @param string $mediaId The media ID in Instagram's internal format (ie "3482384834_43294").
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_MediaLikersResponse
     */
    public function getLikersChrono($mediaId) {
        return $this->ig->request("media/{$mediaId}/likers_chrono/")->getResponse(new InstagramAPI_Response_MediaLikersResponse());
    }

    /**
     * Enable comments for a media item.
     *
     * @param string $mediaId The media ID in Instagram's internal format (ie "3482384834_43294").
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_GenericResponse
     */
    public function enableComments($mediaId) {
        return $this->ig->request("media/{$mediaId}/enable_comments/")
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->setSignedPost(false)
                        ->getResponse(new InstagramAPI_Response_GenericResponse());
    }

    /**
     * Disable comments for a media item.
     *
     * @param string $mediaId The media ID in Instagram's internal format (ie "3482384834_43294").
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_GenericResponse
     */
    public function disableComments($mediaId) {
        return $this->ig->request("media/{$mediaId}/disable_comments/")
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->setSignedPost(false)
                        ->getResponse(new InstagramAPI_Response_GenericResponse());
    }

    /**
     * Post a comment on a media item.
     *
     * @param string $mediaId        The media ID in Instagram's internal format (ie "3482384834_43294").
     * @param string $commentText    Your comment text.
     * @param string $replyCommentId (optional) The comment ID you are replying to, if this is a reply (ie "17895795823020906");
     *                               when replying, your $commentText MUST contain an @-mention at the start (ie "@theirusername Hello!").
     * @param string $module         (optional) From which app module (page) you're performing this action.
     *
     * @throws \InvalidArgumentException
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_CommentResponse
     */
    public function comment($mediaId, $commentText, $replyCommentId = null, $module = 'comments_feed_timeline') {
        $request = $this->ig->request("media/{$mediaId}/comment/")
                ->addPost('user_breadcrumb', Utils::generateUserBreadcrumb(mb_strlen($commentText)))
                ->addPost('idempotence_token', Signatures::generateUUID(true))
                ->addPost('_uuid', $this->ig->uuid)
                ->addPost('_uid', $this->ig->account_id)
                ->addPost('_csrftoken', $this->ig->client->getToken())
                ->addPost('comment_text', $commentText)
                ->addPost('containermodule', $module)
                ->addPost('radio_type', 'wifi-none');

        if ($replyCommentId !== null) {
            if ($commentText[0] !== '@') {
                throw new InvalidArgumentException('When replying to a comment, your text must begin with an @-mention to their username.');
            }
            $request->addPost('replied_to_comment_id', $replyCommentId);
        }

        return $request->getResponse(new InstagramAPI_Response_CommentResponse());
    }

    /**
     * Get media comments.
     *
     * @param string      $mediaId The media ID in Instagram's internal format (ie "3482384834_43294").
     * @param null|string $maxId   Next "maximum ID", used for pagination.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_MediaCommentsResponse
     */
    public function getComments($mediaId, $maxId = null) {
        return $this->ig->request("media/{$mediaId}/comments/")
                        ->addParam('ig_sig_key_version', Constants::SIG_KEY_VERSION)
                        ->addParam('max_id', $maxId)
                        ->getResponse(new InstagramAPI_Response_MediaCommentsResponse());
    }

    /**
     * Delete a comment.
     *
     * @param string $mediaId   The media ID in Instagram's internal format (ie "3482384834_43294").
     * @param string $commentId The comment's ID.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_DeleteCommentResponse
     */
    public function deleteComment($mediaId, $commentId) {
        return $this->ig->request("media/{$mediaId}/comment/{$commentId}/delete/")
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_uid', $this->ig->account_id)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->getResponse(new InstagramAPI_Response_DeleteCommentResponse());
    }

    /**
     * Delete multiple comments.
     *
     * @param string          $mediaId    The media ID in Instagram's internal format (ie "3482384834_43294").
     * @param string|string[] $commentIds The IDs of one or more comments to delete.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_DeleteCommentResponse
     */
    public function deleteComments($mediaId, $commentIds) {
        if (is_array($commentIds)) {
            $commentIds = implode(',', $commentIds);
        }

        return $this->ig->request("media/{$mediaId}/comment/bulk_delete/")
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_uid', $this->ig->account_id)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->addPost('comment_ids_to_delete', $commentIds)
                        ->getResponse(new InstagramAPI_Response_DeleteCommentResponse());
    }

    /**
     * Like a comment.
     *
     * @param string $commentId The comment's ID.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_CommentLikeUnlikeResponse
     */
    public function likeComment($commentId) {
        return $this->ig->request("media/{$commentId}/comment_like/")
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_uid', $this->ig->account_id)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->getResponse(new InstagramAPI_Response_CommentLikeUnlikeResponse());
    }

    /**
     * Unlike a comment.
     *
     * @param string $commentId The comment's ID.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_CommentLikeUnlikeResponse
     */
    public function unlikeComment($commentId) {
        return $this->ig->request("media/{$commentId}/comment_unlike/")
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_uid', $this->ig->account_id)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->getResponse(new InstagramAPI_Response_CommentLikeUnlikeResponse());
    }

    /**
     * Get list of users who liked a comment.
     *
     * @param string $commentId The comment's ID.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_CommentLikersResponse
     */
    public function getCommentLikers($commentId) {
        return $this->ig->request("media/{$commentId}/comment_likers/")->getResponse(new InstagramAPI_Response_CommentLikersResponse());
    }

    /**
     * Translates comments and/or media captions.
     *
     * Note that the text will be translated to American English (en-US).
     *
     * @param string|string[] $commentIds The IDs of one or more comments and/or media IDs
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_TranslateResponse
     */
    public function translateComments($commentIds) {
        if (is_array($commentIds)) {
            $commentIds = implode(',', $commentIds);
        }

        return $this->ig->request("language/bulk_translate/?comment_ids={$commentIds}")
                        ->getResponse(new InstagramAPI_Response_TranslateResponse());
    }

    /**
     * Validate a web URL for acceptable use as external link.
     *
     * This endpoint lets you check if the URL is allowed by Instagram, and is
     * helpful to call before you try to use a web URL in your media links.
     *
     * @param string $url The URL you want to validate.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_ValidateURLResponse
     */
    public function validateURL($url) {
        return $this->ig->request('media/validate_reel_url/')
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_uid', $this->ig->account_id)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->addPost('url', $url)
                        ->getResponse(new InstagramAPI_Response_ValidateURLResponse());
    }

    /**
     * Save a media item.
     *
     * @param string $mediaId The media ID in Instagram's internal format (ie "3482384834_43294").
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_SaveAndUnsaveMedia
     */
    public function save($mediaId) {
        return $this->ig->request("media/{$mediaId}/save/")
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_uid', $this->ig->account_id)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->getResponse(new InstagramAPI_Response_SaveAndUnsaveMedia());
    }

    /**
     * Unsave a media item.
     *
     * @param string $mediaId The media ID in Instagram's internal format (ie "3482384834_43294").
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_SaveAndUnsaveMedia
     */
    public function unsave($mediaId) {
        return $this->ig->request("media/{$mediaId}/unsave/")
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_uid', $this->ig->account_id)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->getResponse(new InstagramAPI_Response_SaveAndUnsaveMedia());
    }

    /**
     * Get saved media items feed.
     *
     * @param null|string $maxId Next "maximum ID", used for pagination.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_SavedFeedResponse
     */
    public function getSavedFeed($maxId = null) {
        $request = $this->ig->request('feed/saved/')
                ->addPost('_uuid', $this->ig->uuid)
                ->addPost('_uid', $this->ig->account_id)
                ->addPost('_csrftoken', $this->ig->client->getToken());

        if ($maxId !== null) {
            $request->addParam('max_id', $maxId);
        }

        return $request->getResponse(new InstagramAPI_Response_SavedFeedResponse());
    }

    /**
     * Get blocked media.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_BlockedMediaResponse
     */
    public function getBlockedMedia() {
        return $this->ig->request('media/blocked/')
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_uid', $this->ig->account_id)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->getResponse(new InstagramAPI_Response_BlockedMediaResponse());
    }

}

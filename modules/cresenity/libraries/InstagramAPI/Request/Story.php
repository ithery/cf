<?php

/**
 * Functions for managing your story and interacting with other stories.
 *
 * @see Media for more functions that let you interact with the media.
 */
class InstagramAPI_Request_Story extends InstagramAPI_Request_RequestCollection {

    /**
     * Uploads a photo to your Instagram story.
     *
     * @param string $photoFilename    The photo filename.
     * @param array  $externalMetadata (optional) User-provided metadata key-value pairs.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_ConfigureResponse
     *
     * @see Internal::configureSinglePhoto() for available metadata fields.
     */
    public function uploadPhoto($photoFilename, array $externalMetadata = []) {
        return $this->ig->internal->uploadSinglePhoto(Constants::FEED_STORY, $photoFilename, null, $externalMetadata);
    }

    /**
     * Uploads a video to your Instagram story.
     *
     * @param string $videoFilename    The video filename.
     * @param array  $externalMetadata (optional) User-provided metadata key-value pairs.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws InstagramAPI_Exception_InstagramException
     * @throws InstagramAPI_Exception_UploadFailedException If the video upload fails.
     *
     * @return InstagramAPI_Response_ConfigureResponse
     *
     * @see Internal::configureSingleVideo() for available metadata fields.
     */
    public function uploadVideo(
    $videoFilename, array $externalMetadata = []) {
        return $this->ig->internal->uploadSingleVideo(Constants::FEED_STORY, $videoFilename, null, $externalMetadata);
    }

    /**
     * Get the global story feed which contains everyone you follow.
     *
     * Note that users will eventually drop out of this list even though they
     * still have stories. So it's always safer to call getUserStoryFeed() if
     * a specific user's story feed matters to you.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_ReelsTrayFeedResponse
     *
     * @see InstagramAPI_Request_Story::getUserStoryFeed()
     */
    public function getReelsTrayFeed() {
        return $this->ig->request('feed/reels_tray/')
                        ->getResponse(new InstagramAPI_Response_ReelsTrayFeedResponse());
    }

    /**
     * Get a specific user's story reel feed.
     *
     * This function gets the user's story Reel object directly, which always
     * exists and contains information about the user and their last story even
     * if that user doesn't have any active story anymore.
     *
     * @param string $userId Numerical UserPK ID.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_UserReelMediaFeedResponse
     *
     * @see Story::getUserStoryFeed()
     */
    public function getUserReelMediaFeed($userId) {
        return $this->ig->request("feed/user/{$userId}/reel_media/")
                        ->getResponse(new InstagramAPI_Response_UserReelMediaFeedResponse());
    }

    /**
     * Get a specific user's story feed with broadcast details.
     *
     * This function gets the story in a roundabout way, with some extra details
     * about the "broadcast". But if there is no story available, this endpoint
     * gives you an empty response.
     *
     * NOTE: At least AT THIS MOMENT, this endpoint and the reels-tray endpoint
     * are the only ones that will give you people's "post_live" fields (their
     * saved Instagram Live Replays). The other "get user stories" funcs don't!
     *
     * @param string $userId Numerical UserPK ID.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_UserStoryFeedResponse
     *
     * @see Story::getUserReelMediaFeed()
     */
    public function getUserStoryFeed($userId) {
        return $this->ig->request("feed/user/{$userId}/story/")
                        ->getResponse(new InstagramAPI_Response_UserStoryFeedResponse());
    }

    /**
     * Get multiple users' story feeds at once.
     *
     * @param string|string[] $userList List of numerical UserPK IDs.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_ReelsMediaResponse
     */
    public function getReelsMediaFeed($userList) {
        if (!is_array($userList)) {
            $userList = [$userList];
        }

        foreach ($userList as &$value) {
            $value = (string) $value;
        }
        unset($value); // Clear reference.

        return $this->ig->request('feed/reels_media/')
                        ->addPost('user_ids', $userList) // Must be string[] array.
                        ->getResponse(new InstagramAPI_Response_ReelsMediaResponse());
    }

    /**
     * Get the list of users who have seen one of your story items.
     *
     * Note that this only works for your own story items. Instagram doesn't
     * allow you to see the viewer list for other people's stories!
     *
     * @param string      $storyPk The story media item's PK in Instagram's internal format (ie "3482384834").
     * @param null|string $maxId   Next "maximum ID", used for pagination.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_ReelMediaViewerResponse
     */
    public function getStoryItemViewers($storyPk, $maxId = null) {
        $request = $this->ig->request("media/{$storyPk}/list_reel_media_viewer/");
        if ($maxId !== null) {
            $request->addParam('max_id', $maxId);
        }

        return $request->getResponse(new InstagramAPI_Response_ReelMediaViewerResponse());
    }

    /**
     * Mark story media items as seen.
     *
     * The various story-related endpoints only give you lists of story media.
     * They don't actually mark any stories as "seen", so the user doesn't know
     * that you've seen their story. Actually marking the story as "seen" is
     * done via this endpoint instead. The official app calls this endpoint
     * periodically (with 1 or more items at a time) while watching a story.
     *
     * Tip: You can pass in the whole "getItems()" array from a user's story
     * feed (retrieved via any of the other story endpoints), to easily mark
     * all of that user's story media items as seen.
     *
     * @param InstagramAPI_Response_Model\Item[] $items An array of one or more
     *                                                   story media Items.
     *
     * @throws \InvalidArgumentException
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_MediaSeenResponse
     */
    public function markMediaSeen(array $items) {
        // Build the list of seen media, with human randomization of seen-time.
        $reels = [];
        $maxSeenAt = time(); // Get current global UTC timestamp.
        $seenAt = $maxSeenAt - (3 * count($items)); // Start seenAt in the past.
        foreach ($items as $item) {
            if (!$item instanceof InstagramAPI_Response_Model\Item) {
                throw new \InvalidArgumentException(
                'markMediaSeen(): All items must be instances of InstagramAPI_Response_Model\Item.'
                );
            }

            // Raise "seenAt" if it's somehow older than the item's "takenAt".
            // NOTE: Can only happen if you see a story instantly when posted.
            $itemTakenAt = $item->getTakenAt();
            if ($seenAt < $itemTakenAt) {
                $seenAt = $itemTakenAt + 2;
            }

            // Do not let "seenAt" exceed the current global UTC time.
            if ($seenAt > $maxSeenAt) {
                $seenAt = $maxSeenAt;
            }

            // Key Format: "mediaPk_userPk_userPk" (yes, userPK is repeated).
            $reelId = $item->getId() . '_' . $item->getUser()->getPk();

            // Value Format: ["mediaTakenAt_seenAt"] (array with single string).
            $reels[$reelId] = [$item->getTakenAt() . '_' . $seenAt];

            // Randomly add 1-3 seconds to next seenAt timestamp, to act human.
            $seenAt += rand(1, 3);
        }

        return $this->ig->request('media/seen/')
                        ->setVersion(2)
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_uid', $this->ig->account_id)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->addPost('reels', $reels)
                        ->addPost('live_vods', [])
                        ->addParam('reel', 1)
                        ->addParam('live_vod', 0)
                        ->getResponse(new InstagramAPI_Response_MediaSeenResponse());
    }

    /**
     * Get your story settings.
     *
     * This has information such as your story messaging mode (who can reply
     * to your story), and the list of users you have blocked from seeing your
     * stories.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_ReelSettingsResponse
     */
    public function getReelSettings() {
        return $this->ig->request('users/reel_settings/')
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_uid', $this->ig->account_id)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->getResponse(new InstagramAPI_Response_ReelSettingsResponse());
    }

    /**
     * Set your story settings.
     *
     * @param string $messagePrefs Who can reply to your story. Valid values are "anyone" (meaning
     *                             your followers), "following" (followers that you follow back),
     *                             or "off" (meaning that nobody can reply to your story).
     *
     * @throws InvalidArgumentException
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_ReelSettingsResponse
     */
    public function setReelSettings($messagePrefs) {
        if (!in_array($messagePrefs, ['anyone', 'following', 'off'])) {
            throw new \InvalidArgumentException('You must provide a valid message preference value.');
        }

        return $this->ig->request('users/set_reel_settings/')
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_uid', $this->ig->account_id)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->addPost('message_prefs', $messagePrefs)
                        ->getResponse(new InstagramAPI_Response_ReelSettingsResponse());
    }

}

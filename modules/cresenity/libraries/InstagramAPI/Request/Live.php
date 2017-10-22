<?php

/**
 * Functions for exploring and interacting with live broadcasts.
 */
class InstagramAPI_Request_Live extends InstagramAPI_Request_RequestCollection {

    /**
     * Get suggested broadcasts.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_SuggestedBroadcastsResponse
     */
    public function getSuggestedBroadcasts() {
        return $this->ig->request('live/get_suggested_broadcasts/')
                        ->getResponse(new InstagramAPI_Response_SuggestedBroadcastsResponse());
    }

    /**
     * Get top live broadcasts.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_DiscoverTopLiveResponse
     */
    public function getDiscoverTopLive() {
        return $this->ig->request('discover/top_live/')
                        ->getResponse(new InstagramAPI_Response_DiscoverTopLiveResponse());
    }

    /**
     * Get status for a list of top broadcast ids.
     *
     * @param string|string[] $broadcastIds One or more numeric broadcast IDs.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_TopLiveStatusResponse
     */
    public function getTopLiveStatus($broadcastIds) {
        if (!is_array($broadcastIds)) {
            $broadcastIds = [$broadcastIds];
        }

        foreach ($broadcastIds as &$value) {
            $value = (string) $value;
        }
        unset($value); // Clear reference.

        return $this->ig->request('discover/top_live_status/')
                        ->addPost('broadcast_ids', $broadcastIds) // Must be string[] array.
                        ->getResponse(new InstagramAPI_Response_TopLiveStatusResponse());
    }

    /**
     * Get broadcast information.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_BroadcastInfoResponse
     */
    public function getInfo($broadcastId) {
        return $this->ig->request("live/{$broadcastId}/info/")
                        ->getResponse(new InstagramAPI_Response_BroadcastInfoResponse());
    }

    /**
     * Get the viewer list of a broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_ViewerListResponse
     */
    public function getViewerList(
    $broadcastId) {
        return $this->ig->request("live/{$broadcastId}/get_viewer_list/")
                        ->getResponse(new InstagramAPI_Response_ViewerListResponse());
    }

    /**
     * Get the final viewer list of a broadcast after it has ended.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_FinalViewerListResponse
     */
    public function getFinalViewerList($broadcastId) {
        return $this->ig->request("live/{$broadcastId}/get_final_viewer_list/")
                        ->getResponse(new InstagramAPI_Response_FinalViewerListResponse());
    }

    /**
     * Get the viewer list of a post-live (saved replay) broadcast.
     *
     * @param string      $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param null|string $maxId       Next "maximum ID", used for pagination.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_PostLiveViewerListResponse
     */
    public function getPostLiveViewerList($broadcastId, $maxId = null) {
        $request = $this->ig->request("live/{$broadcastId}/get_post_live_viewers_list/");
        if ($maxId !== null) {
            $request->addParam('max_id', $maxId);
        }

        return $request->getResponse(new InstagramAPI_Response_PostLiveViewerListResponse());
    }

    /**
     * Get a live broadcast's heartbeat and viewer count.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_BroadcastHeartbeatAndViewerCountResponse
     */
    public function getHeartbeatAndViewerCount($broadcastId) {
        return $this->ig->request("live/{$broadcastId}/heartbeat_and_get_viewer_count/")
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->getResponse(new InstagramAPI_Response_BroadcastHeartbeatAndViewerCountResponse());
    }

    /**
     * Post a comment to a live broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $commentText Your comment text.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_CommentBroadcastResponse
     */
    public function comment($broadcastId, $commentText) {
        return $this->ig->request("live/{$broadcastId}/comment/")
                        ->addPost('user_breadcrumb', Utils::generateUserBreadcrumb(mb_strlen($commentText)))
                        ->addPost('idempotence_token', Signatures::generateUUID(true))
                        ->addPost('comment_text', $commentText)
                        ->addPost('live_or_vod', 1)
                        ->addPost('offset_to_video_start', 0)
                        ->getResponse(new InstagramAPI_Response_CommentBroadcastResponse());
    }

    /**
     * Pin a comment on live broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $commentId   Target comment ID.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_PinCommentBroadcastResponse
     */
    public function pinComment($broadcastId, $commentId) {
        return $this->ig->request("live/{$broadcastId}/pin_comment/")
                        ->addPost('offset_to_video_start', 0)
                        ->addPost('comment_id', $commentId)
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_uid', $this->ig->account_id)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->getResponse(new InstagramAPI_Response_PinCommentBroadcastResponse());
    }

    /**
     * Unpin a comment on live broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $commentId   Pinned comment ID.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_UnpinCommentBroadcastResponse
     */
    public function unpinComment($broadcastId, $commentId) {
        return $this->ig->request("live/{$broadcastId}/unpin_comment/")
                        ->addPost('offset_to_video_start', 0)
                        ->addPost('comment_id', $commentId)
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_uid', $this->ig->account_id)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->getResponse(new InstagramAPI_Response_UnpinCommentBroadcastResponse());
    }

    /**
     * Get broadcast comments.
     *
     * @param string $broadcastId   The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param int    $lastCommentTs Last comments timestamp (optional).
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_BroadcastCommentsResponse
     */
    public function getComments($broadcastId, $lastCommentTs = 0) {
        return $this->ig->request("live/{$broadcastId}/get_comment/")
                        ->addParam('last_comment_ts', $lastCommentTs)
                        ->getResponse(new InstagramAPI_Response_BroadcastCommentsResponse());
    }

    /**
     * Get post-live (saved replay) broadcast comments.
     *
     * @param string $broadcastId    The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param int    $startingOffset (optional) The time-offset to start at when retrieving the comments.
     * @param string $encodingTag    (optional) TODO: ?.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_PostLiveCommentsResponse
     */
    public function getPostLiveComments($broadcastId, $startingOffset = 0, $encodingTag = 'instagram_dash_remuxed') {
        return $this->ig->request("live/{$broadcastId}/get_post_live_comments/")
                        ->addParam('starting_offset', $startingOffset)
                        ->addParam('encoding_tag', $encodingTag)
                        ->getResponse(new InstagramAPI_Response_PostLiveCommentsResponse());
    }

    /**
     * Like a broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param int    $likeCount   Number of likes ("hearts") to send (optional).
     *
     * @throws \InvalidArgumentException
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_BroadcastLikeResponse
     */
    public function like($broadcastId, $likeCount = 1) {
        if ($likeCount < 1 || $likeCount > 6) {
            throw new \InvalidArgumentException('Like count must be a number from 1 to 6.');
        }

        return $this->ig->request("live/{$broadcastId}/like/")
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_uid', $this->ig->account_id)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->addPost('user_like_count', $likeCount)
                        ->getResponse(new InstagramAPI_Response_BroadcastLikeResponse());
    }

    /**
     * Get a live broadcast's like count.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param int    $likeTs      Like timestamp.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_BroadcastLikeCountResponse
     */
    public function getLikeCount($broadcastId, $likeTs = 0) {
        return $this->ig->request("live/{$broadcastId}/get_like_count/")
                        ->addParam('like_ts', $likeTs)
                        ->getResponse(new InstagramAPI_Response_BroadcastLikeCountResponse());
    }

    /**
     * Get post-live (saved replay) broadcast likes.
     *
     * @param string $broadcastId    The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param int    $startingOffset (optional) The time-offset to start at when retrieving the likes.
     * @param string $encodingTag    (optional) TODO: ?.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_PostLiveLikesResponse
     */
    public function getPostLiveLikes($broadcastId, $startingOffset = 0, $encodingTag = 'instagram_dash_remuxed') {
        return $this->ig->request("live/{$broadcastId}/get_post_live_likes/")
                        ->addParam('starting_offset', $startingOffset)
                        ->addParam('encoding_tag', $encodingTag)
                        ->getResponse(new InstagramAPI_Response_PostLiveLikesResponse());
    }

    /**
     * Create a live broadcast.
     *
     * Read the description of start() for proper usage.
     *
     * @param int    $previewWidth     (optional) Width.
     * @param int    $previewHeight    (optional) Height.
     * @param string $broadcastMessage (optional) Message to use for the broadcast.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_CreateLiveResponse
     *
     * @see Live::start()
     */
    public function create($previewWidth = 720, $previewHeight = 1184, $broadcastMessage = '') {
        return $this->ig->request('live/create/')
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->addPost('preview_height', $previewHeight)
                        ->addPost('preview_width', $previewWidth)
                        ->addPost('broadcast_message', $broadcastMessage)
                        ->addPost('broadcast_type', 'RTMP')
                        ->addPost('internal_only', 0)
                        ->getResponse(new InstagramAPI_Response_CreateLiveResponse());
    }

    /**
     * Start a live broadcast.
     *
     * Note that you MUST first call create() to get a broadcast-ID and its RTMP
     * upload-URL. Next, simply begin sending your actual video broadcast to the
     * stream-upload URL. And then call start() with the broadcast-ID to make
     * the stream available to viewers.
     *
     * Also note that broadcasting to the video stream URL must be done via
     * other software, since it ISN'T (and won't be) handled by this library!
     *
     * Lastly, note that stopping the stream is done via RTMP signals, which
     * your broadcasting software MUST output properly (FFmpeg DOESN'T do it!).
     *
     * @param string $broadcastId       The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param bool   $sendNotifications (optional) Whether to send notifications about the broadcast to your followers.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_StartLiveResponse
     *
     * @see Live::create()
     */
    public function start($broadcastId, $sendNotifications = true) {
        return $this->ig->request("live/{$broadcastId}/start/")
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->addPost('should_send_notifications', (int) $sendNotifications)
                        ->getResponse(new InstagramAPI_Response_StartLiveResponse());
    }

    /**
     * Add a finished broadcast to your post-live feed (saved replay).
     *
     * The broadcast must have ended before you can call this function.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_GenericResponse
     */
    public function addToPostLive($broadcastId) {
        return $this->ig->request("live/{$broadcastId}/add_to_post_live/")
                        ->addPost('_uid', $this->ig->account_id)
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->getResponse(new InstagramAPI_Response_GenericResponse());
    }

    /**
     * Delete a saved post-live broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_GenericResponse
     */
    public function deletePostLive($broadcastId) {
        return $this->ig->request("live/{$broadcastId}/delete_post_live/")
                        ->addPost('_uid', $this->ig->account_id)
                        ->addPost('_uuid', $this->ig->uuid)
                        ->addPost('_csrftoken', $this->ig->client->getToken())
                        ->getResponse(new InstagramAPI_Response_GenericResponse());
    }

}

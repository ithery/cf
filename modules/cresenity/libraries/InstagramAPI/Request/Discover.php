<?php

/**
 * General content discovery functions which don't fit into any better groups.
 */
class InstagramAPI_Request_Discover extends InstagramAPI_Request_RequestCollection {

    /**
     * Get Explore tab feed.
     *
     * @param null|string $maxId      Next "maximum ID", used for pagination.
     * @param bool        $isPrefetch Whether this is the first fetch; we'll ignore maxId if TRUE.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_ExploreResponse
     */
    public function getExploreFeed(
    $maxId = null, $isPrefetch = false) {
        $request = $this->ig->request('discover/explore/')
                ->addParam('is_prefetch', $isPrefetch)
                ->addParam('is_from_promote', false)
                ->addParam('timezone_offset', date('Z'))
                ->addParam('session_id', $this->ig->session_id);

        if (!$isPrefetch) {
            if ($maxId === null) {
                $maxId = 0;
            }
            $request->addParam('max_id', $maxId);
            $request->addParam('module', 'explore_popular');
        }

        return $request->getResponse(new InstagramAPI_Response_ExploreResponse());
    }

    /**
     * Report media in the Explore-feed.
     *
     * @param string $exploreSourceToken Token related to the Explore media.
     * @param string $userId             Numerical UserPK ID.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_ReportExploreMediaResponse
     */
    public function reportExploreMedia(
    $exploreSourceToken, $userId) {
        return $this->ig->request('discover/explore_report/')
                        ->addParam('explore_source_token', $exploreSourceToken)
                        ->addParam('m_pk', $this->ig->account_id)
                        ->addParam('a_pk', $userId)
                        ->getResponse(new InstagramAPI_Response_ReportExploreMediaResponse());
    }

    /**
     * Get popular feed.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_PopularFeedResponse
     */
    public function getPopularFeed() {
        $request = $this->ig->request('feed/popular/')
                ->addParam('people_teaser_supported', '1')
                ->addParam('rank_token', $this->ig->rank_token)
                ->addParam('ranked_content', 'true');
        // if ($maxId) { // NOTE: Popular feed DOESN'T properly support max_id.
        //     $request->addParam('max_id', $maxId);
        // }

        return $request->getResponse(new InstagramAPI_Response_PopularFeedResponse());
    }

    /**
     * Get Home channel feed.
     *
     * @param null|string $maxId Next "maximum ID", used for pagination.
     *
     * @throws InstagramAPI_Exception_InstagramException
     *
     * @return InstagramAPI_Response_DiscoverChannelsResponse
     */
    public function getHomeChannelFeed(
    $maxId = null) {
        $request = $this->ig->request('discover/channels_home/');
        if ($maxId) {
            $request->addParam('max_id', $maxId);
        }

        return $request->getResponse(new InstagramAPI_Response_DiscoverChannelsResponse());
    }

}

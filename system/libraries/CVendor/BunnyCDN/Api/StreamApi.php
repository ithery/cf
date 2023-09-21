<?php

class CVendor_BunnyCDN_Api_StreamApi extends CVendor_BunnyCDN_ApiAbstract {
    private $streamLibraryId;

    public function __construct(CVendor_BunnyCDN_Client_StreamClient $client, $streamLibraryId = null) {
        $this->client = $client;
        $this->setStreamLibraryId($streamLibraryId);
    }

    /**
     * @param string|int $streamLibraryId
     *
     * @return CVendor_BunnyCDN_Api_StreamApi
     */
    public function setStreamLibraryId($streamLibraryId) {
        $this->streamLibraryId = $streamLibraryId;

        return $this;
    }

    public function getVideoCollections(int $page = 1, int $items_per_page = 100, $streamLibraryId = null): array {
        if ($streamLibraryId == null) {
            $streamLibraryId = $this->streamLibraryId;
        }

        return $this->client->request("library/{$streamLibraryId}/collections", 'GET', [
            'json' => ['page' => $page, 'itemsPerPage' => $items_per_page]
        ]);
    }

    public function getStreamCollections(int $page = 1, int $items_pp = 100, string $order_by = 'date', $streamLibraryId = null): array {
        if ($streamLibraryId == null) {
            $streamLibraryId = $this->streamLibraryId;
        }

        return $this->client->request("library/{$streamLibraryId}/collections?page=$page&itemsPerPage=$items_pp&orderBy=$order_by", 'GET');
    }

    public function getStreamCollection($collectionId, $streamLibraryId = null) {
        if ($streamLibraryId == null) {
            $streamLibraryId = $this->streamLibraryId;
        }

        return $this->client->request("library/{$streamLibraryId}/collections/" . $collectionId, 'GET');
    }

    public function updateCollectionName($collectionId, $newName, $streamLibraryId = null) {
        if ($streamLibraryId == null) {
            $streamLibraryId = $this->streamLibraryId;
        }

        return $this->client->request("library/{$streamLibraryId}/collections/" . $collectionId, 'POST', [
            'json' => [
                'name' => $newName
            ]
        ]);
    }

    public function deleteCollection($collectionId, $streamLibraryId = null) {
        if ($streamLibraryId == null) {
            $streamLibraryId = $this->streamLibraryId;
        }

        return $this->client->request("library/{$streamLibraryId}/collections/" . $collectionId, 'DELETE');
    }

    public function createCollection($name, $streamLibraryId = null) {
        if ($streamLibraryId == null) {
            $streamLibraryId = $this->streamLibraryId;
        }

        return $this->client->request("library/{$streamLibraryId}/collections", 'POST', [
            'json' => [
                'name' => $name
            ]
        ]);
    }

    public function getVideos(int $page = 1, int $items_pp = 100, string $order_by = 'date', $collectionId = null, $streamLibraryId = null) {
        if ($streamLibraryId == null) {
            $streamLibraryId = $this->streamLibraryId;
        }
        $path = "library/{$streamLibraryId}/videos?page=$page&itemsPerPage=$items_pp&orderBy=$order_by";
        if ($collectionId) {
            $path .= '&collection=' . $collectionId;
        }

        return $this->client->request($path, 'GET');
    }

    public function getVideoStatistics($streamLibraryId = null) {
        if ($streamLibraryId == null) {
            $streamLibraryId = $this->streamLibraryId;
        }
        $path = "library/{$streamLibraryId}/statistics";

        return $this->client->request($path, 'GET');
    }

    public function getVideoHeatmap($videoId, $streamLibraryId = null) {
        if ($streamLibraryId == null) {
            $streamLibraryId = $this->streamLibraryId;
        }
        $path = "library/{$streamLibraryId}/videos/{$videoId}/heatmap";

        return $this->client->request($path, 'GET');
    }

    public function getVideo($videoId, $streamLibraryId = null) {
        if ($streamLibraryId == null) {
            $streamLibraryId = $this->streamLibraryId;
        }
        $path = "library/{$streamLibraryId}/videos/{$videoId}";

        return $this->client->request($path, 'GET');
    }

    public function deleteVideo($videoId, $streamLibraryId = null) {
        if ($streamLibraryId == null) {
            $streamLibraryId = $this->streamLibraryId;
        }
        $path = "library/{$streamLibraryId}/videos/{$videoId}";

        return $this->client->request($path, 'DELETE');
    }

    public function createVideo($title, $collectionId = null, $streamLibraryId = null) {
        if ($streamLibraryId == null) {
            $streamLibraryId = $this->streamLibraryId;
        }
        $path = "library/{$streamLibraryId}/videos";

        $postData = [
            'title' => $title
        ];
        if ($collectionId) {
            $postData['collectionId'] = $collectionId;
        }

        return $this->client->request($path, 'POST', [
            'json' => $postData
        ]);
    }

    public function uploadVideo($videoId, $file, $streamLibraryId = null) {
        if ($streamLibraryId == null) {
            $streamLibraryId = $this->streamLibraryId;
        }
        $path = "library/{$streamLibraryId}/videos/{$videoId}";

        return $this->client->request($path, 'PUT', [
            'json' => [
                'file' => $file
            ]
        ]);
    }

    public function setThumbnail($videoId, $thumbnailUrl, $streamLibraryId = null) {
        if ($streamLibraryId == null) {
            $streamLibraryId = $this->streamLibraryId;
        }
        $path = "library/{$streamLibraryId}/videos/{$videoId}/thumbnail?thumbnailUrl={$thumbnailUrl}";

        return $this->client->request($path, 'POST');
    }

    public function addCaptions($videoId, $srclang, $label, $captionsFile, $streamLibraryId = null) {
        if ($streamLibraryId == null) {
            $streamLibraryId = $this->streamLibraryId;
        }
        $path = "library/{$streamLibraryId}/videos/{$videoId}/captions/$srclang?label=$label&captionsFile=$captionsFile";

        return $this->client->request($path, 'POST');
    }

    public function reEncodeVideo($videoId, $streamLibraryId = null) {
        if ($streamLibraryId == null) {
            $streamLibraryId = $this->streamLibraryId;
        }
        $path = "library/{$streamLibraryId}/videos/{$videoId}/reencode";

        return $this->client->request($path, 'POST');
    }

    public function fetchVideo($videoUrl, $collectionId = null, $streamLibraryId = null) {
        //Downloads a video from a URL into stream library/collection
        if ($streamLibraryId == null) {
            $streamLibraryId = $this->streamLibraryId;
        }
        $path = "library/{$streamLibraryId}/videos/fetch";
        if ($collectionId) {
            $path .= '?collectionId=' . $collectionId;
        }

        return $this->client->request($path, 'POST', [
            'json' => [
                'url' => $videoUrl
            ]
        ]);
    }

    public function deleteCaptions($videoId, $srclang, $streamLibraryId = null) {
        if ($streamLibraryId == null) {
            $streamLibraryId = $this->streamLibraryId;
        }
        $path = "library/{$streamLibraryId}/videos/{$videoId}/captions/$srclang";

        return $this->client->request($path, 'POST');
    }
}

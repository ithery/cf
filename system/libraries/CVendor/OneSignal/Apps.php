<?php

class CVendor_OneSignal_Apps extends CVendor_OneSignal_AbstractApi {
    const OUTCOME_ATTRIBUTION_TOTAL = 'total';

    const OUTCOME_ATTRIBUTION_UNATTRIBUTED = 'unattributed';

    const OUTCOME_ATTRIBUTION_INFLUENCED = 'influenced';

    const OUTCOME_ATTRIBUTION_DIRECT = 'direct';

    const OUTCOME_TIME_RANGE_MONTH = '1mo';

    const OUTCOME_TIME_RANGE_HOUR = '1h';

    const OUTCOME_TIME_RANGE_DAY = '1d';

    protected $api;

    private $resolverFactory;

    public function __construct(CVendor_OneSignal $api, CVendor_OneSignal_Resolver_ResolverFactory $resolverFactory) {
        parent::__construct($api);

        $this->resolverFactory = $resolverFactory;
    }

    /**
     * Get information about application with provided ID.
     *
     * User authentication key must be set.
     *
     * @param string $id ID of your application
     */
    public function getOne($id) {
        $request = $this->createRequest('GET', "/apps/${id}");
        $request = $request->withHeader('Authorization', "Basic {$this->client->getConfig()->getUserAuthKey()}");

        return $this->client->sendRequest($request);
    }

    /**
     * Get information about all your created applications.
     *
     * User authentication key must be set.
     */
    public function getAll() {
        $request = $this->createRequest('GET', '/apps');
        $request = $request->withHeader('Authorization', "Basic {$this->client->getConfig()->getUserAuthKey()}");

        return $this->client->sendRequest($request);
    }

    /**
     * Create a new application with provided data.
     *
     * User authentication key must be set.
     *
     * @param array $data Application data
     */
    public function add(array $data) {
        $resolvedData = $this->resolverFactory->createAppResolver()->resolve($data);

        $request = $this->createRequest('POST', '/apps');
        $request = $request->withHeader('Authorization', "Basic {$this->client->getConfig()->getUserAuthKey()}");
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withBody($this->createStream($resolvedData));

        return $this->client->sendRequest($request);
    }

    /**
     * Update application with provided data.
     *
     * User authentication key must be set.
     *
     * @param string $id   ID of your application
     * @param array  $data New application data
     */
    public function update($id, array $data) {
        $resolvedData = $this->resolverFactory->createAppResolver()->resolve($data);

        $request = $this->createRequest('PUT', "/apps/${id}");
        $request = $request->withHeader('Authorization', "Basic {$this->client->getConfig()->getUserAuthKey()}");
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withBody($this->createStream($resolvedData));

        return $this->client->sendRequest($request);
    }

    /**
     * Create a new segment for application with provided data.
     *
     * @param string $appId ID of your application
     * @param array  $data  Segment Data
     */
    public function createSegment($appId, array $data) {
        $resolvedData = $this->resolverFactory->createSegmentResolver()->resolve($data);

        $request = $this->createRequest('POST', "/apps/${appId}/segments");
        $request = $request->withHeader('Authorization', "Basic {$this->client->getConfig()->getApplicationAuthKey()}");
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withBody($this->createStream($resolvedData));

        return $this->client->sendRequest($request);
    }

    /**
     * Delete existing segment from your application.
     *
     * Application auth key must be set.
     *
     * @param string $appId     Application ID
     * @param string $segmentId Segment ID
     */
    public function deleteSegment($appId, $segmentId) {
        $request = $this->createRequest('DELETE', "/apps/${appId}/segments/${segmentId}");
        $request = $request->withHeader('Authorization', "Basic {$this->client->getConfig()->getApplicationAuthKey()}");

        return $this->client->sendRequest($request);
    }

    /**
     * View the details of all the outcomes associated with your app.
     *
     * @param string $appId Application ID
     * @param array  $data  Outcome data filters
     */
    public function outcomes($appId, array $data) {
        $resolvedData = $this->resolverFactory->createOutcomesResolver()->resolve($data);

        $queryString = preg_replace('/%5B\d+%5D/', '%5B%5D', http_build_query($resolvedData));

        $request = $this->createRequest('GET', "/apps/${appId}/outcomes?${queryString}");
        $request = $request->withHeader('Authorization', "Basic {$this->client->getConfig()->getApplicationAuthKey()}");

        return $this->client->sendRequest($request);
    }
}

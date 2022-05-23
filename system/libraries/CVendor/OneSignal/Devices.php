<?php

class CVendor_OneSignal_Devices extends CVendor_OneSignal_AbstractApi {
    const IOS = 0;

    const ANDROID = 1;

    const AMAZON = 2;

    const WINDOWS_PHONE = 3;

    const WINDOWS_PHONE_MPNS = 3;

    const CHROME_APP = 4;

    const CHROME_WEB = 5;

    const WINDOWS_PHONE_WNS = 6;

    const SAFARI = 7;

    const FIREFOX = 8;

    const MACOS = 9;

    const ALEXA = 10;

    const EMAIL = 11;

    const HUAWEI = 13;

    const SMS = 14;

    private $resolverFactory;

    public function __construct(CVendor_OneSignal $api, CVendor_OneSignal_Resolver_ResolverFactory $resolverFactory) {
        parent::__construct($api);

        $this->resolverFactory = $resolverFactory;
    }

    /**
     * Get information about device with provided ID.
     *
     * @param string $id Device ID
     *
     * @return array
     */
    public function getOne($id) {
        $request = $this->createRequest('GET', "/players/${id}?app_id={$this->client->getConfig()->getApplicationId()}");

        return $this->client->sendRequest($request);
    }

    /**
     * Get information about all registered devices for your application.
     *
     * Application auth key must be set.
     *
     * @param int $limit  How many devices to return. Max is 300. Default is 300
     * @param int $offset Result offset. Default is 0. Results are sorted by id
     *
     * @return array
     */
    public function getAll($limit = null, $offset = null) {
        $query = ['app_id' => $this->client->getConfig()->getApplicationId()];

        if ($limit !== null) {
            $query['limit'] = $limit;
        }

        if ($offset !== null) {
            $query['offset'] = $offset;
        }

        $request = $this->createRequest('GET', '/players?' . http_build_query($query));
        $request = $request->withHeader('Authorization', "Basic {$this->client->getConfig()->getApplicationAuthKey()}");

        return $this->client->sendRequest($request);
    }

    /**
     * Register a device for your application.
     *
     * @param array $data Device data
     *
     * @return array
     */
    public function add(array $data) {
        $resolvedData = $this->resolverFactory->createNewDeviceResolver()->resolve($data);

        $request = $this->createRequest('POST', '/players');
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withBody($this->createStream($resolvedData));

        return $this->client->sendRequest($request);
    }

    /**
     * Update existing registered device for your application with provided data.
     *
     * @param string $id   Device ID
     * @param array  $data New device data
     *
     * @return array
     */
    public function update($id, array $data) {
        $resolvedData = $this->resolverFactory->createExistingDeviceResolver()->resolve($data);

        $request = $this->createRequest('PUT', "/players/${id}");
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withBody($this->createStream($resolvedData));

        return $this->client->sendRequest($request);
    }

    /**
     * Delete existing registered device from your application.
     *
     * OneSignal supports DELETE on the players API endpoint which is not documented in their official documentation
     * Reference: https://documentation.onesignal.com/docs/handling-personal-data#section-deleting-users-or-other-data-from-onesignal
     *
     * Application auth key must be set.
     *
     * @param string $id Device ID
     *
     * @return array
     */
    public function delete($id) {
        $request = $this->createRequest('DELETE', "/players/${id}?app_id={$this->client->getConfig()->getApplicationId()}");
        $request = $request->withHeader('Authorization', "Basic {$this->client->getConfig()->getApplicationAuthKey()}");

        return $this->client->sendRequest($request);
    }

    /**
     * Call on new device session in your app.
     *
     * @param string $id   Device ID
     * @param array  $data Device data
     *
     * @return array
     */
    public function onSession($id, array $data) {
        $resolvedData = $this->resolverFactory->createDeviceSessionResolver()->resolve($data);

        $request = $this->createRequest('POST', "/players/${id}/on_session");
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withBody($this->createStream($resolvedData));

        return $this->client->sendRequest($request);
    }

    /**
     * Track a new purchase.
     *
     * @param string $id   Device ID
     * @param array  $data Device data
     *
     * @return array
     */
    public function onPurchase($id, array $data) {
        $resolvedData = $this->resolverFactory->createDevicePurchaseResolver()->resolve($data);

        $request = $this->createRequest('POST', "/players/${id}/on_purchase");
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withBody($this->createStream($resolvedData));

        return $this->client->sendRequest($request);
    }

    /**
     * Increment the device's total session length.
     *
     * @param string $id   Device ID
     * @param array  $data Device data
     *
     * @return array
     */
    public function onFocus($id, array $data) {
        $resolvedData = $this->resolverFactory->createDeviceFocusResolver()->resolve($data);

        $request = $this->createRequest('POST', "/players/${id}/on_focus");
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withBody($this->createStream($resolvedData));

        return $this->client->sendRequest($request);
    }

    /**
     * Export all information about devices in a CSV format for your application.
     *
     * Application auth key must be set.
     *
     * @param array  $extraFields     Additional fields that you wish to include.
     *                                Currently supports: "location", "country", "rooted"
     * @param string $segmentName     A segment name to filter the scv export by.
     *                                Only devices from that segment will make it into the export
     * @param int    $lastActiveSince An epoch to filter results to users active after this time
     *
     * @return array
     */
    public function csvExport(array $extraFields = [], $segmentName = null, $lastActiveSince = null) {
        $request = $this->createRequest('POST', "/players/csv_export?app_id={$this->client->getConfig()->getApplicationId()}");
        $request = $request->withHeader('Authorization', "Basic {$this->client->getConfig()->getApplicationAuthKey()}");
        $request = $request->withHeader('Content-Type', 'application/json');

        $body = ['extra_fields' => $extraFields];

        if ($segmentName !== null) {
            $body['segment_name'] = $segmentName;
        }

        if ($lastActiveSince !== null) {
            $body['last_active_since'] = (string) $lastActiveSince;
        }

        $request = $request->withBody($this->createStream($body));

        return $this->client->sendRequest($request);
    }

    /**
     * Update an existing device's tags using the External User ID.
     *
     * @param string $externalUserId External User ID
     * @param array  $data           Tags data
     *
     * @return array
     */
    public function editTags($externalUserId, array $data) {
        $resolvedData = $this->resolverFactory->createDeviceTagsResolver()->resolve($data);

        $request = $this->createRequest('PUT', "/apps/{$this->client->getConfig()->getApplicationId()}/users/${externalUserId}");
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withBody($this->createStream($resolvedData));

        return $this->client->sendRequest($request);
    }
}

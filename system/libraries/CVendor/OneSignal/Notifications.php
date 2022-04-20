<?php

class CVendor_OneSignal_Resolver_Notifications extends CVendor_OneSignal_AbstractApi {
    const NOTIFICATIONS_LIMIT = 50;

    private $resolverFactory;

    public function __construct(CVendor_OneSignal $client, CVendor_OneSignal_Resolver_ResolverFactory $resolverFactory) {
        parent::__construct($client);

        $this->resolverFactory = $resolverFactory;
    }

    /**
     * Get information about notification with provided ID.
     *
     * Application authentication key and ID must be set.
     *
     * @param string $id Notification ID
     *
     * @return array
     */
    public function getOne($id) {
        $request = $this->createRequest('GET', "/notifications/${id}?app_id={$this->client->getConfig()->getApplicationId()}");
        $request = $request->withHeader('Authorization', "Basic {$this->client->getConfig()->getApplicationAuthKey()}");

        return $this->client->sendRequest($request);
    }

    /**
     * Get information about all notifications.
     *
     * Application authentication key and ID must be set.
     *
     * @param int $limit  How many notifications to return (max 50)
     * @param int $offset Results offset (results are sorted by ID)
     *
     * @return array
     */
    public function getAll($limit = self::NOTIFICATIONS_LIMIT, $offset = 0) {
        $query = ['app_id' => $this->client->getConfig()->getApplicationId()];

        if ($limit !== null) {
            $query['limit'] = $limit;
        }

        if ($offset !== null) {
            $query['offset'] = $offset;
        }

        if (func_num_args() > 2 && is_int(func_get_arg(2))) {
            $query['kind'] = func_get_arg(2);
        }

        $request = $this->createRequest('GET', '/notifications?' . http_build_query($query));
        $request = $request->withHeader('Authorization', "Basic {$this->client->getConfig()->getApplicationAuthKey()}");

        return $this->client->sendRequest($request);
    }

    /**
     * Send new notification with provided data.
     *
     * Application authentication key and ID must be set.
     *
     * @param array $data
     *
     * @return array
     */
    public function add(array $data) {
        $resolvedData = $this->resolverFactory->createNotificationResolver()->resolve($data);

        $request = $this->createRequest('POST', '/notifications');
        $request = $request->withHeader('Authorization', "Basic {$this->client->getConfig()->getApplicationAuthKey()}");
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withBody($this->createStream($resolvedData));

        return $this->client->sendRequest($request);
    }

    /**
     * Open notification.
     *
     * Application authentication key and ID must be set.
     *
     * @param string $id Notification ID
     *
     * @return array
     */
    public function open($id) {
        $request = $this->createRequest('PUT', "/notifications/${id}");
        $request = $request->withHeader('Authorization', "Basic {$this->client->getConfig()->getApplicationAuthKey()}");
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withBody($this->createStream([
            'app_id' => $this->client->getConfig()->getApplicationId(),
            'opened' => true,
        ]));

        return $this->client->sendRequest($request);
    }

    /**
     * Cancel notification.
     *
     * Application authentication key and ID must be set.
     *
     * @param string $id Notification ID
     *
     * @return array
     */
    public function cancel($id) {
        $request = $this->createRequest('DELETE', "/notifications/${id}?app_id={$this->client->getConfig()->getApplicationId()}");
        $request = $request->withHeader('Authorization', "Basic {$this->client->getConfig()->getApplicationAuthKey()}");

        return $this->client->sendRequest($request);
    }

    /**
     * View the devices sent a notification.
     *
     * Application authentication key and ID must be set.
     *
     * @param string $id Notification ID
     *
     * @return array
     */
    public function history($id, array $data) {
        $resolvedData = $this->resolverFactory->createNotificationHistoryResolver()->resolve($data);

        $request = $this->createRequest('POST', "/notifications/${id}/history");
        $request = $request->withHeader('Authorization', "Basic {$this->client->getConfig()->getApplicationAuthKey()}");
        $request = $request->withHeader('Cache-Control', 'no-cache');
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withBody($this->createStream($resolvedData));

        return $this->client->sendRequest($request);
    }
}

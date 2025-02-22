<?php

class CVendor_Shipper {
    const VERSION = '3.0';

    private $key = 'c46eacd847a2ab8d4459d3e54c8694dc';

    private $url;

    private $curl;

    private $environment;

    public function __construct($env = 'production') {
        if (is_array($env)) {
            $this->key = carr::get($env, 'apiKey', $this->key);
            $env = carr::get($env, 'environment', 'production');
        }
        $this->environment = $env;
        $this->url = 'https://merchant-api.shipper.id/';
        if ($this->environment == 'dev' || $this->environment == 'development') {
            $this->url = 'https://merchant-api-sandbox.shipper.id/';
        }
    }

    public function asPlugin($params = []) {
        $options = [];
        $options['apiKey'] = carr::get($params, 'apiKey', $this->key);
        $options['apiType'] = carr::get($params, 'apiType', 'starter');

        return new CVendor_Shipper_Plugin($options);
    }

    public function setKey($key) {
        $this->key = $key;
    }

    public function createMerchant($options) {
        $method = 'GET';

        $mandatoryKeys = [
            'phoneNumber',
            'email',
            'password',
            'fullName',
            'companyName',
            'address',
            'direction',
            'cityID',
            'postcode',
            'isCustomAWB',
            'merchantLogo',
            'isAutoTrack',
        ];

        // VALIDATION

        foreach ($mandatoryKeys as $key) {
            if (!isset($options[$key])) {
                throw new Exception('Key ' . $key . ' is required.');
            }
        }

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'public/v1/merchants?apiKey=' . $this->key,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => http_build_query($options),
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            return $this->error($err);
        } else {
            return $this->response($response);
        }
    }

    public function getMerchants($phone = '') {
        $method = 'GET';
        $options = [
            'apiKey' => $this->key,
        ];

        if ($phone) {
            $options['phone'] = $phone;
        }

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'public/v1/merchants?' . http_build_query($options),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            return $this->error($err);
        } else {
            return $this->response($response);
        }
    }

    public function updateMerchant($merchantId, $options) {
        $method = 'PUT';
        $errCode = 0;

        $parameterKeys = [
            'apiKey',
            'phoneNumber',
            'fullName',
            'companyName',
        ];

        // VALIDATION

        foreach ($parameterKeys as $key) {
            if (!isset($options[$key])) {
                $errCode++;
            }
        }

        if ($errCode == count($parameterKeys)) {
            throw new Exception('There must be at least one parameter. Parameter: ' . implode(',', $parameterKeys));
        }

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'public/v1/merchants/' . $merchantId . '?apiKey=' . $this->key,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => http_build_query($options),
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            return $this->error($err);
        } else {
            return $this->response($response);
        }
    }

    public function subscription($activation, $options) {
        $method = 'PUT';
        $errCode = 0;

        $parameterKeys = [
            'merchantLogo',
            'merchantAds',
        ];

        if ($activation) {
            foreach ($parameterKeys as $key) {
                if (!isset($options[$key])) {
                    $errCode++;
                }
            }

            if ($errCode == count($parameterKeys)) {
                throw new Exception('There must be at least one parameter. Parameter: ' . implode(',', $parameterKeys));
            }
        }

        $options['customAWB'] = $activation;

        // VALIDATION

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'public/v1/subscriptions?apiKey=' . $this->key,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => http_build_query($options),
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            return $this->error($err);
        } else {
            return $this->response($response);
        }
    }

    public function getCountriesById($countryId) {
        $endPoint = $this->url . 'v3/location/country/' . $countryId;
        $response = $this->requestToShipper($endPoint, 'GET');

        return $response;
    }

    public function getCountries() {
        $endPoint = $this->url . 'v3/location/countries?';
        $response = $this->requestToShipper($endPoint, 'GET');

        return $response;
    }

    public function getProvinces() {
        $endPoint = $this->url . 'v3/location/provinces?';
        $response = $this->requestToShipper($endPoint, 'GET');

        return $response;
    }

    public function getProvinceById($provinceId) {
        $endPoint = $this->url . 'v3/location/province/' . $provinceId;
        $response = $this->requestToShipper($endPoint, 'GET');

        return $response;
    }

    public function getProvincesByCountryId($countryId) {
        $endPoint = $this->url . 'v3/location/country/' . $countryId . '/provinces/';
        $response = $this->requestToShipper($endPoint, 'GET');

        return $response;
    }

    public function getCities() {
        $endPoint = $this->url . 'v3/location/cities?';
        $response = $this->requestToShipper($endPoint, 'GET');

        return $response;
    }

    public function getCitiesByProvinceId($provinceId) {
        $endPoint = $this->url . 'v3/location/province/' . $provinceId . '/cities/';
        $response = $this->requestToShipper($endPoint, 'GET');

        return $response;
    }

    public function getCityById($cityId) {
        $endPoint = $this->url . 'v3/location/city/' . $cityId;
        $response = $this->requestToShipper($endPoint, 'GET');

        return $response;
    }

    public function getSuburbs() {
        $endPoint = $this->url . 'v3/location/suburbs?';
        $response = $this->requestToShipper($endPoint, 'GET');

        return $response;
    }

    public function getSuburbsByCityId($cityId) {
        $endPoint = $this->url . 'v3/location/city/' . $cityId . '/suburbs/';
        $response = $this->requestToShipper($endPoint, 'GET');

        return $response;
    }

    public function getSuburbById($suburbId) {
        $endPoint = $this->url . 'v3/location/suburb/' . $suburbId;
        $response = $this->requestToShipper($endPoint, 'GET');

        return $response;
    }

    /**
     * Get Pricings Domestic.
     *
     * @param array $destination
     * @param array $origin      s
     * @param int   $weight      kg
     * @param float $itemValue   rp
     * @param float $height      cm
     * @param float $length      cm
     * @param float $width       cm
     * @param bool  $forOrder
     * @param int   $limit
     * @param int   $page
     * @param bool  $cod
     * @param mixed $sortBy
     *
     * @return void
     */
    public function getPricingDomestic(
        $destination,
        $origin,
        $weight,
        $itemValue,
        $height,
        $length,
        $width,
        $forOrder = false,
        $sortBy = [],
        $limit = 30,
        $page = 1,
        $cod = false
    ) {
        $data = [];
        $data['cod'] = $cod;
        $data['destination'] = $destination;
        $data['origin'] = $origin;
        $data['weight'] = $weight;
        $data['item_value'] = $itemValue;
        $data['height'] = $height;
        $data['length'] = $length;
        $data['width'] = $width;
        $data['for_order'] = $forOrder;
        $data['limit'] = $limit;
        $data['page'] = $page;
        if (count($sortBy) > 0) {
            $data['sort_by'] = $sortBy;
        }
        $endPoint = $this->url . 'v3/pricing/domestic';
        $response = $this->requestToShipper($endPoint, 'POST', $data);

        return $response;
    }

    /**
     * Get Pricings Domestic.
     *
     * @param string $rateTypes   ['intant', 'regular', 'express', 'trucking', 'same-day']
     * @param array  $destination
     * @param array  $origin      s
     * @param int    $weight      kg
     * @param float  $itemValue   rp
     * @param float  $height      cm
     * @param float  $length      cm
     * @param float  $width       cm
     * @param bool   $forOrder
     * @param int    $limit
     * @param int    $page
     * @param bool   $cod
     * @param mixed  $sortBy
     *
     * @return void
     */
    public function getPricingDomesticByRates(
        $rateTypes,
        $destination,
        $origin,
        $weight,
        $itemValue,
        $height,
        $length,
        $width,
        $forOrder = false,
        $sortBy = [],
        $limit = 30,
        $page = 1,
        $cod = false
    ) {
        $data = [];
        $data['cod'] = $cod;
        $data['destination'] = $destination;
        $data['origin'] = $origin;
        $data['weight'] = $weight;
        $data['item_value'] = $itemValue;
        $data['height'] = $height;
        $data['length'] = $length;
        $data['width'] = $width;
        $data['for_order'] = $forOrder;
        $data['limit'] = $limit;
        $data['page'] = $page;
        if ($sortBy != 2) {
            $data['sort_by'] = $sortBy;
        }
        $endPoint = $this->url . 'v3/pricing/domestic/' . $rateTypes;
        $response = $this->requestToShipper($endPoint, 'POST', $data);

        return $response;
    }

    public function createOrder(
        $consignee,
        $courier,
        $coverage,
        $destination,
        $origin,
        $package,
        $paymentType,
        $externalId = '',
        $consigner = [],
        $serviceType = '',
        $bestPrice = false
    ) {
        $data = [];
        $data['consignee'] = $consignee;
        if (count($consigner) > 0) {
            $data['consigner'] = $consigner;
        }
        $data['courier'] = $courier;
        $data['coverage'] = $coverage;
        $data['destination'] = $destination;
        $data['origin'] = $origin;
        $data['package'] = $package;
        if (strlen($externalId) > 0) {
            $data['external_id'] = $externalId;
        }
        $data['payment_type'] = $paymentType;
        if (strlen($serviceType) > 0) {
            $data['service_type'] = $serviceType;
        }
        $data['best_price'] = $bestPrice;
        $endPoint = $this->url . 'v3/order/';
        $response = $this->requestToShipper($endPoint, 'POST', $data);
        $response['request'] = $data;

        return $response;
    }

    /**
     * Retrieves tracking ID of the order with the provided ID.
     *
     * @param int $orderId the ID retrieved after creating the order
     *
     * @method getOrder
     *
     * @return object JSON Results
     */
    public function getOrder($orderId) {
        $data = [];
        $endPoint = $this->url . 'v3/order/' . $orderId;
        $response = $this->requestToShipper($endPoint, 'GET', $data);

        return $response;
    }

    /**
     * Create pickup order.
     *
     *  $order = array(
     *      'order_id'      => ['21CWXMW82GEWV'],          // the id
     *      'pickup_time'   => '2021-12-30T19:00:00+07:00',     // the class
     *  );
     *
     * @param mixed $orderActivation
     *
     * @return object JSON Results
     */
    public function createPickUpOrder($orderActivation) {
        $data = [];
        $data['data']['order_activation'] = $orderActivation;
        $endPoint = $this->url . 'v3/pickup/';
        $response = $this->requestToShipper($endPoint, 'POST', $data);

        return $response;
    }

    /**
     * Create pickup order with timeslot.
     *
     *  $order = array(
     *      'order_id'      => ['21CWXMW82GEWV'],          // the id
     *      'start_time'   => '2021-12-30T19:00:00+07:00',     // the class
     *      'end_time'   => '2021-12-30T19:00:00+07:00',     // the class
     *      'timezone'   => 'Asia/Jakarta',     // the class
     *  );
     *
     * @param mixed $orderActivation
     *
     * @return object JSON Results
     */
    public function createPickUpOrderWithTimeslot($orderActivation) {
        $data = [];
        $data['data']['order_activation'] = $orderActivation;
        $endPoint = $this->url . 'v3/pickup/timeslot';
        $response = $this->requestToShipper($endPoint, 'POST', $data);

        return $response;
    }

    public function getTimeslot($timeZone = 'Asia/Jakarta') {
        $data = [];
        $data['time_zone'] = $timeZone;
        $endPoint = $this->url . 'v3/pickup/timeslot';
        $response = $this->requestToShipper($endPoint, 'GET', $data);

        return $response;
    }

    public function getAreas($suburbId) {
        $method = 'GET';
        $options = [
            'apiKey' => $this->key,
            'suburb' => $suburbId,
        ];

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'public/v1/areas?' . http_build_query($options),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            return $this->error($err);
        } else {
            return $this->response($response);
        }
    }

    public function search($value) {
        $method = 'GET';
        $options = [
            'apiKey' => $this->key,
        ];

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'public/v1/details/' . $value . '?' . http_build_query($options),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            return $this->error($err);
        } else {
            return $this->response($response);
        }
    }

    /**
     * Retrieve every rates based on the submitted query string parameters. This endpoint requires area ID for its o and d parameter.
     *
     * @param int $origin      origin area ID. Obtained from getAreas.
     * @param int $destination destination area ID. Obtained from getAreas.
     * @param int $weight      package's weight (float in kilograms e.g. 1.5). The allowance for each logistic will be calculated automatically.
     * @param int $length      package's length (integer in centimeter e.g 10)
     * @param int $width       package's width (integer in centimeter e.g 10)
     * @param int $height      package's height (integer in centimeter e.g 10)
     * @param int $value       package's value/price (integer in IDR e.g 100000)
     * @param int $type        package type ID (1 for documents; 2 for small packages[DEFAULT]; and 3 for medium-sized packages)
     * @param int $cod         is this a Cash on Delivery shipment? (1 for yes; 0 for no[DEFAULT])
     * @param int $order       is this a Rate Checking only or is this for a valid Transaction Order? (1 for yes; 0 for no[DEFAULT])
     *
     * @method getDomesticRates
     *
     * @return object JSON Results
     */
    public function getDomesticRates(
        $origin,
        $destination,
        $weight,
        $length,
        $width,
        $height,
        $value,
        $type = 2,
        $cod = 0,
        $order = 0
    ) {
        $method = 'GET';
        $options = [
            'apiKey' => $this->key,
            'o' => $origin,
            'd' => $destination,
            'wt' => $weight,
            'l' => $length,
            'w' => $width,
            'h' => $height,
            'v' => $value,
            'type' => $type,
            'cod' => $cod,
            'order' => $order,
        ];

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'public/v1/domesticRates?' . http_build_query($options),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            return $this->error($err);
        } else {
            return $this->response($response);
        }
    }

    /**
     * Retrieve rate for International shipment.
     *
     * @param int $destination destination country ID. Obtained from getCountries.
     * @param int $weight      package's weight (double in kilograms e.g. 1.40).
     * @param int $length      package's length (integer in centimeter e.g 10)
     * @param int $width       package's width (integer in centimeter e.g 10)
     * @param int $height      package's height (integer in centimeter e.g 10)
     * @param int $value       package's value/price (integer in IDR e.g 100000)
     * @param int $type        package type ID (1 for documents; 2 for small parcels[DEFAULT]; and 3 for medium-sized parcels)
     * @param int $order       is this a Rate Checking only or is this for a valid Transaction Order? (1 for yes; 0 for no[DEFAULT])
     *
     * @method getDomesticRates
     *
     * @return object JSON Results
     */
    public function getInternationalRates(
        $destination,
        $weight,
        $length,
        $width,
        $height,
        $value,
        $type = 2,
        $order = 0
    ) {
        $method = 'GET';
        $options = [
            'apiKey' => $this->key,
            'd' => $destination,
            'wt' => $weight,
            'l' => $length,
            'w' => $width,
            'h' => $height,
            'v' => $value,
            'type' => $type,
            'order' => $order,
        ];

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'public/v1/intlRates?' . http_build_query($options),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            return $this->error($err);
        } else {
            return $this->response($response);
        }
    }

    /**
     * Creates a delivery order whose rate is the result of getDomesticRates.
     * By default, every order is not activated so you must activate it manually.
     * The id returned is not our tracking ID.
     * You need to retrieve the tracking ID from getOrder and use that as actual order ID.
     *
     * @param int $origin      origin area ID
     * @param int $destination destination area ID
     * @param float/integer $weight               package's weight
     * @param float/integer $length               package's length
     * @param float/integer $width                package's width
     * @param float/integer $height               package's height
     * @param int    $value                item's price (IDR e.g. 100000)
     * @param int    $rateId               rate ID as you choose from rate search result
     * @param string $consignerName        consigner's name
     * @param string $consignerPhoneNumber consigner's phone number (with country code)
     * @param string $originAddress        origin address
     * @param string $originDirection      hints of the location e.g. in front of drug store K-12, etc (can be empty)
     * @param string $consigneeName        consignee's name
     * @param string $consigneePhoneNumber consignee's phone number (with country code)
     * @param string $destinationAddress   destination address
     * @param string $destinationDirection hints of the location e.g. in front of drug store K-1, etc (can be empty)
     * @param string $itemName             item name - ie: Shoes
     * @param string $contents             item description - ie: One pair of red shoes
     * @param int    $useInsurance         is Insurance needed? (1 for yes; 0 for no). If compulsory insurance is flagged by system, then this does not make any difference
     * @param int    $packageType          package type ID (1 for documents; 2 for small packages; 3 for medium-sized packages)
     * @param string $externalId           the merchant's self-tailored order ID (optional - Unique)
     * @param string $paymentType          payment type for the user's orders. Valid values are currently cash and the default value : postpay (optional)
     * @param int    $cod                  is this a COD order? Please note there is a fee for COD Order. Accepted paymentType for COD is postpay. (1 for yes; 0 for no [default])
     *
     * @method domesticOrder
     *
     * @return object JSON Results
     */
    public function domesticOrder(
        $origin,
        $destination,
        $weight = '',
        $length = '',
        $width = '',
        $height = '',
        $value = null,
        $rateId = null,
        $consignerName = '',
        $consignerPhoneNumber = '',
        $originAddress = null,
        $originDirection = '',
        $consigneeName = null,
        $consigneePhoneNumber = null,
        $destinationAddress = null,
        $destinationDirection = '',
        $itemName = null,
        $contents = '',
        $useInsurance = 0,
        $packageType = null,
        $externalId = '',
        $paymentType = 'postpay',
        $cod = 0
    ) {
        $method = 'POST';
        $options = [
            'o' => $origin,
            'd' => $destination,
            'v' => $value,
            'originAddress' => $originAddress,
            'consigneeName' => $consigneeName,
            'consigneePhoneNumber' => $consigneePhoneNumber,
            'destinationAddress' => $destinationAddress,
            'itemName' => $itemName,
            'useInsurance' => $useInsurance,
            'packageType' => $packageType,
            'paymentType' => $paymentType,
            'cod' => $cod,
        ];

        if ($weight) {
            $options['weight'] = $weight;
        }
        if ($length) {
            $options['length'] = $length;
        }
        if ($width) {
            $options['width'] = $width;
        }
        if ($height) {
            $options['height'] = $height;
        }
        if ($consignerName) {
            $options['consignerName'] = $consignerName;
        }
        if ($consignerPhoneNumber) {
            $options['consignerPhoneNumber'] = $consignerPhoneNumber;
        }
        if ($originDirection) {
            $options['originDirection'] = $originDirection;
        }
        if ($destinationDirection) {
            $options['destinationDirection'] = $destinationDirection;
        }
        if ($contents) {
            $options['contents'] = $contents;
        }
        if ($externalId) {
            $options['externalId'] = $externalId;
        }

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'public/v1/orders/domestics?apiKey=' . $this->key,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => http_build_query($options),
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            return $this->error($err);
        } else {
            return $this->response($response);
        }
    }

    /**
     * Creates a delivery order whose rate is the result of getDomesticRates.
     * By default, every order is not activated so you must activate it manually.
     * The id returned is not our tracking ID.
     * You need to retrieve the tracking ID from getOrder and use that as actual order ID.
     *
     * @param int $origin      origin area ID
     * @param int $destination destination country ID
     * @param float/integer $weight               package's weight
     * @param float/integer $length               package's length
     * @param float/integer $width                package's width
     * @param float/integer $height               package's height
     * @param int    $value                item's price (IDR e.g. 100000)
     * @param int    $rateId               rate ID as you choose from rate search result
     * @param string $consignerName        consigner's name
     * @param string $consignerPhoneNumber consigner's phone number (with country code)
     * @param string $originAddress        origin address
     * @param string $originDirection      hints of the location e.g. in front of drug store K-12, etc (can be empty)
     * @param string $consigneeName        consignee's name
     * @param string $consigneePhoneNumber consignee's phone number (with country code)
     * @param string $destinationAddress   destination address
     * @param string $destinationDirection hints of the location e.g. in front of drug store K-1, etc (can be empty)
     * @param string $destinationArea      destination area (can be empty)
     * @param string $destinationSuburb    destination suburb (can be empty)
     * @param string $destinationCity      destination city (can be empty)
     * @param string $destinationProvince  destination province (can be empty)
     * @param string $destinationPostcode  destination postcode (can be empty)
     * @param string $itemName             item name - ie: Shoes
     * @param string $contents             item description - ie: One pair of red shoes
     * @param int    $useInsurance         is Insurance needed? (1 for yes; 0 for no). If compulsory insurance is flagged by system, then this does not make any difference
     * @param int    $packageType          package type ID (1 for documents; 2 for small packages; 3 for medium-sized packages)
     * @param string $externalId           the merchant's self-tailored order ID (optional - Unique)
     * @param string $paymentType          payment type for the user's orders. Valid values are currently cash and the default value : postpay (optional)
     *
     * @method internationalOrder
     *
     * @return object JSON Results
     */
    public function internationalOrder(
        $origin,
        $destination,
        $weight = '',
        $length = '',
        $width = '',
        $height = '',
        $value = null,
        $rateId = null,
        $consignerName = '',
        $consignerPhoneNumber = '',
        $originAddress = null,
        $originDirection = '',
        $consigneeName = null,
        $consigneePhoneNumber = null,
        $destinationAddress = null,
        $destinationDirection = '',
        $destinationArea = null,
        $destinationSuburb = null,
        $destinationCity = null,
        $destinationProvince = null,
        $destinationPostcode = null,
        $itemName = null,
        $contents = '',
        $useInsurance = 0,
        $packageType = null,
        $externalId = '',
        $paymentType = 'postpay'
    ) {
        $method = 'POST';
        $options = [
            'apiKey' => $this->key,
            'o' => $origin,
            'd' => $destination,
            'v' => $value,
            'originAddress' => $originAddress,
            'consigneeName' => $consigneeName,
            'consigneePhoneNumber' => $consigneePhoneNumber,
            'destinationAddress' => $destinationAddress,
            'destinationArea' => $destinationArea,
            'destinationSuburb' => $destinationSuburb,
            'destinationProvince' => $destinationProvince,
            'destinationPostcode' => $destinationPostcode,
            'itemName' => $itemName,
            'useInsurance' => $useInsurance,
            'packageType' => $packageType,
            'paymentType' => $paymentType,
        ];

        if ($weight) {
            $options['weight'] = $weight;
        }
        if ($length) {
            $options['length'] = $length;
        }
        if ($width) {
            $options['width'] = $width;
        }
        if ($height) {
            $options['height'] = $height;
        }
        if ($consignerName) {
            $options['consignerName'] = $consignerName;
        }
        if ($consignerPhoneNumber) {
            $options['consignerPhoneNumber'] = $consignerPhoneNumber;
        }
        if ($originDirection) {
            $options['originDirection'] = $originDirection;
        }
        if ($destinationDirection) {
            $options['destinationDirection'] = $destinationDirection;
        }
        if ($contents) {
            $options['contents'] = $contents;
        }
        if ($externalId) {
            $options['externalId'] = $externalId;
        }

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'public/v1/orders/internationals',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => http_build_query($options),
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            return $this->error($err);
        } else {
            return $this->response($response);
        }
    }

    /**
     * Activate/deactivate an order. Such activation will initiate Shipper’s pickup process.
     *
     * @param string $orderId order ID obtained from Order Creation or order tracking ID
     * @param int    $active  integer (0 for order deactivation and 1 for its activation)
     *
     * @method orderActivation
     *
     * @return object JSON Results
     */
    public function orderActivation($orderId, $active) {
        $method = 'PUT';
        $options = [
            'active' => $active,
        ];

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'public/v1/activations/' . $orderId . '?apiKey=' . $this->key,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => http_build_query($options),
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            return $this->error($err);
        } else {
            return $this->response($response);
        }
    }

    /**
     * Retrieves an order’s detail.
     *
     * @param string $orderId order ID obtained from Order Creation or order tracking ID
     *
     * @method getOrderDetail
     *
     * @return object JSON Result
     */
    public function getOrderDetail($orderId) {
        $method = 'GET';
        $options = [
            'apiKey' => $this->key,
        ];

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'public/v1/orders/' . $orderId . '?' . http_build_query($options),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            return $this->error($err);
        } else {
            return $this->response($response);
        }
    }

    /**
     * Retrieve Label Checksum from getOrderDetail.
     *
     * @param string $orderId       order ID obatined from Order Creation or order tracking ID
     * @param string $labelChecksum labelChecksum obtained from getOrderDetail
     *
     * @method getLabelChecksum
     *
     * @return string Label Checksum URL
     */
    public function getLabelChecksum($orderId, $labelChecksum) {
        if ($this->environment == 'dev' || $this->environment == 'development') {
            return 'https://shipper.id/label-dev/sticker.php?oid=' . $orderId . '&uid=' . $labelChecksum;
        }

        return 'https://shipper.id/label/sticker.php?oid=' . $orderId . '&uid=' . $labelChecksum;
    }

    /**
     * Generate Airway Bill number of the order with the provided external ID or the order ID (you must provide either one of those).
     *
     * @param array $options eid (external ID) or oid (order ID)
     *
     * @method generateAWB
     *
     * @return object JSON Results
     */
    public function generateAWB($options) {
        $method = 'GET';
        $errCode = 0;

        $parameterKeys = [
            'eid',
            'oid',
        ];

        foreach ($parameterKeys as $key) {
            if (!isset($options[$key])) {
                $errCode++;
            }
        }

        if ($errCode == count($parameterKeys)) {
            throw new Exception('There must be at least one parameter. Parameter: ' . implode(',', $parameterKeys));
        }

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'public/v1/awbs/generate?' . http_build_query($options),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            return $this->error($err);
        } else {
            return $this->response($response);
        }
    }

    /**
     * Retrieves Airway Bill number of the order with the provided external ID or the order ID (you must provide either one of those).
     *
     * @param array $options eid (external ID) or oid (order ID)
     *
     * @method getAWB
     *
     * @return object JSON Results
     */
    public function getAWB($options) {
        $method = 'GET';
        $errCode = 0;

        $parameterKeys = [
            'eid',
            'oid',
        ];

        foreach ($parameterKeys as $key) {
            if (!isset($options[$key])) {
                $errCode++;
            }
        }

        if ($errCode == count($parameterKeys)) {
            throw new Exception('There must be at least one parameter. Parameter: ' . implode(',', $parameterKeys));
        }

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'public/v1/awbs?' . http_build_query($options),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            return $this->error($err);
        } else {
            return $this->response($response);
        }
    }

    /**
     * Updates an order’s AWB number.
     *
     * @param string $orderId   order ID obtained from Order Creation or order tracking ID
     * @param string $awbNumber airway bill number
     *
     * @method updateAWB
     *
     * @return object JSON Results
     */
    public function updateAWB($orderId, $awbNumber) {
        $method = 'PUT';
        $options = [
            'awbNumber' => $awbNumber,
        ];

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'public/v1/awbs/' . $orderId . '?apiKey=' . $this->key,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => http_build_query($options),
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            return $this->error($err);
        } else {
            return $this->response($response);
        }
    }

    /**
     * Update an order’s package’s weight and dimension.
     *
     * @param string $orderId order ID obtained from Order Creation or order tracking ID
     * @param int    $weight  [description]
     * @param int    $length  [description]
     * @param int    $height  [description]
     * @param int    $width   [description]
     *
     * @method updateOrder
     *
     * @return object JSON Results
     */
    public function updateOrder($orderId, $weight, $length, $height, $width) {
        $method = 'PUT';
        $options = [
            'weight' => $weight,
            'length' => $length,
            'height' => $height,
            'width' => $width,
        ];

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'public/v1/orders/' . $orderId . '?apiKey=' . $this->key,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => http_build_query($options),
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            return $this->error($err);
        } else {
            return $this->response($response);
        }
    }

    /**
     * Cancel an order.
     *
     * @param string $orderId order ID obtained from Order Creation or order tracking ID
     *
     * @method cancelOrder
     *
     * @return object JSON Results
     */
    public function cancelOrder($orderId) {
        $method = 'PUT';
        $options = [
            'apiKey' => $this->key,
        ];

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'public/v1/orders/' . $orderId . '/cancel/?' . http_build_query($options),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            return $this->error($err);
        } else {
            return $this->response($response);
        }
    }

    /**
     * Update subscription data in orders so you can determine which orders have the benefit of subscription.
     *
     * @param string $orderId order ID or order tracking ID
     * @param array  $options customAWB or autoTrack
     *
     * @method updateSubscription
     *
     * @return object JSON Results
     */
    public function updateSubscription($orderId, $options) {
        $method = 'PUT';
        $errCode = 0;

        $parameterKeys = [
            'customAWB',
            'autoTrack',
        ];

        foreach ($parameterKeys as $key) {
            if (!isset($options[$key])) {
                $errCode++;
            }
        }

        if ($errCode == count($parameterKeys)) {
            throw new Exception('There must be at least one parameter. Parameter: ' . implode(',', $parameterKeys));
        }

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'public/v1/subscriptions/' . $orderId . '?apiKey=' . $this->key,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => http_build_query($options),
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            return $this->error($err);
        } else {
            return $this->response($response);
        }
    }

    /**
     * Retrieves the user’s order history.
     * If parameter merchantID is present, then order history displayed belongs to that particular merchant.
     * If parameter phone is present, then the history displayed is that of orders whose consigner’s phone number is the argument provided.
     * Both phone and merchantID could be combined in one request.
     * The orders are those that are sent by the user.
     * Date format is UTC time.
     *
     * @param string $merchantId merchant’s ID
     * @param string $phone      consigner’s phone number with country code e.g. +6281112343231
     * @param string $limit      how many orders will be displayed in single response. Default value : 20.
     * @param string $startDate  retrieve orders created at and after the date in UTC time (YYYY-MM-DDThh:mm:ss+00:00)
     * @param string $endDate    retrieve orders created at and before the date in UTC time (YYYY-MM-DDThh:mm:ss+00:00)
     * @param string $page       page number to be shown from total number of possible pages in this request (totalRecord / limit)
     *
     * @method getOrderHistory
     *
     * @return object JSON Results
     */
    public function getOrderHistory($merchantId = '', $phone = '', $limit = '', $startDate = '', $endDate = '', $page = '') {
        $method = 'GET';
        $options = [
            'apiKey' => $this->key,
        ];

        if ($merchantId) {
            $options['merchantID'] = $merchantId;
        }
        if ($phone) {
            $options['phone'] = $phone;
        }
        if ($limit) {
            $options['limit'] = $limit;
        }
        if ($startDate) {
            $options['startDate'] = $startDate;
        }
        if ($endDate) {
            $options['endDate'] = $endDate;
        }
        if ($page) {
            $options['page'] = $page;
        }

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'public/v1/histories/orders?' . http_build_query($options),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            return $this->error($err);
        } else {
            return $this->response($response);
        }
    }

    /**
     * Retrieve every available logistic in a city.
     *
     * @param int $cityId City ID
     *
     * @method getLogistics
     *
     * @return object JSON Results
     */
    public function getLogistics($cityId) {
        $method = 'GET';
        $options = [
            'apiKey' => $this->key,
        ];

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'public/v1/logistics/' . $cityId . '?' . http_build_query($options),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            return $this->error($err);
        } else {
            return $this->response($response);
        }
    }

    /**
     * Retrieves shipper Tracking Status.
     *
     * @method track
     *
     * @return object JSON Results
     */
    public function track() {
        $method = 'GET';
        $options = [
            'apiKey' => $this->key,
        ];

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'public/v1/logistics/status?' . http_build_query($options),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        if ($err) {
            return $this->error($err);
        } else {
            return $this->response($response);
        }
    }

    protected function requestToShipper($endPoint, $method, $data = null) {
        $curl = curl_init();
        $headers = [];
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'X-API-Key: ' . $this->key;

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, $endPoint);
        if ($method == 'POST') {
            if ($data != null) {
                $payload = json_encode($data);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
            }
        } else {
            if ($data != null) {
                $payload = curl::asPostString($data);
                curl_setopt($curl, CURLOPT_URL, $endPoint . '?' . $payload);
            }
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        curl_close($curl);
        $responseObject = json_decode($response, true);

        return $responseObject;
    }

    private function response($res) {
        return json_decode($res);
    }

    private function error($err) {
        return (object) [
            'status' => 'fail',
            'data' => $err,
        ];
    }
}

<?php

class CVendor_GoSend {
    private $clientId;
    private $passKey;
    private $url;
    private $environment;
    private $required;

    public function __construct($environment = 'production') {
        $this->environment = $environment;
        $this->url = 'https://kilat-api.gojekapi.com';

        if ($this->environment == 'dev' || $this->environment) {
            $this->url = 'https://integration-kilat-api.gojekapi.com';
        }

        $this->required = [
            'originName',
            'originContactName',
            'originContactPhone',
            'originLatLong',
            'originAddress',
            'destinationName',
            'destinationContactName',
            'destinationContactPhone',
            'destinationLatLong',
            'destinationAddress',
            'itemName',
            'storeOrderId',
            'insurance',
            'insuranceFee',
            'productDescription',
            'productPrice',
        ];
    }

    public function setClientId($clientId) {
        $this->clientId = $clientId;
        return $this;
    }

    public function setPassKey($passKey) {
        $this->passKey = $passKey;
        return $this;
    }

    /**
     * Returns the pricing estimate
     *
     * @param integer $paymentType 0 = cash, 3 = corporate
     * @param string  $origin      comma separated origin latitude and longitude
     * @param string  $destination comma separated destination latitude and longitude
     *
     * @return JSON responses
     */
    public function calculatePrice($paymentType, $origin, $destination) {
        if ($paymentType != 0 && $paymentType != 3) {
            throw new Exception('paymentType value should be 0 for Cash or 3 for Corporate');
        }

        $params = [
            'paymentType' => $paymentType,
            'origin' => $origin,
            'destination' => $destination,
        ];

        $curl = CCurl::factory($this->url . '/gokilat/v10/calculate/price?' . http_build_query($params));
        $curl->setHttpHeader([
            'Content-Type: application/json',
            'Client-ID: ' . $this->clientId,
            'Pass-Key: ' . $this->passKey,
        ]);

        $curl->exec();

        if ($curl->has_error()) {
            throw new Exception($curl->has_error());
        }

        return $curl->response();
    }

    /**
     * Creates a new booking
     *
     * @param integer $paymentType    0 = Cash, 3 = Corporate
     * @param string  $shipmentMethod Instant or SameDay
     * @param array   $shippingData   Data Shipping
     *
     * @method booking
     *
     * @return JSON responses
     */
    public function booking($paymentType, $shipmentMethod, array $shippingData) {
        $curl = CCurl::factory($this->url . '/gokilat/v10/booking');
        $curl->setHttpHeader([
            'Content-Type: application/json',
            'Client-ID: ' . $this->clientId,
            'Pass-Key: ' . $this->passKey,
        ]);

        if ($paymentType != 0 && $paymentType != 3) {
            throw new Exception('paymentType value must be 0 for Cash or 3 for Corporate');
        }

        if ($shipmentMethod != 'Instant' && $shipmentMethod != 'SameDay') {
            throw new Exception('shipmentMethod value must be Instant or SameDay');
        }

        foreach ($this->required as $value) {
            ${$value} = carr::get($shippingData, $value);
            switch ($value) {
                case 'insurance':
                    if (!is_bool(${$value})) {
                        throw new Exception("$value type data must be boolean");
                    }
                    break;
                case 'insuranceFee':
                    if ($insurance) {
                        if (${$value} === null) {
                            throw new Exception("$value is required");
                        }
                    }
                    break;
                default:
                    if (${$value} === null) {
                        throw new Exception("$value is required");
                    }
                    break;
            }
        }

        $originNote = carr::get($shippingData, 'originNote');
        $destinationNote = carr::get($shippingData, 'destinationNote');

        $post = [
            'paymentType' => $paymentType,
            'collection_location' => 'pickup',
            'shipment_method' => $shipmentMethod,
            'routes' => [
                [
                    'originName' => $originName,
                    'originNote' => $originNote,
                    'originContactName' => $originContactName,
                    'originContactPhone' => $originContactPhone,
                    'originLatLong' => $originLatLong,
                    'originAddress' => $originAddress,
                    'destinationName' => $destinationName,
                    'destinationNote' => $destinationNote,
                    'destinationContactName' => $destinationContactName,
                    'destinationContactPhone' => $destinationContactPhone,
                    'destinationLatLong' => $destinationLatLong,
                    'destinationAddress' => $destinationAddress,
                    'item' => $itemName,
                    'storeOrderId' => $storeOrderId,
                    'insuranceDetails' => [
                        'applied' => $insurance,
                        'fee' => $insuranceFee,
                        'product_description' => $productDescription,
                        'product_price' => $productPrice,
                    ],
                ],
            ],
        ];

        $curl->setRawPost(json_encode($post));
        $curl->exec();

        if ($curl->has_error()) {
            throw new Exception($curl->has_error());
        }

        return $curl->response();
    }

    /**
     * Show booking details
     *
     * @param string $orderNo GO-SEND Booking Order Number
     *
     * @method getDetails
     *
     * @return string JSON responses
     */
    public function getDetails($orderNo) {
        $curl = CCurl::factory($this->url . '/gokilat/v10/booking/orderno/' . $orderNo);
        $curl->setHttpHeader([
            'Content-Type: application/json',
            'Client-ID: ' . $this->clientId,
            'Pass-Key: ' . $this->passKey,
        ]);

        $curl->exec();

        if ($curl->has_error()) {
            throw new Exception($curl->has_error());
        }

        return $curl->response();
    }

    /**
     * Show booking Details By Store Order Id
     *
     * @param string $storeOrderId Merchant's Order ID
     *
     * @method getDetailsByStoreOrderId
     *
     * @return JSON responses
     */
    public function getDetailsByStoreOrderId($storeOrderId) {
        $curl = CCurl::factory($this->url . '/gokilat/v10/booking/storeOrderId/' . $storeOrderId);
        $curl->setHttpHeader([
            'Content-Type: application/json',
            'Client-ID: ' . $this->clientId,
            'Pass-Key: ' . $this->passKey,
        ]);

        $curl->exec();

        if ($curl->has_error()) {
            throw new Exception($curl->has_error());
        }

        return $curl->response();
    }

    /**
     * Cancel Booking
     *
     * @param string $orderNo GO-SEND Booking Order Number
     *
     * @method cancelBooking
     *
     * @return JSON responses
     */
    public function cancelBooking($orderNo) {
        $curl = CCurl::factory($this->url . '/gokilat/v10/booking/cancel/');
        $curl->setHttpHeader([
            'Content-Type: application/json',
            'Client-ID: ' . $this->clientId,
            'Pass-Key: ' . $this->passKey,
        ]);

        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $curl->setOpt(CURLOPT_POSTFIELDS, ['orderNo' => $orderNo]);

        $curl->exec();

        if ($curl->has_error()) {
            throw new Exception($curl->has_error());
        }

        return $curl->response();
    }
}

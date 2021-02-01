<?php

class CVendor_RajaOngkir_Pro extends CVendor_RajaOngkir {
    public function __construct() {
        parent::__construct();
        // $this->url = 'https://pro.rajaongkir.com/api/';
        $this->url = '64.227.6.154/api/';
    }

    public function getProvince($provinceID = '') {
        $method = 'GET';
        $options = [
            'id' => $provinceID,
        ];

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'province?' . http_build_query($options),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'key: ' . $this->key
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

    public function getCity($provinceID = '', $cityID = '') {
        $method = 'GET';
        $options = [
            'id' => $cityID,
            'province' => $provinceID,
        ];

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'city?' . http_build_query($options),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'key: ' . $this->key
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

    public function getDistrict($cityID = '', $districtID = '') {
        $method = 'GET';
        $options = [
            'id' => $districtID,
            'city' => $cityID,
        ];

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'subdistrict?' . http_build_query($options),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'key: ' . $this->key
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

    public function getCost(
        $originType,
        $origin,
        $destinationType,
        $destination,
        $weight,
        $courier,
        $length = '',
        $width = '',
        $height = '',
        $diameter = ''
    ) {
        $method = 'POST';

        $options = [
            'origin' => $origin,
            'originType' => $originType,
            'destination' => $destination,
            'destinationType' => $destinationType,
            'weight' => $weight,
            'courier' => $courier,
            'length' => $length,
            'width' => $width,
            'height' => $height,
            'diameter' => $diameter,
        ];

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'cost',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => http_build_query($options),
            CURLOPT_HTTPHEADER => [
                'content-type: application/x-www-form-urlencoded',
                'key: ' . $this->key
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

    public function getInternationalOrigin($provinceID = '', $cityID = '') {
        $method = 'GET';
        $options = [
            'id' => $cityID,
            'province' => $provinceID,
        ];

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'v2/internationalOrigin?' . http_build_query($options),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'key: ' . $this->key
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

    public function getInternationalDestination($countryID = '') {
        $method = 'GET';
        $options = [
            'id' => $countryID,
        ];

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'v2/internationalDestination?' . http_build_query($options),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'key: ' . $this->key
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

    public function getInternationalCost(
        $origin,
        $destination,
        $weight,
        $courier,
        $length = '',
        $width = '',
        $height = ''
    ) {
        $method = 'POST';
        $options = [
            'origin' => $origin,
            'destination' => $destination,
            'weight' => $weight,
            'courier' => $courier,
            'length' => $length,
            'width' => $width,
            'height' => $height,
        ];

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'v2/internationalCost',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => http_build_query($options),
            CURLOPT_HTTPHEADER => [
                'content-type: application/x-www-form-urlencoded',
                'key: ' . $this->key
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

    public function getCurrency() {
        $method = 'GET';

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'currency',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'key: ' . $this->key
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

    public function getWaybill($courier, $waybill) {
        $method = 'POST';
        $options = [
            'waybill' => $waybill,
            'courier' => $courier,
        ];

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url . 'waybill',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => http_build_query($options),
            CURLOPT_HTTPHEADER => [
                'content-type: application/x-www-form-urlencoded',
                'key: ' . $this->key
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
}

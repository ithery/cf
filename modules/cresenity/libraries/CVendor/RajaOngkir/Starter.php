<?php

class CVendor_RajaOngkir_Starter extends CVendor_RajaOngkir {
    public function __construct() {
        parent::__construct();
        $this->url = 'https://api.rajaongkir.com/starter/';
        // $this->url = '68.183.25.19/starter/';
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

    public function getCost(
        $origin,
        $destination,
        $weight,
        $courier
    ) {
        $method = 'POST';
        $options = [
            'origin' => $origin,
            'destination' => $destination,
            'weight' => intval($weight),
            'courier' => $courier,
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
}

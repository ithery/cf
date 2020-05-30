<?php

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since May 30, 2020 
 * @license Ittron Global Teknologi
 */
class CXMPP_Ejabberd_Response {

    protected $errCode;
    protected $errMessage;
    protected $data;

    public function __construct($body) {
        $this->errCode = 0;
        $this->errMessage = '';
        $this->data = [];
        if ($body instanceof Exception) {
            $this->errCode = 9999;
            $this->errMessage = $body->getMessage;
            $this->data = [];
        }

        if (is_string($body)) {
            $body = json_decode($body, true);
        }

        if (is_array($body)) {
            $status = carr::get($body, 'status');
            if ($status != 'success') {
                $this->errCode++;
                $this->errMessage=carr::get($body, 'ejabberd');
            }

            $this->data = carr::get($body, 'ejabberd');
        }
        cdbg::dd($body);


        if (!is_array($body)) {
            $this->errCode = 10000;
            $this->errMessage = 'Unknown error';
        }
    }

    public function toArray() {
        return [
            'errCode' => $this->errCode,
            'errMessage' => $this->errMessage,
            'data' => $this->data,
        ];
    }

    public function data() {
        return $this->data;
    }

    public function toJson() {
        $array = $this->toArray();
        if (is_empty(carr::get($array, 'data', []))) {
            $array['data'] = new stdclass();
        }
        return json_encode($array);
    }

}

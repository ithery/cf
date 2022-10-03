<?php

trait CTrait_Controller_Application_OAuth_AccessToken {
    protected function getTitle() {
        return 'OAuth Access Token';
    }

    protected function getApiGroup() {
        return 'api';
    }

    public function index() {
        $app = c::app();
        $app->title($this->getTitle());

        $oauth = CApi::oauth($this->getApiGroup());

        $widget = $app->addWidget()->setTitle('Access Token Decoder')->setIcon('ti ti-layers');
        $form = $widget->addForm();
        $form->addField()->setLabel('Access Token')->addTextControl('access_token')->setValue(c::request()->access_token);
        if (c::request()->method() == 'POST') {
            $accessToken = c::request()->access_token;
            $appKey = c::env('APP_KEY');
            $encKey = base64_decode(substr($appKey, 7));
            $crypto = [];

            try {
                $crypto = static::dirtyDecode($accessToken);
            } catch (Exception $exception) {
                c::msg('error', $exception->getMessage());
            }

            $jsonDecrypted = json_encode($crypto, JSON_PRETTY_PRINT);
            $form->addPre()->addClass('my-3')->add($jsonDecrypted);
        }

        $form->addActionList()->addAction()->setLabel('Decode')->setSubmit();
        $table = $app->addTable();
        $table->setDataFromModel($oauth->tokenModel(), function (CModel_Query $q) {
            $q->with(['oauthClient']);
            $q->whereHas('oauthClient');

            $q->orderBy('created', 'desc');
        });
        $table->addColumn('oauthClient.name')->setLabel('Client');
        $table->addColumn('user_type')->setLabel('User Type');
        $table->addColumn('user_id')->setLabel('User ID');
        $table->addColumn('token')->setLabel('Token')->addTransform('showMore:10');
        $table->addColumn('name')->setLabel('Name');
        $table->addColumn('scopes')->setLabel('Scopes');
        $table->addColumn('revoked')->setLabel('Revoked')->addTransform('yesNo');
        $table->addColumn('expires_at')->setLabel('Expired')->addTransform('formatDatetime');
        $table->addColumn('created')->setLabel('Created')->addTransform('formatDatetime');
        $table->setAjax(true);

        return $app;
    }

    /**
     * Decode a Access Token.
     *
     * @param string $accessToken Access Token
     * @param array  $claims
     *
     * @return array
     */
    private static function dirtyDecode($accessToken, $claims = []) {
        $now = time();
        $expecting = false;
        $incorrect = false;
        $expired = false;
        $error = false;
        $errors = [];
        $tokenSegments = explode('.', $accessToken);
        //cdbg::dd($tokenSegments);
        $body = (isset($tokenSegments[1])) ? $tokenSegments[1] : null;

        if (count($tokenSegments) != 3) {
            $error = true;
            $errors[] = 'Token has wrong number of segments';
        }
        if (null === $data = static::jsonDecode(static::urlDecode($body))) {
            $error = true;
            $errors[] = 'Decoder has problem with Token encoding';
        }
        if (isset($data->nbf) && $data->nbf > $now) {
            $expecting = true;
        }
        if (isset($data->iat) && $data->iat > $now) {
            $incorrect = true;
        }
        if (isset($data->exp) && $now >= $data->exp) {
            $expired = true;
        }
        $decodedToken = [
            'token_id' => (isset($data->jti)) ? $data->jti : null,
            'user_id' => (isset($data->sub)) ? $data->sub : null,
            'expecting' => $expecting,
            'start_at_unix' => (isset($data->nbf)) ? $data->nbf : null,
            'start_at' => (isset($data->nbf)) ? date(DateTime::ISO8601, $data->nbf) : null,
            'incorrect' => $incorrect,
            'created_at_unix' => (isset($data->iat)) ? $data->iat : null,
            'created_at' => (isset($data->iat)) ? date(DateTime::ISO8601, $data->iat) : null,
            'expired' => $expired,
            'expires_at_unix' => (isset($data->exp)) ? $data->exp : null,
            'expires_at' => (isset($data->exp)) ? date(DateTime::ISO8601, $data->exp) : null,
            'error' => $error,
            'errors' => $errors,
            'valid' => ($expecting || $incorrect || $expired || $error) ? false : true
        ];

        if (!empty($claims)) {
            $decodedToken['claims'] = static::getCustomClaims($data, $claims);
        }

        return $decodedToken;
    }

    private static function urlDecode($input) {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }

        return base64_decode(strtr($input, '-_', '+/'));
    }

    private static function jsonDecode($input) {
        if (version_compare(PHP_VERSION, '5.4.0', '>=') && !(defined('JSON_C_VERSION') && PHP_INT_SIZE > 4)) {
            $obj = json_decode($input, false, 512, JSON_BIGINT_AS_STRING);
        } else {
            $max_int_length = strlen((string) PHP_INT_MAX) - 1;
            $json_without_bigints = preg_replace('/:\s*(-?\d{' . $max_int_length . ',})/', ': "$1"', $input);
            $obj = json_decode($json_without_bigints);
        }

        if (function_exists('json_last_error') && $errno = json_last_error()) {
            return null;
        } elseif ($obj === null && $input !== 'null') {
            return null;
        }

        return $obj;
    }

    private static function getCustomClaims($data, $claims) {
        $decodedToken = [];
        foreach ($claims as $claim) {
            foreach ($data as $key => $value) {
                if ($key == $claim) {
                    $decodedToken[$claim] = (isset($claim)) ? $value : null;
                }
            }
        }

        return $decodedToken;
    }
}

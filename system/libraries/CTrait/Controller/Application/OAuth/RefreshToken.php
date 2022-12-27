<?php
use Defuse\Crypto\Crypto;

trait CTrait_Controller_Application_OAuth_RefreshToken {
    protected function getTitle() {
        return 'OAuth Refresh Token';
    }

    protected function getApiGroup() {
        return 'api';
    }

    public function index() {
        $app = c::app();
        $app->title($this->getTitle());
        $oauth = CApi::oauth($this->getApiGroup());

        $widget = $app->addWidget()->setTitle('Refresh Token Decoder')->setIcon('ti ti-layers')->addClass('mb-3');
        $form = $widget->addForm();
        $form->addField()->setLabel('Refresh Token')->addTextControl('refresh_token')->setValue(c::request()->refresh_token);
        if (c::request()->method() == 'POST') {
            $refreshToken = c::request()->refresh_token;
            $appKey = c::env('APP_KEY');
            $encKey = base64_decode(substr($appKey, 7));
            $crypto = null;

            try {
                $crypto = Crypto::decryptWithPassword($refreshToken, $encKey);
            } catch (Exception $exception) {
                c::msg('error', $exception->getMessage());
            }

            $jsonDecrypted = json_encode(json_decode($crypto, true), JSON_PRETTY_PRINT);
            $form->addPre()->addClass('my-3')->add($jsonDecrypted);
        }

        $form->addActionList()->addAction()->setLabel('Decode')->setSubmit();
        $table = $app->addTable();

        $table->setDataFromModel($oauth->refreshTokenModel(), function (CModel_Query $q) {
            $q->with(['oauthAccessToken', 'oauthAccessToken.oauthClient']);
            $q->whereHas('oauthAccessToken');
            $q->whereHas('oauthAccessToken.oauthClient');
            $q->orderBy('created', 'desc');
        });
        $table->addColumn('oauthAccessToken.oauthClient.name')->setLabel('Client');
        $table->addColumn('oauthAccessToken.token')->setLabel('Access Token')->addTransform('showMore:10');
        $table->addColumn('token')->setLabel('Refresh Token')->addTransform('showMore:10');
        $table->addColumn('oauthAccessToken.user_type')->setLabel('User Type');
        $table->addColumn('oauthAccessToken.user_id')->setLabel('User ID');
        $table->addColumn('oauthAccessToken.scopes')->setLabel('Scopes');
        $table->addColumn('revoked')->setLabel('Revoked')->addTransform('yesNo');
        $table->addColumn('expires_at')->setLabel('Expired')->addTransform('formatDatetime');
        $table->addColumn('oauthAccessToken.created')->setLabel('Created')->addTransform('formatDatetime');
        $table->setAjax(true);

        return $app;
    }
}

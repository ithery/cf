<?php

class CApi_OAuth_Method_Authorization_Login extends CApi_OAuth_MethodAbstract {
    use CApi_OAuth_Trait_ConvertPsrResponseTrait;
    use CApi_OAuth_Trait_RetrieveAuthRequestFromSessionTrait;

    public function __construct() {
        parent::__construct();
    }

    public function execute() {
        $request = $this->apiRequest;
        $oauth = $this->getOAuth();
        $email = $request->email;
        $password = $request->password;
        $state = $request->state;
        $clientId = $request->client_id;
        $authToken = $request->auth_token;
        $redirectUri = $request->redirect_uri;
        $auth = $oauth->createSessionGuard();
        $successLogin = $auth->attempt(['email' => $email, 'password' => $password], false);

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => '',
            'state' => $state,
            'error' => $successLogin ? '' : 'Invalid username or password',
        ]);

        $authorizeUrl = $oauth->routeManager()->getAuthorizeUrl();

        return c::redirect($authorizeUrl . '?' . $query);
    }
}

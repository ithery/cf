<?php
use Nyholm\Psr7\Response as Psr7Response;

class CApi_OAuth_Method_Token_Refresh extends CApi_OAuth_MethodAbstract {
    use CApi_OAuth_Trait_HandleOAuthErrorTrait;
    use CApi_OAuth_Trait_FactoryTrait;

    public function __construct() {
        parent::__construct();
        // $this->middleware(SEApi_Middleware_MethodGetMiddleware::class);
    }

    public function execute() {
        $request = $this->apiRequest;

        $cookieFactory = CApi_OAuth_ApiTokenCookieFactory::instance();

        return c::response()->withCookie($cookieFactory->make(
            $request->user()->getAuthIdentifier(),
            $request->session()->token()
        ));
    }
}

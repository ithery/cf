<?php
use Nyholm\Psr7\Response as Psr7Response;

class CApi_OAuth_Method_Token extends CApi_OAuth_MethodAbstract {
    use CApi_OAuth_Trait_HandleOAuthErrorTrait;
    use CApi_OAuth_Trait_FactoryTrait;

    public function __construct() {
        parent::__construct();
        // $this->middleware(SEApi_Middleware_MethodGetMiddleware::class);
    }

    public function execute() {
        $request = $this->apiRequest;
        $response = $this->withErrorHandling(function () use ($request) {
            return $this->convertResponse(
                $this->oauthServer()->respondToAccessTokenRequest($this->toServerRequestInterface($request), new Psr7Response())
            );
        });

        return $response;
        // $data = $this->reformatContent(json_decode($response->getContent(), true));

        // $this->data = $data;
    }

    protected function reformatContent(array $data) {
        $mapArrayKeys = function (callable $f, array $xs) use (&$mapArrayKeys) {
            $out = [];
            foreach ($xs as $key => $value) {
                $out[$f($key)] = is_array($value) ? $mapArrayKeys($f, $value) : $value;
            }

            return $out;
        };

        return $mapArrayKeys(function ($item) {
            return cstr::camel($item);
        }, $data);
    }
}

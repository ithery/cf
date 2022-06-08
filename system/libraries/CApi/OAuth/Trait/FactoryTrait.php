<?php
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

trait CApi_OAuth_Trait_FactoryTrait {
    public function oauthServer() {
        return CApi::currentDispatcher()->oauth()->authorizationServer();
    }

    /**
     * @param SymfonyRequest $request
     *
     * @return ServerRequestInterface
     */
    public function toServerRequestInterface(SymfonyRequest $request) {
        $psr = (new PsrHttpFactory(
            new Psr17Factory(),
            new Psr17Factory(),
            new Psr17Factory(),
            new Psr17Factory()
        ))->createRequest($request);

        return $psr;
    }
}

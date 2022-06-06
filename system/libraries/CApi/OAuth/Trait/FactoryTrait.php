<?php

trait CApi_OAuth_Trait_FactoryTrait {
    public function oauthServer() {
        return CApi::currentDispatcher()->oauth()->authorizationServer();
    }
}

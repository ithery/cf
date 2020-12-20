
<?php

class CHTTP_Exception_RedirectHttpException extends CHTTP_Exception_HttpException {
    protected $uri;


    public function getUri(){
        return $this->uri;
    }

    public function setUri($uri) {
        $this->uri=$uri;
    }
}

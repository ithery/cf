<?php

class CObservable_Listener_Handler_DownloadProgressHandler extends CObservable_Listener_Handler {
    use CObservable_Listener_Handler_Trait_ParamHandlerTrait;
    use CObservable_Listener_Handler_Trait_AjaxHandlerTrait;

    public function __construct($listener) {
        parent::__construct($listener);
        $this->method = 'get';
        $this->target = '';

        $this->name = 'DownloadProgress';
        $this->url = '';
        $this->urlParam = [];
    }

    public function js() {
        $js = '';
        $dataAddition = $this->populateParamJson();

        $generatedUrl = $this->generatedUrl();
        $jsOptions = '{';
        $jsOptions .= "url:'" . $generatedUrl . "',";
        $jsOptions .= "method:'" . $this->method . "',";
        $jsOptions .= 'dataAddition:' . $dataAddition . ',';

        $jsOptions .= '}';

        $js .= '

            cresenity.downloadProgress(' . $jsOptions . ');

         ';

        return $js;
    }
}

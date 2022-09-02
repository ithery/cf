<?php

class CObservable_Listener_Handler_RedirectHandler extends CObservable_Listener_Handler {
    use CTrait_Element_Property_Url;

    public function js() {
        //parse url to normalize the value
        $url = $this->url;

        preg_match_all("/{(\w*)}/", $url, $matches);
        foreach ($matches[1] as $key => $match) {
            $url = str_replace('{' . $match . '}', "'+ cresenity.value('" . $match . "') +'", $url);
        }
        $js = "window.location.href = '" . $url . "';";

        return $js;
    }
}

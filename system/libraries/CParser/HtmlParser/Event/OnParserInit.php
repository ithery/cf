<?php

class CParser_HtmlParser_Event_OnParserInit {
    public $htmlParser;

    public function __construct(CParser_HtmlParser $htmlParser) {
        $this->htmlParser = $htmlParser;
    }
}

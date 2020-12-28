<?php

interface CParser_HtmlParser_TokenizerCallbackInterface {
    public function onattribdata($value); //TODO implement the new event

    public function onattribend();

    public function onattribname($name);

    public function oncdata($data);

    public function onclosetag($name);

    public function oncomment($data);

    public function ondeclaration($content);

    public function onend();

    public function onerror($error, $state);

    public function onopentagend();

    public function onopentagname($name);

    public function onprocessinginstruction($instruction);

    public function onselfclosingtag();

    public function ontext($value);
}

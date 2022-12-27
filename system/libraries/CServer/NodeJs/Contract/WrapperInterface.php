<?php
interface CServer_NodeJs_Contract_WrapperInterface {
    public function compile($destination = null);

    public function fallback();
}

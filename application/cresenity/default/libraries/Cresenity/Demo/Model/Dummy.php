<?php

namespace Cresenity\Demo\Model;

class Dummy extends \CModel {
    use \CModel_ArrayDriver_ArrayDriverTrait;

    protected function getRows() {
        $json = file_get_contents('https://jsonplaceholder.typicode.com/todos');
        $data = json_decode($json, true);

        return $data;
    }
}

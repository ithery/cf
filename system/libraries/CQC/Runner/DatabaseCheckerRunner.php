<?php

defined('SYSPATH') or die('No direct access allowed.');

class CQC_Runner_DatabaseCheckerRunner extends CQC_RunnerAbstract {
    public function run() {
        $className = $this->className;
        $checker = new $className();

        $errCode = 0;
        $errMessage = '';
        $db = c::db();
        $data = [];

        if ($errCode == 0) {
            $sql = $checker->getSql();
            $message = $checker->getMessage();

            if ($sql == null) {
                $errCode++;
                $errMessage = 'Failed to get data on class:' . $className . ', key sql not found';
            }
        }

        if ($errCode == 0) {
            $result = $db->query($sql);
            if ($result->count() > 0) {
                foreach ($result as $row) {
                    $messageRow = 'data mismatch with data:' . json_encode($row);
                    if ($message != null) {
                        $messageRow = $message;
                        preg_match_all("/{([\w]*)}/", $messageRow, $matches, PREG_SET_ORDER);

                        foreach ($matches as $val) {
                            $str = $val[1]; //matches str without bracket {}
                            $bStr = $val[0]; //matches str with bracket {}
                            $val = isset($row->$str) ? $row->$str : $bStr;

                            $messageRow = str_replace($bStr, $val, $messageRow);
                        }
                    }
                    $data[] = $messageRow;
                }
            }
        }

        if ($errCode > 0) {
            throw new Exception($errMessage);
        }

        return $data;
    }
}

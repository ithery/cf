<?php
use CVendor_LiteSpeed_Node as Node;

class CVendor_LiteSpeed_Parser_RawFiles {
    private $list = [];

    // list of obj (name, level, fid, dir, fullpath)

    private $errs = [];

    private $fatal = 0;

    public function getFullFileName($fid) {
        return $this->list[$fid][4];
    }

    public function addRawFile(Node $node) {
        $filename = $node->get(Node::FLD_VAL);
        $index = count($this->list);
        $parentid = $index - 1;
        $level = ($index > 0) ? $this->list[$parentid][1] + 1 : 0;
        $fullpath = $filename;
        if ($filename[0] != '/') {
            if ($parentid) {
                $fullpath = $this->list[$parentid][3] . '/' . $filename;
            } else {
                $fullpath = CVendor_LiteSpeed::serverRoot() . '/conf/' . $fullpath;
            }
        }
        $dir = dirname($fullpath);

        $this->list[$index] = [$filename, $level, $index, $dir, $fullpath]; // list of obj (name, level, fid, dir, fullpath)

        return $index;
    }

    public function markError($node, $errlevel, $errmsg) {
        $node->setErr($errmsg, $errlevel);
        $this->errs[] = [$errlevel, $errmsg, $node->Get(Node::FLD_FID), $node->Get(Node::FLD_FLFROM), $node->Get(Node::FLD_FLTO)];
        if ($errlevel == Node::E_FATAL) {
            $this->fatal++;
        }
    }

    public function hasFatalErr() {
        return $this->fatal > 0;
    }

    public function hasErr() {
        return count($this->errs) > 0;
    }

    public function getAllErrors() {
        $level = [Node::E_WARN => 'WARN', Node::E_FATAL => 'FATEL'];

        $buf = "\nShow Errors: \n";
        foreach ($this->errs as $e) {
            $errlevel = $level[$e[0]];
            $filename = $this->list[$e[2]]->filename;
            $buf .= "${errlevel} ${filename} line {$e[3]}";
            if ($e[3] != $e[4]) {
                $buf .= " ~ {$e[4]}";
            }
            $buf .= ": {$e[1]}\n";
        }

        return $buf;
    }
}

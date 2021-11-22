<?php
use CVendor_LiteSpeed_Node as Node;

class CVendor_LiteSpeed_Parser_PlainConfParser {
    private $hasInclude;

    private $bypassIncludes = [];

    public function setBypassInclude($bypassPatterns) {
        $this->bypassIncludes = $bypassPatterns;
    }

    public function __construct() {
    }

    /**
     * @param string $filename
     *
     * @return Node
     */
    public function parse($filename) {
        $this->hasInclude = false;
        $root = new Node(Node::K_ROOT, $filename, Node::T_ROOT);
        $rawfiles = new CVendor_LiteSpeed_Parser_RawFiles();

        $this->parseRaw($rawfiles, $root);

        return $root;
    }

    public function hasInclude() {
        return $this->hasInclude;
    }

    private function parseRaw(CVendor_LiteSpeed_Parser_RawFiles $rawfiles, $root) {
        $fid = $rawfiles->addRawFile($root);

        $filename = $root->get(Node::FLD_VAL);
        if (!empty($this->bypassIncludes)) {
            foreach ($this->bypassIncludes as $pattern) {
                if (preg_match($pattern, $filename)) {
                    //error_log("bypass $filename");
                    return;
                }
            }
        }
        $fullpath = $rawfiles->GetFullFileName($fid);

        $rawlines = file($fullpath);

        if ($rawlines == null) {
            $errlevel = ($root->Get(Node::FLD_KEY) == Node::K_ROOT) ? Node::E_FATAL : Node::E_WARN;
            $errmsg = "Failed to read file ${filename}, abspath = ${fullpath}";
            $rawfiles->MarkError($root, $errlevel, $errmsg);

            return;
        }

        $root->SetRawMap($fid, 1, count($rawlines), '');

        $stack = [];
        $cur_node = $root;
        $prev_node = null;
        $cur_val = $cur_comment = '';
        $from_line = $to_line = 0;

        $sticky = false;
        $multiline_tag = '';

        foreach ($rawlines as $line_num => $data) {
            $line_num++;

            if ($sticky || ($multiline_tag != '')) {
                $d = rtrim($data, "\r\n");
            } else {
                $d = trim($data);
                if ($d == '') {
                    $cur_comment .= "\n";

                    continue;  // ignore empty lines
                }

                if ($d[0] == '#') {
                    $cur_comment .= $d . "\n";

                    continue; // comments
                }
                $from_line = $line_num;
            }

            if (strlen($d) > 0) {
                $end_char = $d[strlen($d) - 1];
            } else {
                $end_char = '';
            }

            $cur_val .= $d;

            if ($end_char == '\\') {
                $sticky = true;
                $cur_val .= "\n"; //make the line end with \n\

                continue;
            } else {
                $sticky = false;
            }

            if ($multiline_tag != '') {
                if (trim($d) == $multiline_tag) { // stop
                    $multiline_tag = '';
                } else {
                    $cur_val .= "\n";

                    continue;
                }
            } elseif (($pos = strpos($d, '<<<')) > 0) {
                $multiline_tag = trim(substr($d, $pos + 3));
                $cur_val .= "\n";

                continue;
            }

            $to_line = $line_num;

            if ($d[0] == '}') {
                // end of block
                $cur_node->EndBlock($cur_comment);

                if (strlen($cur_val) > 1) {
                    $rawfiles->MarkError($cur_node, Node::E_WARN, 'No other characters allowed at the end of closing }');
                }

                if (count($stack) > 0) {
                    $prev_node = $cur_node;
                    $prev_node->Set(Node::FLD_FLTO, $line_num);
                    $cur_node = array_pop($stack);
                } else {
                    $rawfiles->MarkError(($prev_node == null) ? $cur_node : $prev_node, Node::E_FATAL, 'Mismatched blocks, may due to extra closing }');
                }
            } else {
                $is_block = false;
                if ($end_char == '{') {
                    $cur_val = rtrim(substr($cur_val, 0, (strlen($cur_val) - 1)));
                    $is_block = true;
                }

                if (preg_match('/^([\S]+)\s/', $cur_val, $m)) {
                    $key = $m[1];
                    $val = trim(substr($cur_val, strlen($m[0])));
                    if (substr($val, 0, 3) == '<<<') {
                        $posv0 = strpos($val, "\n");
                        $posv1 = strrpos($val, "\n");
                        $val = trim(substr($val, $posv0 + 1, $posv1 - $posv0));
                    }
                } else {
                    $key = $cur_val;
                    $val = null;
                }

                if ($cur_node->HasFlag(Node::BM_HAS_RAW)) {
                    // if (TblDef::getInstance()->IsSpecialBlockRawContent($cur_node, $key)) {
                    //     $cur_node->AddRawContent($d, $cur_comment);
                    //     $cur_val = '';

                    //     continue;
                    // }
                }

                $type = Node::T_KV;
                if ($is_block) {
                    $type = ($val == null) ? Node::T_KB : Node::T_KVB;
                } elseif (strcasecmp($key, 'include') == 0) {
                    $type = Node::T_INC;
                }

                $newnode = new Node($key, $val, $type);
                $newnode->SetRawMap($fid, $from_line, $to_line, $cur_comment);
                // validate key
                if (!preg_match('/^([a-zA-Z_0-9:])+$/', $key)) {
                    $rawfiles->MarkError($newnode, Node::E_WARN, "Invalid char in keyword ${key}");
                }

                $cur_node->AddChild($newnode);

                if ($newnode->hasFlag(Node::BM_BLK)) {
                    //CVendor_LiteSpeed_OWS_TblDef::getInstance()->MarkSpecialBlock($newnode);
                    $stack[] = $cur_node;
                    $prev_node = $cur_node;
                    $cur_node = $newnode;
                } elseif ($newnode->hasFlag(Node::BM_INC)) {
                    $this->parseRaw($rawfiles, $newnode);
                    $cur_node->AddIncludeChildren($newnode);
                    $this->hasInclude = true;
                }
            }

            $cur_val = '';
            $cur_comment = '';
        }

        $cur_node->endBlock($cur_comment);

        while (count($stack) > 0) {
            $rawfiles->MarkError($cur_node, Node::E_FATAL, 'Mismatched blocks at end of the file, may due to extra openning { or missing closing }.');

            $prev_node = $cur_node;
            $cur_node = array_pop($stack);
        }
    }

    public function test() {
        ini_set('include_path', '.:ws/');

        date_default_timezone_set('America/New_York');

        spl_autoload_register(function ($class) {
            include $class . '.php';
        });

        $filename = '/home/lsong/proj/temp/t2.conf';
        $this->Parse($filename);

        /*            $confdata = new CData('serv', $filename);
          echo "Test file $filename \n";
          $root = $this->LoadData($confdata);
          //$this->ExportData($root);


          $filemap = DPageDef::GetInstance()->GetFileMap($confdata->_type);   // serv, vh, tp, admin



          $root->PrintBuf($buf1);
          echo "=======buf1====\n$buf1";

          $exproot = $root->DupHolder();
          $filemap->Convert($root, $exproot, 1, 1);

          $exproot->PrintBuf($buf2);
          echo "=======buf2====\n$buf2";

          $newxml = $exproot->DupHolder();
          $filemap->Convert($exproot, $newxml, 1, 0);
          $newxml->PrintXmlBuf($buf3);
          echo "=======buf3====\n$buf3";

          //$exproot->MergeUnknown($root);
          //$exproot->PrintBuf($buf2);
          //echo $buf2;

          return $root; */
    }
}

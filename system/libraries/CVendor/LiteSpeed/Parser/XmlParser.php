<?php
use CVendor_LiteSpeed_Node as Node;

class CVendor_LiteSpeed_Parser_XmlParser {
    private $nodeStack;

    private $curNode;

    private $curVal;

    public function parse($filename) {
        $root = new Node(Node::K_ROOT, $filename, Node::T_ROOT);

        $filename = $root->get(Node::FLD_VAL);
        $xmlstring = file_get_contents($filename);
        if ($xmlstring === false) {
            $root->setErr("failed to read file ${filename}", Node::E_FATAL);

            return $root;
        }

        $parser = xml_parser_create();
        xml_set_object($parser, $this);
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
        xml_set_element_handler($parser, 'startElement', 'endElement');
        xml_set_character_data_handler($parser, 'characterData');

        // Build a Root node and initialize the nodeStack...
        $this->nodeStack = [];
        $this->curNode = $root;

        // parse the data and free the parser...
        $result = xml_parse($parser, $xmlstring);
        if (!$result) {
            $err = 'XML error: ' . xml_error_string(xml_get_error_code($parser))
            . ' at line ' . xml_get_current_line_number($parser);
            $root->SetErr("failed to parse file ${filename}, ${err}", Node::E_FATAL);
        }
        xml_parser_free($parser);

        return $root;
    }

    private function startElement($parser, $name, $attrs) {
        if ($this->curNode != null) {
            $this->nodeStack[] = $this->curNode;
        }

        $this->curNode = new Node($name, '');

        $this->curVal = '';
    }

    private function endElement($parser, $name) {
        $this->curNode->setVal(trim($this->curVal));
        $this->curVal = '';
        $node = $this->curNode;
        $this->curNode = array_pop($this->nodeStack);
        $this->curNode->addChild($node);
    }

    private function characterData($parser, $data) {
        $this->curVal .= $data;
    }

    public function test() {
        // ini_set('include_path', '.:ws/');

        // date_default_timezone_set('America/New_York');

        // spl_autoload_register(function ($class) {
        //     include $class . '.php';
        // });

        // $filename = '/home/lsong/proj/temp/conf/register.xml';
        // //$filename = '/home/lsong/proj/temp/conf/templates/ccl.xml';
        // $confdata = new ConfData('vh', $filename);
        // echo "Test file ${filename} \n";
        // $root = $this->LoadData($confdata);

        // $root = $xmlroot->DupHolder();
        // $tblDef = DTblDef::GetInstance();
        // $filemap = DPageDef::GetInstance()->GetFileMap($confdata->_type);   // serv, vh, tp, admin
        // $filemap->Convert($xmlroot, $root, 0, 1);

        // $root->PrintBuf($buf1);
        // $newxml = $root->DupHolder();
        // $filemap->Convert($root, $newxml, 1, 0);
        // $newxml->PrintXmlBuf($buf1);
        // echo $buf1;

        // return true;
        //$this->ExportData($root);
    }
}

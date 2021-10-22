<?php

class CVendor_LiteSpeed_Node {
    const E_WARN = 1;

    const E_FATAL = 2;

    const K_ROOT = '__root__';

    const K_EXTRACTED = '__extracted__';

    const BM_VAL = 1;

    const BM_BLK = 2;

    const BM_INC = 4;

    const BM_IDX = 8;

    const BM_MULTI = 16;

    const BM_ROOT = 32;

    const BM_HAS_RAW = 64; // block contains raw node

    const BM_RAW = 128; // raw node, no key, just content

    const T_ROOT = 32;

    const T_KV = 1;  //key-value pair

    const T_KB = 2;  // key blk

    const T_KVB = 3; // key-value blk

    const T_INC = 4; // include

    const T_RAW = 128; // raw content, no key parsed

    const KEY_WIDTH = 25;

    const FLD_TYPE = 1;

    const FLD_KEY = 2;

    const FLD_VAL = 3;

    const FLD_ERR = 4;

    const FLD_ERRLEVEL = 5;

    const FLD_PARENT = 6;

    const FLD_FID = 7;

    const FLD_FLFROM = 8;

    const FLD_FLTO = 9;

    const FLD_ELM = 10;

    const FLD_PRINTKEY = 11;

    private $type = self::T_KV;

    private $k;

    private $rawK;

    private $printK;

    private $v = null; //value

    private $rawContent = null; // raw content

    private $rawTag = null;

    private $e = null; //err

    private $errLevel = 0; //  1-Warning, 2-fatal

    /**
     * @var null|CVendor_LiteSpeed_Node
     */
    private $parent = null;

    /**
     * @var CVendor_LiteSpeed_Node_FileLine
     */
    private $fileline;

    private $changed;

    /**
     * @var CVendor_LiteSpeed_Node[]
     */
    private $els;  // elements

    public function __construct($key, $val, $type = self::T_KV) {
        $this->rawK = $key;
        $this->k = CVendor_LiteSpeed_KeywordAlias::normalizedKey($key);
        $this->printK = $key;
        $this->changed = true;
        $this->type = $type;

        if ($this->type != static::T_KV) {
            $this->els = [];
        }
        $this->v = $val;
        if ($val != null && ($this->type & self::BM_VAL == 0)) {
            $this->type |= self::BM_VAL;
        }
    }

    public function addRawContent($line, &$current_comment) {
        if ($current_comment != '') {
            $this->rawContent .= rtrim($current_comment) . "\n";
            $current_comment = '';
        }
        $this->rawContent .= rtrim($line) . "\n";
    }

    public function addRawTag($tag) {
        $this->type |= self::BM_HAS_RAW;
        $this->rawContent = '';
        $this->rawTag = $tag;
    }

    public function getVal() {
        return $this->get(self::FLD_VAL);
    }

    public function getKey() {
        return $this->get(self::FLD_KEY);
    }

    public function get($field) {
        switch ($field) {
            case self::FLD_KEY:
                return $this->k;
            case self::FLD_VAL:
                return $this->v;
            case self::FLD_ERR:
                return $this->e;
            case self::FLD_PARENT:
                return $this->parent;
            case self::FLD_FID:
                return ($this->fileline == null) ? '' : $this->fileline->fid;
            case self::FLD_FLFROM:
                return ($this->fileline == null) ? '' : $this->fileline->fline0;
            case self::FLD_FLTO:
                return ($this->fileline == null) ? '' : $this->fileline->fline1;
        }
        die("field ${field} not supported");
    }

    public function set($field, $fieldval) {
        switch ($field) {
            case self::FLD_FLTO:
                $this->fileline->fline1 = $fieldval;

                break;
            case self::FLD_PRINTKEY:
                $this->printK = $fieldval;

                break;
            case self::FLD_TYPE:
                $this->type = $fieldval;

                break;
            case self::FLD_KEY:
                $this->rawK = $fieldval;
                $this->k = CVendor_LiteSpeed_KeywordAlias::normalizedKey($fieldval);
                $this->printK = $fieldval;

                break;

            default:
                die("field ${field} not supported");
        }
    }

    public function setVal($v) {
        $this->v = $v;
        if ($v != null && ($this->type & self::BM_VAL == 0)) {
            $this->type |= self::BM_VAL;
        }
    }

    public function addFlag($flag) {
        $this->type |= $flag;
    }

    public function hasFlag($flag) {
        return ($this->type & $flag) != 0;
    }

    public function hasVal() {
        // 0 is valid
        return $this->v !== null && $this->v !== '';
    }

    public function setErr($errmsg, $level = 1) {
        if ($errmsg != '') {
            $this->e .= $errmsg;
            if ($this->errLevel < $level) {
                $this->errLevel = $level;
            }
        }
    }

    public function hasErr() {
        return $this->e != null;
    }

    public function hasFatalErr() {
        return $this->errLevel == self::E_FATAL;
    }

    public function hasChanged() {
        return $this->changed;
    }

    public function dupHolder() {
        // create an empty blk node
        $holder = new static($this->k, $this->v, $this->type);
        if ($this->errLevel > 0) {
            $holder->e = $this->e;
            $holder->errLevel = $this->errLevel;
        }
        $holder->changed = $this->changed;
        $holder->fileline = $this->fileline;

        return $holder;
    }

    public function mergeUnknown($node) {
        if ($this->type != self::T_ROOT && !($this->type & self::BM_BLK)) {
            echo "Err, should merge at parent level {$node->k} {$node->v} \n";

            return;
        }

        foreach ($node->els as $k => $el) {
            if (isset($this->els[$k])) {
                if (is_a($el, 'CNode')) {
                    echo " k = ${k} \n";
                    $this->els[$k]->mergeUnknown($el);
                } else {
                    foreach ($el as $id => $elm) {
                        if (isset($this->els[$k][$id])) {
                            $this->els[$k][$id]->MergeUnknown($elm);
                        } else {
                            $this->AddChild($elm);
                        }
                    }
                }
            } else {
                if (is_a($el, 'CNode')) {
                    $this->AddChild($el);
                } else {
                    $this->els[$k] = $el;
                } // move array over
            }
        }
    }

    public function setRawMap($file_id, $from_line, $to_line, $comment) {
        $this->fileline = new CVendor_LiteSpeed_Node_FileLine($file_id, $from_line, $to_line, $comment);
        $this->changed = false;
    }

    public function endBlock(&$cur_comment) {
        if ($this->rawTag != '') {
            $this->rawContent .= $cur_comment;
            if ($this->rawContent != '') {
                $child = new static($this->rawTag, $this->rawContent, self::T_KV | self::T_RAW);
                $this->AddChild($child);
            } else {
                // backward compatible, mark existing node to raw
                $child = $this->GetChildren($this->rawTag);
                if ($child != null && $child instanceof CVendor_LiteSpeed_Node) {
                    $child->type |= self::T_RAW;
                }
            }
        } else {
            $this->fileline->AddEndComment($cur_comment);
        }
        $cur_comment = '';
    }

    public function addChild($child) {
        if ($this->type == self::T_KV) {
            $this->type = ($this->v == '') ? self::T_KB : self::T_KVB;
        }
        $child->parent = $this;
        $k = $child->k;
        if (isset($this->els[$k])) {
            if (!is_array($this->els[$k])) {
                $first_node = $this->els[$k];
                $this->els[$k] = [];
                if ($first_node->v == null) {
                    $this->els[$k][] = $first_node;
                } else {
                    $this->els[$k][$first_node->v] = $first_node;
                }
            }
            if ($child->v == null) {
                $this->els[$k][] = $child;
            } else {
                $this->els[$k][$child->v] = $child;
            }
        } else {
            $this->els[$k] = $child;
        }
    }

    public function addIncludeChildren($incroot) {
        if (is_array($incroot->els)) {
            foreach ($incroot->els as $elm) {
                if (is_array($elm)) {
                    foreach ($elm as $elSingle) {
                        $elSingle->AddFlag(self::BM_INC);
                        $this->AddChild($elSingle);
                    }
                } else {
                    $elm->AddFlag(self::BM_INC);
                    $this->AddChild($elm);
                }
            }
        }
    }

    public function removeChild($key) {
        // todo: key contains :
        $key = strtolower($key);
        unset($this->els[$key]);
    }

    public function removeFromParent() {
        if ($this->parent != null) {
            if (is_array($this->parent->els[$this->k])) {
                foreach ($this->parent->els[$this->k] as $key => $el) {
                    if ($el == $this) {
                        unset($this->parent->els[$this->k][$key]);

                        return;
                    }
                }
            } else {
                unset($this->parent->els[$this->k]);
            }
            $this->parent = null;
        }
    }

    public function hasDirectChildren($key = '') {
        if ($key == '') {
            return $this->els != null && count($this->els) > 0;
        } else {
            return $this->els != null && isset($this->els[strtolower($key)]);
        }
    }

    public function getChildren($key) {
        $key = strtolower($key);
        if (($pos = strpos($key, ':')) > 0) {
            $node = $this;
            $keys = explode(':', $key);
            foreach ($keys as $k) {
                if (isset($node->els[$k])) {
                    $node = $node->els[$k];
                } else {
                    return null;
                }
            }

            return $node;
        } elseif (isset($this->els[$key])) {
            return $this->els[$key];
        } else {
            // can be array
            return null;
        }
    }

    public function getChildVal($key) {
        $child = $this->getChildren($key);

        return ($child == null || !($child instanceof CVendor_LiteSpeed_Node)) ? null : $child->v;
    }

    public function setChildVal($key, $val) {
        $child = $this->getChildren($key);
        if ($child == null && !($child instanceof CVendor_LiteSpeed_Node)) {
            return false;
        }
        $child->setVal($val);

        return true;
    }

    public function setChildErr($key, $err) {
        $child = $this->getChildren($key);
        if ($child == null && !($child instanceof CVendor_LiteSpeed_Node)) {
            return false;
        }
        if ($err == null) { // clear err
            $child->setErr(null, 0);
        } else {
            $child->setErr($err);
        }

        return true;
    }

    public function getChildNodeById($key, $id) {
        $layer = $this->getChildren($key);
        if ($layer != null) {
            if (is_array($layer)) {
                return isset($layer[$id]) ? $layer[$id] : null;
            } elseif ($layer->v == $id) {
                return $layer;
            }
        }

        return null;
    }

    private function getLastLayer($location) {
        $layers = explode(':', $location);
        $lastlayer = array_pop($layers);
        if ($lastlayer != null) {
            $lastlayer = ltrim($lastlayer, '*');
            if (($varpos = strpos($lastlayer, '$')) > 0) {
                $lastlayer = substr($lastlayer, 0, $varpos);
            }
        }

        return $lastlayer;
    }

    public function getChildrenByLoc(&$location, &$ref) {
        $node = $this;
        if ($location == '') {
            return $node;
        }

        $layers = explode(':', $location);
        foreach ($layers as $layer) {
            $ismulti = false;
            if ($layer[0] == '*') {
                $layer = ltrim($layer, '*');
                $ismulti = true;
            }
            if (($varpos = strpos($layer, '$')) > 0) {
                $layer = substr($layer, 0, $varpos);
            }
            $location = substr($location, strpos($location, $layer));
            $layer = strtolower($layer);
            if (!isset($node->els[$layer])) {
                if ($ismulti && ($ref == '~')) { // for new child, return parent
                    return $node;
                } else {
                    return null;
                }
            }

            $nodelist = $node->els[$layer];
            if ($ismulti) {
                if ($ref == '') {
                    return $nodelist;
                }

                if ($ref == '~') { // for new child, return parent
                    return $node;
                }

                if (($pos = strpos($ref, '`')) > 0) {
                    $curref = substr($ref, 0, $pos);
                    $ref = substr($ref, $pos + 1);
                } else {
                    $curref = $ref;
                    $ref = '';
                }

                if (is_array($nodelist)) {
                    if (!isset($nodelist[$curref])) {
                        return null;
                    }
                    $node = $nodelist[$curref];
                } else {
                    $node = $nodelist;
                    if ($node->v != $curref) {
                        return null;
                    }
                }
            } else {
                $node = $nodelist;
            }
        }

        return $node;
    }

    public function getChildNode(&$location, &$ref) {
        $node = $this->GetChildrenByLoc($location, $ref);

        return ($node instanceof CVendor_LiteSpeed_Node) ? $node : null;
    }

    public function updateChildren($loc, $curref, $extractData) {
        $location = $loc;
        $ref = $curref;
        $child = $this->getChildNode($location, $ref);
        if ($child == null) {
            // need original loc
            $child = $this->allocateLayerNode($loc);
        } elseif (!is_a($child, 'CNode')) {
            die("child is not cnode \n");
        }

        if ($ref == '~') {
            // new node, ref & location has been modified by GetChildNode
            // right now, only last one
            $lastlayer = $this->getLastLayer($loc);
            $extractData->set(static::FLD_KEY, $lastlayer);
            $child->addChild($extractData);
        } else {
            foreach ($extractData->els as $key => $exchild) {
                if (isset($child->els[$key])) {
                    unset($child->els[$key]);
                }
                if (is_array($exchild)) {
                    foreach ($exchild as $exchildSingle) {
                        $child->addChild($exchildSingle);
                    }
                } else {
                    $child->addChild($exchild);
                }
            }
            if ($extractData->v != null && $extractData->v !== $child->v) {
                $child->updateHolderVal($extractData->v);
            }
        }
    }

    private function updateHolderVal($val) {
        if ($this->parent != null) {
            $oldval = $this->v;
            $this->v = $val;
            if (is_array($this->parent->els[$this->k])) {
                unset($this->parent->els[$this->k][$oldval]);
                $this->parent->els[$this->k][$val] = $this;
            }
        }
    }

    public function debugStr() {
        $buf = '';
        $this->debugOut($buf);

        return $buf;
    }

    public function debugOut(&$buf, $level = 0) {
        $indent = str_pad('', $level * 2);
        $buf .= "key={$this->k} val= {$this->v} type={$this->type} ";
        if ($this->els != null) {
            $buf .= " {\n";
            $level++;
            foreach ($this->els as $k => $el) {
                $buf .= "${indent}   [${k}] => ";
                if (is_array($el)) {
                    $buf .= "\n";
                    $level++;
                    foreach ($el as $k0 => $child) {
                        $buf .= "${indent}      [${k0}] => ";
                        if (!($child instanceof CVendor_LiteSpeed_Node)) {
                            $buf .= 'not cnode ';
                        }
                        $child->debugOut($buf, $level);
                    }
                } else {
                    $el->debugOut($buf, $level);
                }
            }
            $buf .= "${indent} }\n";
        } else {
            $buf .= "\n";
        }
    }

    public function printBuf(&$buf, $level = 0) {
        $note0 = ($this->fileline == null) ? '' : $this->fileline->note0;
        $note1 = ($this->fileline == null) ? '' : $this->fileline->note1;
        $key = $this->printK;
        $alias_note = '';
        if (($key1 = CVendor_LiteSpeed_KeywordAlias::shortPrintKey($this->k)) != null) {
            $key = $key1;
            if ($note0 == '' && $key != $this->rawK) {
                $alias_note = "# ${key} is shortened alias of {$this->printK} \n";
            }
        }

        if ($note0 != '') {
            $buf .= str_replace("\n\n", "\n", $note0);
        }

        if ($this->errLevel > 0) {
            $buf .= "#__ERR__({$this->errLevel}): {$this->e} \n";
        }

        if ($this->type & self::BM_IDX) {
            return;
        } // do not print index node

        if (($this->type != self::T_INC) && ($this->type & self::BM_INC)) {
            return;
        } // do not print including nodes

        if ($this->type & self::BM_RAW) {
            $buf .= rtrim($this->v) . "\n";

            return;
        }
        $indent = str_pad('', $level * 2);
        $val = $this->v;
        if ($val != '' && strpos($val, "\n")) {
            $val = "<<<END_{$key}\n${val}\n{$indent}END_{$key}\n";
        }

        if (($this->type & self::BM_VAL) && !($this->type & self::BM_BLK)) {
            // do not print empty value
            if ($val != '' || $note0 != '' || $this->errLevel > 0) {
                $width = self::KEY_WIDTH - $level * 2;
                $buf .= $alias_note;
                $buf .= $indent . str_pad($key, $width) . " ${val}\n";
            }
        } else {
            $begin = '';
            $end = '';
            $buf1 = '';
            $buf2 = '';

            if ($this->type & self::BM_BLK) {
                if ($note0 == '') {
                    $buf1 .= "\n";
                }
                $buf1 .= $alias_note;
                $buf1 .= "{$indent}$key ${val}";
                $begin = " {\n";
                $end = "${indent}}\n";
                $level++;
            } elseif ($this->type == self::T_INC) {
                $buf1 .= "{$indent}$key ${val}\n";
                $buf1 .= "\n##__INC__\n";
                $end = "\n##__ENDINC__\n";
            }

            foreach ($this->els as $el) {
                if (is_array($el)) {
                    foreach ($el as $child) {
                        $child->printBuf($buf2, $level);
                    }
                } else {
                    $el->printBuf($buf2, $level);
                }
            }

            if ($note1 != '') {
                $buf2 .= str_replace("\n\n", "\n", $note1);
            }

            // do not print empty block

            if ($val != '' || $buf2 != '') {
                if ($buf2 == '') {
                    $buf .= $buf1 . "\n";
                } else {
                    $buf .= $buf1 . $begin . $buf2 . $end;
                }
            }
        }
    }

    public function printXmlBuf(&$buf, $level = 0) {
        if ($this->type == self::T_ROOT) {
            $buf .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        }
        $indent = str_pad('', $level * 2);

        $key = $this->printK;
        $value = htmlspecialchars($this->v);

        if (($this->type & self::BM_VAL) && !($this->type & self::BM_BLK)) {
            if ($value !== '') {
                $buf .= "${indent}<${key}>${value}</${key}>\n";
            }
        } else {
            $buf1 = '';
            $buf2 = '';

            if ($this->type & self::BM_BLK) {
                $buf1 .= "${indent}<${key}>\n";
                $level++;
            }
            foreach ($this->els as $el) {
                if (is_array($el)) {
                    foreach ($el as $child) {
                        $child->printXmlBuf($buf2, $level);
                    }
                } else {
                    $el->printXmlBuf($buf2, $level);
                }
            }

            // do not print empty block
            if ($buf2 != '') {
                $buf .= $buf1 . $buf2;
                if ($this->type & self::BM_BLK) {
                    $buf .= "${indent}</${key}>\n";
                }
            }
        }
    }

    public function locateLayer($location) {
        $node = $this;
        if ($location != '') {
            $layers = explode(':', $location);
            foreach ($layers as $layer) {
                $holder_index = '';
                if ($layer[0] == '*') {
                    $layer = ltrim($layer, '*');
                }
                $varpos = strpos($layer, '$');
                if ($varpos > 0) {
                    $holder_index = substr($layer, $varpos + 1);
                    $layer = substr($layer, 0, $varpos);
                }
                $children = $node->getChildren($layer);
                if ($children == null) {
                    return null;
                }

                $node = $children;
            }
        }

        return $node;
    }

    public function allocateLayerNode($location) {
        $node = $this;
        if ($location != '') {
            $layers = explode(':', $location);
            foreach ($layers as $layer) {
                if ($layer[0] == '*') {
                    // contains multiple items, return parent node
                    return $node;
                }

                $varpos = strpos($layer, '$');
                if ($varpos > 0) {
                    $key = substr($layer, 0, $varpos);
                    $type = static::T_KVB;
                } else {
                    $key = $layer;
                    $type = static::T_KB;
                }
                $children = $node->getChildren($key);
                if ($children == null) {
                    $children = new static($key, null, $type);
                    $node->addChild($children);
                }
                $node = $children;
            }
        }

        return $node;
    }
}

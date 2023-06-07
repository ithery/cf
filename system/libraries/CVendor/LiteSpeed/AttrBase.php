<?php
use CVendor_LiteSpeed_Msg as Msg;
use CVendor_LiteSpeed_Node as Node;
use CVendor_LiteSpeed_UIBase as UIBase;

/**
 * Type: parse  _minVal = pattern, _maxVal = pattern tips.
 */
define('ATTR_VAL_NOT_SET', Msg::UIStr('o_notset'));
define('ATTR_VAL_BOOL_YES', Msg::UIStr('o_yes'));
define('ATTR_VAL_BOOL_NO', Msg::UIStr('o_no'));
define('ATTR_NOTE_NUM_RANGE', Msg::UIStr('note_numvalidrange'));
define('ATTR_NOTE_NUMBER', Msg::UIStr('note_number'));

class CVendor_LiteSpeed_AttrBase {
    const BM_NOTNULL = 1;

    const BM_NOEDIT = 2;

    const BM_HIDE = 4;

    const BM_NOFILE = 8;

    const BM_RAWDATA = 16;

    public $helpKey;

    public $type;

    public $minVal;

    public $maxVal;

    public $label;

    public $href;

    public $hrefLink;

    public $multiInd;

    public $note;

    public $icon;

    protected $key;

    protected $keyalias;

    protected $inputType;

    protected $inputAttr;

    protected $glue;

    protected $bitFlag = 0;

    public function __construct($key, $type, $label, $inputType = null, $allowNull = true, $min = null, $max = null, $inputAttr = null, $multiInd = 0, $helpKey = null) {
        $this->key = $key;
        $this->type = $type;
        $this->label = $label;
        $this->minVal = $min;
        $this->maxVal = $max;
        $this->inputType = $inputType;
        $this->inputAttr = $inputAttr;
        $this->multiInd = $multiInd;
        $this->helpKey = ($helpKey == null) ? $key : $helpKey;

        $this->bitFlag = $allowNull ? 0 : self::BM_NOTNULL;
    }

    public function setGlue($glue) {
        $this->glue = $glue;
    }

    public function setFlag($flag) {
        $this->bitFlag |= $flag;
    }

    public function isFlagOn($flag) {
        return ($this->bitFlag & $flag) == $flag;
    }

    public function getKey() {
        return $this->key;
    }

    public function dup($key, $label, $helpkey) {
        $cname = get_class($this);
        $d = new $cname($this->key, $this->type, $this->label, $this->inputType, true, $this->minVal, $this->maxVal, $this->inputAttr, $this->multiInd, $this->helpKey);

        $d->glue = $this->glue;
        $d->href = $this->href;
        $d->hrefLink = $this->hrefLink;
        $d->bitFlag = $this->bitFlag;
        $d->note = $this->note;
        $d->icon = $this->icon;

        if ($key != null) {
            $d->key = $key;
        }
        if ($label != null) {
            $d->label = $label;
        }

        if ($helpkey != null) {
            $d->helpKey = $helpkey;
        }

        return $d;
    }

    protected function extractCheckBoxOr() {
        $value = 0;
        $novalue = 1;
        foreach ($this->maxVal as $val => $disp) {
            $name = $this->key . $val;
            if (isset($_POST[$name])) {
                $novalue = 0;
                $value = $value | $val;
            }
        }

        return  $novalue ? '' : (string) $value;
    }

    protected function extractSplitMultiple(&$value) {
        if ($this->glue == ' ') {
            $vals = preg_split('/[,; ]+/', $value, -1, PREG_SPLIT_NO_EMPTY);
        } else {
            $vals = preg_split('/[,;]+/', $value, -1, PREG_SPLIT_NO_EMPTY);
        }

        $vals1 = [];
        foreach ($vals as $val) {
            $val1 = trim($val);
            if (strlen($val1) > 0 && !in_array($val1, $vals1)) {
                $vals1[] = $val1;
            }
        }

        if ($this->glue == ' ') {
            $value = implode(' ', $vals1);
        } else {
            $value = implode(', ', $vals1);
        }
    }

    protected function toHtmlContent($node = null, $refUrl = null) {
        /** @var Node $node */
        if ($node == null || !$node->hasVal()) {
            return '<span class="text-muted">' . ATTR_VAL_NOT_SET . '</span>';
        }

        $o = '';
        $value = $node->get(Node::FLD_VAL);
        $err = $node->get(Node::FLD_ERR);

        if ($this->type == 'sel1' && $value != null && !array_key_exists($value, $this->maxVal)) {
            $err = 'Invalid value - ' . htmlspecialchars($value, ENT_QUOTES);
        } elseif ($err != null) {
            $type3 = substr($this->type, 0, 3);
            if ($type3 == 'fil' || $type3 == 'pat') {
                $validator = new ConfValidation();
                $validator->chkAttr_file_val($this, $value, $err);
            }
        }

        if ($err) {
            $node->SetErr($err);
            $o .= '<span class="field_error">*' . $err . '</span><br>';
        }

        if ($this->href) {
            $link = $this->hrefLink;
            if (strpos($link, '$V')) {
                $link = str_replace('$V', urlencode($value), $link);
            }
            $o .= '<span class="field_url"><a href="' . $link . '">';
        } elseif ($refUrl != null) {
            $o .= '<span class="field_refUrl"><a href="' . $refUrl . '">';
        }

        if ($this->type === 'bool') {
            if ($value === '1') {
                $o .= ATTR_VAL_BOOL_YES;
            } elseif ($value === '0') {
                $o .= ATTR_VAL_BOOL_NO;
            } else {
                $o .= '<span class="text-muted">' . ATTR_VAL_NOT_SET . '</span>';
            }
        } elseif ($this->type == 'ctxseq') {
            $o = $value;
            if (!defined('_CONF_READONLY_')) {
                $o .= ' <a href="javascript:lst_ctxseq(' . $value
                    . ')" class="btn bg-color-blueLight btn-xs txt-color-white"><i class="fa fa-plus"></i></a> <a href="javascript:lst_ctxseq(-' . $value
                    . ')" class="btn bg-color-blueLight btn-xs txt-color-white"><i class="fa fa-minus"></i></a>';
            }
        } elseif ($this->key == 'note') {
            $o .= '<textarea readonly style="width:100%;height:auto">' . htmlspecialchars($value, ENT_QUOTES) . '</textarea>';
        } elseif ($this->type === 'sel' || $this->type === 'sel1') {
            if ($this->maxVal != null && array_key_exists($value, $this->maxVal)) {
                $o .= $this->maxVal[$value];
            } else {
                $o .= htmlspecialchars($value, ENT_QUOTES);
            }
        } elseif ($this->type === 'checkboxOr') {
            if ($this->minVal !== null && ($value === '' || $value === null)) {
                // has default value, for "Not set", set default val
                $value = $this->minVal;
            }
            foreach ($this->maxVal as $val => $name) {
                if (($value & $val) || ($value === $val) || ($value === '0' && $val === 0)) {
                    $o .= '<i class="fa fa-check-square-o">';
                } else {
                    $o .= '<i class="fa fa-square-o">';
                }
                $o .= '</i> ';
                $o .= $name . '&nbsp;&nbsp;&nbsp;&nbsp;';
            }
        } elseif ($this->inputType === 'textarea1') {
            $o .= '<textarea readonly style="width:100%;"' . $this->inputAttr . '>' . htmlspecialchars($value, ENT_QUOTES) . '</textarea>';
        } elseif ($this->inputType === 'text') {
            $o .= '<span class="field_text">' . htmlspecialchars($value, ENT_QUOTES) . '</span>';
        } else {
            $o .= htmlspecialchars($value);
        }

        if ($this->href || $refUrl != null) {
            $o .= '</a></span>';
        }

        return $o;
    }

    protected function getNote() {
        if ($this->note != null) {
            return $this->note;
        }
        if ($this->type == 'uint') {
            if ($this->maxVal) {
                return ATTR_NOTE_NUM_RANGE . ': ' . $this->minVal . ' - ' . $this->maxVal;
            } elseif ($this->minVal !== null) {
                return ATTR_NOTE_NUM_RANGE . ' >= ' . $this->minVal;
            }
        }

        return null;
    }

    public function extractPost($parent) {
        if ($this->type == 'checkboxOr') {
            $value = $this->extractCheckBoxOr();
        } else {
            $value = UIBase::grabInput('post', $this->key);
            if (get_magic_quotes_gpc()) {
                $value = stripslashes($value);
            }
        }
        $value = str_replace("\r\n", "\n", $value);

        $key = $this->key;
        $node = $parent;
        while (($pos = strpos($key, ':')) > 0) {
            $key0 = substr($key, 0, $pos);
            $key = substr($key, $pos + 1);
            if ($node->HasDirectChildren($key0)) {
                $node = $node->GetChildren($key0);
            } else {
                $child = new Node($key0, '', Node::T_KB);
                $node->AddChild($child);
                $node = $child;
            }
        }

        if ($this->multiInd == 2 && $value != null) {
            $v = preg_split("/\n+/", $value, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($v as $vi) {
                $node->AddChild(new Node($key, trim($vi)));
            }
        } elseif ($this->type == 'checkboxOr') {
            $node->AddChild(new Node($key, $value));
        } else {
            if ($this->multiInd == 1 && $value != null) {
                $this->extractSplitMultiple($value);
            }
            $node->AddChild(new Node($key, $value));
        }

        return true;
    }

    public function toHtml($pnode, $refUrl = null) {
        $node = ($pnode == null) ? null : $pnode->GetChildren($this->key);
        $o = '';
        if (is_array($node)) {
            foreach ($node as $nd) {
                $o .= $this->toHtmlContent($nd, $refUrl);
                $o .= '<br>';
            }
        } else {
            $o .= $this->toHtmlContent($node, $refUrl);
        }

        return $o;
    }

    public function toInputGroup($pnode, $is_blocked, $helppop) {
        $node = ($pnode == null) ? null : $pnode->GetChildren($this->key);
        $err = '';
        $value = '';

        if (is_array($node)) {
            $value = [];
            foreach ($node as $d) {
                $value[] = $d->get(Node::FLD_VAL);
                $e1 = $d->get(Node::FLD_ERR);
                if ($e1 != null) {
                    $err .= $e1 . '<br>';
                }
            }
        } else {
            if ($node != null) {
                $value = $node->get(Node::FLD_VAL);
                $err = $node->get(Node::FLD_ERR);
            } else {
                $value = null;
            }
        }

        $buf = '<div class="form-group' . ($err ? ' has-error">' : '">');
        if ($this->label) {
            $buf .= '<label class="col-md-3 control-label">';
            $buf .= $this->label;
            if ($this->isFlagOn(self::BM_NOTNULL)) {
                $buf .= ' *';
            }

            $buf .= "</label>\n";
            $buf .= '<div class="col-md-9">';
        } else {
            $buf .= '<div class="col-md-12">';
        }

        $buf .= $this->toHtmlInput($helppop, $is_blocked, $err, $value);

        $buf .= "</div></div>\n";

        return $buf;
    }

    protected function toHtmlInput($helppop, $isDisabled, $err, $value) {
        $spacer = '&nbsp;&nbsp;&nbsp;&nbsp;';
        $checked = ' checked="checked"';

        $input = '<div class="input-group">';
        $input .= '<span class="input-group-addon">' . $helppop . '</span>' . "\n"; // need this even empty, for alignment

        if (is_array($value) && $this->inputType != 'checkbox') {
            if ($this->multiInd == 1) {
                $glue = ', ';
            } else {
                $glue = "\n";
            }
            $value = implode($glue, $value);
        }
        $name = $this->key;

        $inputAttr = $this->inputAttr;
        if ($isDisabled) {
            $inputAttr .= ' disabled="disabled"';
        }

        $style = 'form-control';
        if ($this->inputType == 'text') {
            $input .= '<input class="' . $style . '" type="text" name="' . $name . '" ' . $inputAttr . ' value="' . htmlspecialchars($value, ENT_QUOTES) . '">';
        } elseif ($this->inputType == 'password') {
            $input .= '<input class="' . $style . '" type="password" name="' . $name . '" ' . $inputAttr . ' value="' . $value . '">';
        } elseif ($this->inputType == 'textarea' || $this->inputType == 'textarea1') {
            $input .= '<textarea name="' . $name . '" class="' . $style . '" ' . $inputAttr . '>' . htmlspecialchars($value, ENT_QUOTES) . '</textarea>';
        } elseif ($this->inputType == 'radio' && $this->type == 'bool') {
            $input .= '<div class="form-control"><div class="lst-radio-group"><label class="radio radio-inline">
					<input type="radio" name="' . $name . '" ' . $inputAttr . ' value="1"';
            if ($value == '1') {
                $input .= $checked;
            }
            $input .= '> ' . ATTR_VAL_BOOL_YES . '</label><label class="radio radio-inline">'
                    . '<input type="radio" name="' . $name . '" ' . $inputAttr . ' value="0"';
            if ($value == '0') {
                $input .= $checked;
            }
            $input .= '> ' . ATTR_VAL_BOOL_NO . '</label>';
            if (!$this->isFlagOn(self::BM_NOTNULL)) {
                $input .= '<label class="radio radio-inline">
					<input type="radio" name="' . $name . '" ' . $inputAttr . ' value=""';
                if ($value != '0' && $value != '1') {
                    $input .= $checked;
                }
                $input .= '> ' . ATTR_VAL_NOT_SET . '</label>';
            }
            $input .= '</div></div>';
        } elseif ($this->inputType == 'checkboxgroup') {
            $input .= '<div class="form-control">';
            if ($this->minVal !== null && ($value === '' || $value === null)) {
                // has default value, for "Not set", set default val
                $value = $this->minVal;
            }
            $js0 = $js1 = '';
            if (array_key_exists('0', $this->maxVal)) {
                $chval = array_keys($this->maxVal);
                foreach ($chval as $chv) {
                    if ($chv == '0') {
                        $js1 = "document.confform.${name}${chv}.checked=false;";
                    } else {
                        $js0 .= "document.confform.${name}${chv}.checked=false;";
                    }
                }
                $js1 = " onclick=\"${js1}\"";
                $js0 = " onclick=\"${js0}\"";
            }
            foreach ($this->maxVal as $val => $disp) {
                $id = $name . $val;
                $input .= "<input type=\"checkbox\" id=\"{$id}\" name=\"{$id}\" value=\"{$val}\"";
                if (($value & $val) || ($value === $val) || ($value === '0' && $val === 0)) {
                    $input .= $checked;
                }
                $input .= ($val == '0') ? $js0 : $js1;
                $input .= "> <label for=\"{$id}\"> ${disp} </label> ${spacer}";
            }
            $input .= '</div>';
        } elseif ($this->inputType == 'select') {
            $input .= '<select class="form-control" name="' . $name . '" ' . $inputAttr . '>';
            $input .= UIBase::genOptions($this->maxVal, $value);
            $input .= '</select>';
        }

        $input .= "</div>\n";
        if ($err != '') {
            $input .= '<span class="help-block"><i class="fa fa-warning"></i> ';
            $type3 = substr($this->type, 0, 3);
            $input .= ($type3 == 'fil' || $type3 == 'pat') ? $err : htmlspecialchars($err, ENT_QUOTES);
            $input .= '</span>';
        }

        $note = $this->getNote();
        if ($note) {
            $input .= '<p class="note">' . htmlspecialchars($note, ENT_QUOTES) . '</p>';
        }

        return $input;
    }

    public function setDerivedSelOptions($derived) {
        $options = [];
        if ($this->isFlagOn(self::BM_NOTNULL)) {
            $options['forcesel'] = '-- ' . Msg::UIStr('note_select_option') . ' --';
        } else {
            $options[''] = '';
        }
        if ($derived) {
            $options = array_merge($options, $derived);
        }
        $this->maxVal = $options;
    }
}

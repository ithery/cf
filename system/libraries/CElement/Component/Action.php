<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 12, 2018, 9:08:07 PM
 */
class CElement_Component_Action extends CElement_Component {
    use CTrait_Compat_Element_Action,
        CTrait_Element_Property_Label,
        CTrait_Element_Property_Icon;

    protected $jsfunc;

    /**
     * @var bool
     */
    protected $disabled;

    protected $type;

    protected $linkTarget;

    protected $link;

    protected $orig_label;

    protected $submit;

    protected $submitTo;

    protected $submitToTarget;

    protected $submitValue;

    protected $jsparam;

    /**
     * @var bool
     */
    protected $confirm;

    protected $style;

    /**
     * @var string
     */
    protected $confirmMessage;

    protected $button;

    protected $btn_style;

    protected $value;

    /**
     * @var bool
     */
    protected $isActive = false;

    /**
     * @var string
     */
    protected $name;

    public function __construct($id) {
        parent::__construct($id);
        $this->name = $this->id;
        $this->tag = 'a';
        $this->jsfunc = '';
        $this->type = 'jsfunc';
        $this->icon = '';
        $this->link = '';
        $this->jsparam = [];
        $this->linkTarget = '';
        $this->submit = false;
        $this->submitTo = false;
        $this->submitToTarget = false;
        $this->submitValue = null;
        $this->label = '';
        $this->style = '';
        $this->disabled = false;
        $this->confirm = false;
        $this->confirmMessage = '';
        $this->button = false;
        $this->btn_style = 'default';
        $this->value = '';
        $this->isActive = false;
    }

    public static function factory($id = null) {
        // @phpstan-ignore-next-line
        return new static($id);
    }

    /**
     * @param bool|string $bool
     *
     * @return $this
     */
    public function setConfirm($bool = true) {
        if (is_string($bool)) {
            $this->setConfirmMessage($bool);
            $this->confirm = true;

            return $this;
        }
        $this->confirm = $bool;

        return $this;
    }

    /**
     * @param mixed $message
     *
     * @return $this
     */
    public function setConfirmMessage($message) {
        $this->confirmMessage = $message;

        return $this;
    }

    public function setJsParam($jsparam) {
        $this->jsparam = $jsparam;

        return $this;
    }

    public function setLink($link) {
        $this->type = 'link';
        $this->link = $link;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setActive($bool = true) {
        $this->isActive = true;

        return $this;
    }

    public function setLinkTarget($linkTarget) {
        $this->linkTarget = $linkTarget;

        return $this;
    }

    public function setSubmit($bool = true) {
        $this->submit = $bool;

        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setSubmitValue($value) {
        $this->submitValue = $value;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $url
     * @param string $target
     *
     * @return $this
     */
    public function setSubmitTo($url, $target = '') {
        $this->submitTo = $url;

        if (strlen($target) > 0) {
            $this->submitToTarget = $target;
        }

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setDisabled($bool = true) {
        $this->disabled = $bool;

        return $this;
    }

    protected function renderAsInput() {
        $renderAsInput = false;
        if ($this->submit) {
            $renderAsInput = true;
        }

        return $renderAsInput;
    }

    public function reassignConfirm() {
        if ($this->confirm) {
            //we check the listener
            if (count($this->listeners) > 0) {
                foreach ($this->listeners as $lis) {
                    $lis->setConfirm(true)->setConfirmMessage($this->confirmMessage);
                }
                $this->setConfirm(false);
            }
        }
    }

    public function toArray() {
        $data = [];

        $data = array_merge_recursive($data, parent::toArray());
        if ($this->submit) {
            $data['tag'] = 'input';
            $data['attr']['type'] = 'submit';
            $data['attr']['value'] = $this->label;
            unset($data['text']);
        } else {
            $data['tag'] = 'a';
            $data['attr']['href'] = $this->link;
            $data['text'] = $this->label;
        }

        return $data;
    }

    protected function buildLink($link) {
        $jsparam = $this->jsparam;
        $param = '';
        $i = 0;
        foreach ($jsparam as $k => $p) {
            $i++;
            if ($k == 'param1') {
                if (strlen($param) > 0) {
                    $param .= ',';
                }
                $param .= "'" . $p . "'";
            }
            if ($this->type == 'link') {
                preg_match_all("/{([\w]*)}/", $link, $matches, PREG_SET_ORDER);
                foreach ($matches as $val) {
                    $str = $val[1]; //matches str without bracket {}
                    $b_str = $val[0]; //matches str with bracket {}
                    if ($k == $str) {
                        $link = str_replace($b_str, $p, $link);
                    }
                }
            }
        }

        return $link;
    }

    public function getSubmitValue() {
        return $this->submitValue ?: $this->label;
    }

    // protected function build() {
    //     parent::build();
    //     $this->reassignConfirm();
    //     $this->addClass('btn');
    //     $link = $this->link;
    //     if ($this->submit) {
    //         $this->tag = 'input';
    //         $this->setAttr('type', 'submit');
    //         $this->setAttr('value', $this->label);
    //     } else {
    //         $link = $this->buildLink($link);
    //         if ($link && strlen($link) > 0) {
    //             $this->setAttr('href', $link);
    //         }

    //         if (strlen($this->linkTarget)) {
    //             $this->setAttr('target', $this->linkTarget);
    //         }
    //     }
    //     if ($this->icon && strlen($this->icon) > 0) {
    //         $this->addIcon()->setIcon($this->icon);
    //     }
    //     if ($this->style != 'btn-icon-group') {
    //         $this->add($this->label);
    //     }
    // }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->setIndent($indent);

        $link = $this->link;
        $param = '';

        $classes = $this->classes;
        $classes = implode(' ', $classes);
        if (strlen($classes) > 0) {
            $classes = ' ' . $classes;
        }
        $custom_css = $this->renderStyle($this->custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }
        $addition_attribute = '';
        foreach ($this->attr as $k => $v) {
            $addition_attribute .= ' ' . $k . '="' . $v . '"';
        }
        $link = $this->buildLink($link);
        $linkTarget = '';
        if (strlen($this->linkTarget)) {
            $linkTarget = ' target="' . $this->linkTarget . '"';
        }
        $disabled = '';
        if ($this->disabled) {
            $disabled = ' disabled="disabled"';
        }
        $add_class = '';
        $add_attr = '';
        //check if have listener, we do not need  class confirm, because confirm os already on listener
        if (count($this->listeners) == 0) {
            if ($this->confirm && !$this->submitTo) {
                $add_class .= ' confirm';
            }
        }

        if ($this->style == 'btn-icon-group' && strlen($this->label) > 0) {
            $add_class .= ' tip-top';
            $add_attr .= ' data-original-title="' . c::e($this->label) . '"';
        }
        if (strlen($this->confirmMessage) > 0) {
            $add_attr .= ' data-confirm-message="' . c::e($this->confirmMessage) . '"';
        }

        if ($this->renderAsInput()) {
            $input_type = 'button';

            if ($this->submit) {
                $input_type = 'submit';
            }
            if ($this->button) {
                $html->appendln('<button id="' . $this->id . '" name="' . $this->name . '" class="btn btn-primary' . $add_class . $classes . '" type="' . $input_type . '"' . $disabled . $add_attr . $addition_attribute . $custom_css . '>' . $this->label . '</button>');
                $html->append($this->getIconHtml());
                $html->appendln($this->label . '</button>');
            } else {
                $html->appendln('<button type="submit" id="' . $this->id . '" name="' . $this->name . '" class="btn btn-primary' . $add_class . $classes . '" type="' . $input_type . '" ' . $disabled . $add_attr . $addition_attribute . $custom_css . ' value="' . $this->getSubmitValue() . '">' . $this->label . '</button>');
            }
        } else {
            if ($this->type == 'jsfunc') {
                $link = 'javascript:;';
                if ($this->jsfunc != '') {
                    $link = 'javascript:' . $this->jsfunc . '(' . $param . ')';
                }
            }
            //prefix
            if ($this->style == 'btn-dropdown') {
                $html->appendln('<li>');
            } elseif ($this->style == 'btn-group-toggle-radio') {
                if ($this->isActive) {
                    $classes .= ' active';
                }

                $html->appendln('<label class="btn ' . $add_class . '' . $classes . '">');
            }

            //link
            if ($this->style == 'btn-dropdown') {
                $html->appendln('<a id="' . $this->id . '" href="' . $link . '"' . $linkTarget . ' class=" ' . $add_class . '' . $classes . '" ' . $disabled . $add_attr . $addition_attribute . $custom_css . '>');
            } elseif ($this->style == 'btn-group-toggle-radio') {
                $checkedAttr = '';
                if ($this->isActive) {
                    $checkedAttr = 'checked="checked"';
                }
                $html->appendln('
                    <input type="radio" name="' . $this->id . '" id="' . $this->id . '" autocomplete="off" ' . $checkedAttr . '>
               ');
            } else {
                $html->appendln('<a id="' . $this->id . '" href="' . $link . '"' . $linkTarget . ' class="btn ' . $add_class . '' . $classes . '" ' . $disabled . $add_attr . $addition_attribute . $custom_css . '>');
            }
            //ico
            $html->append($this->getIconHtml());
            //label
            if ($this->style != 'btn-icon-group') {
                $html->append($this->label);
            }
            //close tag
            $html->append('</a>');
            //suffix
            if ($this->style == 'btn-dropdown') {
                $html->appendln('</li>');
            } elseif ($this->style == 'btn-group-toggle-radio') {
                $html->appendln('</label>');
            }
        }

        return $html->text();
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);

        if ($this->disabled) {
            $js->appendln("jQuery('#" . $this->id . "').click(function(e) { e.preventDefault(); });");
        } else {
            if ($this->renderAsInput()) {
                if (strlen($this->link) > 0) {
                    if ($this->submit) {
                        $js->appendln("jQuery('#" . $this->id . "').click(function() { jQuery(this).closest('form').attr('action','" . $this->link . "'); });");
                    } else {
                        $js->appendln("jQuery('#" . $this->id . "').click(function() { window.location.href='" . $this->link . "'; });");
                    }
                }
            } else {
                if (strlen($this->submitTo) > 0) {
                    $jsSubmitToTarget = '';
                    if (strlen($this->submitToTarget) > 0) {
                        $jsSubmitToTarget = "jQuery('#" . $this->id . "').closest('form').attr('target','" . $this->submitToTarget . "');";
                    }
                    $this->onClickListener()->addCustomHandler()->setJs("
                        jQuery('#" . $this->id . "').closest('form').attr('action','" . $this->submitTo . "');
                        " . $jsSubmitToTarget . "
                        jQuery('#" . $this->id . "').closest('form').submit();

                    ");
                }
            }
        }
        $this->reassignConfirm();

        $js->appendln(parent::jsChild($js->getIndent()))->br();

        return $js->text();
    }
}

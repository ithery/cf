<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 14, 2019, 7:29:54 PM
 */
class CElement_List_TabList_Tab extends CElement_Element {
    use CTrait_Compat_Element_Tab,
        CTrait_Element_Property_Label,
        CTrait_Element_Property_Icon;

    protected $active;
    protected $target;
    protected $ajaxUrl;
    protected $ajax;
    protected $nopadding;
    protected $tabList;

    public function __construct($id = '') {
        parent::__construct($id);
        $this->addFriend(CElement_List_TabList::class);

        $this->label = '';
        $this->target = '';
        $this->icon = '';
        $this->ajaxUrl = '';
        $this->ajax = true;
        $this->active = false;
        $this->nopadding = false;
    }

    public function setTabList(CElement_List_TabList $tabList) {
        $this->tabList = $tabList;
        return $this;
    }

    public static function factory($id = '') {
        return new CElement_List_TabList_Tab($id);
    }

    public function setActive($bool = true) {
        if ($bool && $this->tabList) {
            $this->tabList->setActiveTab($this->id);
        }
        $this->active = $bool;

        return $this;
    }

    public function setNoPadding($bool = true) {
        $this->nopadding = $bool;
        return $this;
    }

    public function setTarget($target) {
        $this->target = $target;
        return $this;
    }

    public function setAjaxUrl($url) {
        $this->ajaxUrl = $url;
        return $this;
    }

    public function getAjaxUrl() {
        return $this->ajaxUrl;
    }

    public function setAjax($bool = true) {
        $this->ajax = $bool;
        return $this;
    }

    public function headerHtml($indent = 0) {
        if (strlen($this->ajaxUrl) == 0) {
            if ($this->ajax) {
                $ajaxUrl = CAjax::createMethod()->setType('Reload')
                    ->setData('json', $this->json())
                    ->makeUrl();
                $this->setAjaxUrl($ajaxUrl);
            }
        }

        $classActive = '';
        if ($this->active) {
            $classActive = 'active';
        }
        $tab_icon = '';
        if (strlen($this->icon) > 0) {
            $tab_icon = ' data-icon="' . $this->icon . '"';
        }

        $tab_class = '';

        $classes = '';

        if (count($this->classes) > 0) {
            $classes = implode(' ', $this->classes);
        }
        if ($this->nopadding) {
            $classes .= ' nopadding';
        }
        if (strlen($classes) > 0) {
            $tab_class = ' data-class="' . $classes . '"';
        }

        $tab_responsive = '';
        $tab_tab = '';
        if (strlen($this->id) > 0) {
            $tab_tab = ' data-tab="' . $this->id . '"';
            $tab_responsive = ' tab-responsive="#' . $this->id . '"';
        }

        $tab_target = '';
        if (strlen($this->target) > 0) {
            $tab_target = ' data-target="' . $this->target . '"';
        }

        $tab_url = '';
        if (strlen($this->ajaxUrl) > 0) {
            $tab_url = ' data-url="' . $this->ajaxUrl . '"';
        }
        $tab_label = '';
        if (strlen($this->label) > 0) {
            $tab_label = $this->label;
        }

        $html = '<li class="nav-item w-100 p-1 ' . $classActive . '"><a href="javascript:;" ' . $tab_class . $tab_icon . $tab_tab . $tab_target . $tab_responsive . $tab_url . ' class="nav-link ' . $classActive . ' tab-ajax-load">';
        if ($this->icon) {
            $html .= '<span class="icon"><i class="' . $this->icon . '"></i></span> ';
        }
        $html .= $tab_label;
        $html .= '</a></li>';

        return $html;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->setIndent($indent);
        $add_class = '';
        $classActive = '';
        if ($this->active) {
            $classActive = 'active';
        }
        $additional_style = '';
        if (strlen($this->ajaxUrl) > 0) {
            $additional_style .= 'display:none;';
        }
        $html->appendln('<div class="tab-pane ' . $classActive . '" id="' . $this->id . '" style="' . $additional_style . '">');
        $html->appendln('<div class="tab-container ">');
        $html->appendln(parent::htmlChild($html->getIndent()));
        $html->appendln('</div>');
        $html->appendln('</div>');
        return $html->text();
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);
        $js->appendln(parent::js($indent));
        return $js->text();
    }
}

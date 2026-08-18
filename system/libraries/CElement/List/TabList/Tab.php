<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * A single tab: its nav entry (label/icon) is rendered by the parent
 * CElement_List_TabList's own view (see its build()) -- this class only
 * renders its own content pane (a plain tab-pane div, via the inherited
 * default html()), the same way CElement_Component_Widget/TreeView/Calendar
 * render themselves entirely through build() with no html()/js() override.
 */
class CElement_List_TabList_Tab extends CElement_Element {
    use CTrait_Compat_Element_Tab,
        CTrait_Element_Property_Label,
        CTrait_Element_Property_Icon;

    /**
     * Whether this is the currently selected tab.
     *
     * @var bool
     */
    protected $active;

    /**
     * Id of the shared content pane this tab's ajax content gets loaded into (non-ajax TabList).
     *
     * @var string
     */
    protected $target;

    /**
     * Url the tab's content is ajax-loaded from; auto-generated (a Reload of
     * this tab's own json()) by resolveAjaxUrl() if never explicitly set.
     *
     * @var string
     */
    protected $ajaxUrl;

    /**
     * Whether this tab's content is ajax-loaded, mirrors the parent TabList's own $ajax.
     *
     * @var bool
     */
    protected $ajax;

    /**
     * Whether this tab's content pane strips default padding.
     *
     * @var bool
     */
    protected $nopadding;

    /**
     * The TabList this tab belongs to, set via setTabList().
     *
     * @var null|CElement_List_TabList
     */
    protected $tabList;

    /**
     * This tab's own inner wrapper -- children added via add()/addDiv()/etc.
     * land here rather than directly under the tab-pane div itself, matching
     * the old html()'s nested <div class="tab-pane"><div class="tab-container">.
     *
     * @var CElement_Element_Div
     */
    protected $tabContainer;

    /**
     * @param string $id
     *
     * @return void
     */
    public function __construct($id = '') {
        parent::__construct($id);

        $this->label = '';
        $this->target = '';
        $this->icon = '';
        $this->ajaxUrl = '';
        $this->ajax = true;
        $this->active = false;
        $this->nopadding = false;
        $this->tag = 'div';
        $this->tabContainer = $this->addDiv()->addClass('tab-container');
        $this->wrapper = $this->tabContainer;
    }

    /**
     * @param CElement_List_TabList $tabList
     *
     * @return $this
     */
    public function setTabList(CElement_List_TabList $tabList) {
        $this->tabList = $tabList;

        return $this;
    }

    /**
     * @param string $id
     *
     * @return static
     */
    public static function factory($id = '') {
        /** @phpstan-ignore-next-line */
        return new static($id);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setActive($bool = true) {
        if ($bool && $this->tabList) {
            $this->tabList->setActiveTab($this->id);
        }
        $this->active = $bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive() {
        return (bool) $this->active;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setNoPadding($bool = true) {
        $this->nopadding = $bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNoPadding() {
        return (bool) $this->nopadding;
    }

    /**
     * @param string $target
     *
     * @return $this
     */
    public function setTarget($target) {
        $this->target = $target;

        return $this;
    }

    /**
     * @return string
     */
    public function getTarget() {
        return $this->target;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setAjaxUrl($url) {
        $this->ajaxUrl = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getAjaxUrl() {
        return $this->ajaxUrl;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setAjax($bool = true) {
        $this->ajax = $bool;

        return $this;
    }

    /**
     * Auto-generates an ajax url (a Reload method dump of this tab's own
     * json(), i.e. its content pane's html()+js()) if this tab is ajax-loaded
     * but was never given an explicit one via setAjaxUrl() -- called by the
     * parent CElement_List_TabList's build() for every tab.
     *
     * @return string
     */
    public function resolveAjaxUrl() {
        if (strlen($this->ajaxUrl) == 0 && $this->ajax) {
            $this->ajaxUrl = CAjax::createMethod()->setType('Reload')
                ->setData('json', $this->json())
                ->makeUrl();
        }

        return $this->ajaxUrl;
    }

    /**
     * @return void
     */
    public function build() {
        $this->addClass('tab-pane');
        if ($this->active) {
            $this->addClass('active');
        }
        if (strlen($this->ajaxUrl) > 0) {
            $this->setAttr('style', 'display:none;');
        }
    }
}

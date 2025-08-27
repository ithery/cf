<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 14, 2019, 7:10:42 PM
 */
class CElement_List_TabList extends CElement_List {
    use CTrait_Compat_Element_TabList;

    /**
     * Tabs.
     *
     * @var CElement_List_TabList_Tab[]
     */
    protected $tabs;

    protected $tabPosition;

    protected $activeTab;

    protected $ajax;

    protected $haveIcon;

    protected $widgetClass;

    protected $header;

    protected $jsHeader;

    protected $paramRequest;

    protected $widgetWrapperClass;

    protected $widgetBodyClass;

    protected $widgetHeaderClass;

    public function __construct($id = null) {
        parent::__construct($id);

        $this->tabPosition = 'left';
        $this->activeTab = '';
        $this->ajax = true;
        $this->haveIcon = false;
        $this->tabs = [];
        $this->widgetClass = [];
        $this->header = null;
        $this->jsHeader = '';
        $this->paramRequest = [];
        $this->widgetWrapperClass = c::theme('widget.class.wrapper', 'widget-box');
        $this->widgetBodyClass = c::theme('widget.class.body', 'widget-content');
        $this->widgetHeaderClass = c::theme('widget.class.header', 'widget-title');
    }

    /**
     * @param int $id
     *
     * @return \CElement_List_TabList
     */
    public static function factory($id = null) {
        /** @phpstan-ignore-next-line */
        return new static($id);
    }

    /**
     * @return CElement_Element_Div
     */
    public function header() {
        if ($this->header == null) {
            $this->header = CElement_Factory::createElement('div')->addClass('ml-auto');
        }

        return $this->header;
    }

    /**
     * @param string $id
     *
     * @return CElement_List_TabList_Tab
     */
    public function addTab($id = '') {
        $tab = CElement_List_TabList_Tab::factory($id)->setTabList($this);
        if (strlen($this->activeTab) == 0) {
            $this->activeTab = $tab->id();
        }
        $this->tabs[] = $tab;

        return $tab;
    }

    /**
     * @param string $tabId
     *
     * @return CElement_List_TabList
     */
    public function setActiveTab($tabId) {
        $this->activeTab = $tabId;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return CElement_List_TabList
     */
    public function setAjax($bool = true) {
        $this->ajax = $bool;

        return $this;
    }

    /**
     * @param string $tabPosition
     *
     * @return CElement_List_TabList
     */
    public function setTabPosition($tabPosition) {
        $this->tabPosition = $tabPosition;

        return $this;
    }

    /**
     * @return CElement_List_TabList
     */
    public function setTabPositionLeft() {
        return $this->setTabPosition('left');
    }

    /**
     * @return CElement_List_TabList
     */
    public function setTabPositionTop() {
        return $this->setTabPosition('top');
    }

    /**
     * @param array $paramRequest
     *
     * @return CElement_List_TabList
     */
    public function setParamRequest(array $paramRequest) {
        $this->paramRequest = $paramRequest;

        return $this;
    }

    /**
     * @param string|array $class
     *
     * @return CElement_List_TabList
     */
    public function addWidgetClass($class) {
        if (is_array($class)) {
            $this->widgetClass = array_merge($this->widgetClass, $class);
        } else {
            $this->widgetClass[] = $class;
        }

        return $this;
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->setIndent($indent);
        $ajaxClass = '';
        if ($this->ajax) {
            $ajaxClass = 'ajax';
        } else {
            //we create the ajax url if there are no url on tab
            foreach ($this->tabs as $tab) {
                $tab->setAjax(false);
            }
        }

        $classes = $this->classes;
        $classes = implode(' ', $classes);
        if (strlen($classes) > 0) {
            $classes = ' ' . $classes;
        }
        $classes .= ' ' . $ajaxClass;

        if ($this->tabPosition == 'left') {
            $classes .= ' vtabs';
        }
        $widgetClasses = $this->widgetClass;
        $widgetClasses = implode(' ', $widgetClasses);
        if (strlen($widgetClasses) > 0) {
            $widgetClasses = ' ' . $widgetClasses;
        }

        $html->appendln('<div class="row-fluid tab-list ' . $classes . '" id="' . $this->id . '">');
        $html->appendln('	<div class="span12">');

        $html->appendln('		<div class="row-fluid">');
        if ($this->tabPosition == 'top') {
            $html->appendln('           <div class="row-tab-menu row-tab-menu-top">');
        } else {
            $html->appendln('			<div class="span2 row-tab-menu row-tab-menu-left">');
        }

        if ($this->tabPosition == 'top') {
            $html->appendln('               <div class="top-nav-container d-flex align-items-center">');
        } else {
            $html->appendln('				<div class="side-nav-container affix-top ">');
        }

        $html->appendln('<ul id="' . $this->id . '-tab-nav" class="nav nav-tabs nav-stacked ' . ($this->tabPosition == 'left' ? 'tabs-vertical' : '') . '">');

        $activeTab = null;
        foreach ($this->tabs as $tab) {
            if (strlen($this->activeTab) == 0) {
                $this->setActiveTab($tab->id());
            }
            if ($tab->id() == $this->activeTab) {
                $tab->setActive(true);
                $activeTab = $tab;
            }
            $tab->setTarget('' . $this->id . '-ajax-tab-content');

            $html->appendln($tab->headerHtml($html->getIndent()));
        }

        $activeTabIcon = '';
        $activeTabLabel = '';

        if ($activeTab != null) {
            $activeTabIcon = $activeTab->getIcon();
            $activeTabLabel = $activeTab->getLabel();

            if (strlen($activeTab->getAjaxUrl()) > 0) {
                $tabAjaxUrl = $activeTab->getAjaxUrl();
            }
        }
        $html->appendln('					</ul>');

        if ($this->header != null) {
            $html->appendln($this->header->html($html->getIndent()));
            $this->jsHeader = $this->header->js();
        }

        $html->appendln('				</div>');
        $html->appendln('			</div>');

        if ($this->tabPosition == 'top') {
            $html->appendln('           <div class="row-tab-content row-tab-content-top">');
        } else {
            $html->appendln('           <div class="span10 row-tab-content row-tab-content-left">');
        }

        $html->appendln('				<div id="' . $this->id . '-tab-widget" class="' . $this->widgetWrapperClass . ' nomargin widget-transaction-tab ' . $widgetClasses . '">');
        if ($this->tabPosition != 'top') {
            $html->appendln('					<div class="' . $this->widgetHeaderClass . '">');
        }

        if ($this->tabPosition != 'top') {
            if ($this->haveIcon) {
                $html->appendln('						<span class="icon">');
                $html->appendln('							<i class="icon-' . $activeTabIcon . '"></i>');
                $html->appendln('						</span>');
            }
            $html->appendln('<h5>' . $activeTabLabel . '</h5>');
            $html->appendln('					</div>');
        }

        $html->appendln('					<div class="' . $this->widgetBodyClass . '">');

        if ($this->ajax) {
            $html->appendln('						<div id="' . $this->id . '-ajax-tab-content" class="ajax-tab-content">');
            $html->appendln('						</div>');
        } else {
            foreach ($this->tabs as $tab) {
                $html->appendln($tab->html($html->getIndent()));
            }
        }

        $html->appendln('					</div>');
        $html->appendln('				</div>');
        $html->appendln('			</div>');
        $html->appendln('		</div>');
        $html->appendln('	</div>');
        $html->appendln('</div>');

        return $html->text();
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);
        $js->appendln($this->jsHeader);
        foreach ($this->tabs as $tab) {
            $js->appendln($tab->js($js->getIndent()));
        }
        $js->appendln("
            jQuery('#" . $this->id . ' #' . $this->id . "-tab-nav > li > a.tab-ajax-load').click(function(event) {
                event.preventDefault();
                var target = jQuery(this).attr('data-target');
                var url = jQuery(this).attr('data-url');
                var method = jQuery(this).attr('data-method');
                if(!method) method='get';
                var pare = jQuery(this).parent();

                if(pare.prop('tagName')=='LI') {

                    pare.parent().children().removeClass('active');
                    pare.addClass('active');
                    pare.parent().find('> li > a').removeClass('active');
                    pare.find('> a').addClass('active');
                }
                jQuery(this).parent().children().removeClass('active');
                jQuery(this).addClass('active');

                jQuery(this).parent().find('> li > a').removeClass('active');
                jQuery(this).find('> a').addClass('active');
                var widget_tab = jQuery('#" . $this->id . "-tab-widget');
                if(widget_tab.length>0) {
                    var data_icon = jQuery(this).attr('data-icon');
                    var data_class = jQuery(this).attr('data-class');
                    var data_text = jQuery(this).text();

                    if(data_icon) widget_tab.find('> ." . $this->widgetHeaderClass . " .icon i').first().attr('class',data_icon);

                    if(data_text) widget_tab.find('> ." . $this->widgetHeaderClass . " h5').first().html(data_text);
                    var widget_content = widget_tab.find('." . $this->widgetBodyClass . "').first();
                    widget_content.removeAttr('class').addClass('" . $this->widgetBodyClass . "');
                    if(data_class) widget_content.addClass(data_class);
                }

                if(jQuery('#" . $this->id . "').hasClass('ajax')) {
                    var reloadOptions = {};
                    reloadOptions.selector = '#'+target;
                    reloadOptions.url = url;
                    reloadOptions.method = method;
                    reloadOptions.dataAddition = " . json_encode($this->paramRequest) . ";
                    cresenity.reload(reloadOptions);
                }
                else {
                    var tab_id = jQuery(this).attr('data-tab');
                    jQuery('#'+tab_id).parent().children().hide();
                    jQuery('#'+tab_id).show();
                }

            });

        ");

        $js->appendln("
            jQuery('#" . $this->id . "').find('li.active a.tab-ajax-load').click();
            if(!jQuery('#" . $this->id . "').hasClass('ajax')) {
                setTimeout(function() {
                    //console.log(jQuery('#" . $this->id . "').find('li.active a.tab-ajax-load').attr('data-tab'));
                    jQuery('#" . $this->id . "').find('li.active a.tab-ajax-load').click();
                },500);
            }
        ");

        return $js->text();
    }
}

<?php

/**
 * @deprecated dont use this anymore
 */
// @codingStandardsIgnoreStart
class CCollapseList extends CElement {
    protected $items = [];

    protected $active_tab = '';

    public function __construct($id = '') {
        parent::__construct($id);

        $this->items = [];
    }

    public static function factory($id = '') {
        return new CCollapseList($id);
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->set_indent($indent);

        $classes = $this->generate_class();

        $html->appendln('<div class="collapse-list ' . $classes . '">');
        $html->appendln('   <div class="collapse-nav visible-lg visible-md">');
        $html->appendln('      <ul class="nav nav-tabs">');
        foreach ($this->items as $item_k => $item_v) {
            $html->appendln($item_v->header_html($html->get_indent()));
        }
        $html->appendln('       </ul>');
        $html->appendln('   </div>');
        $html->appendln('   <div class="collapse-panels">');
        foreach ($this->items as $item_k => $item_v) {
            $html->appendln($item_v->html($html->get_indent()));
        }
        $html->appendln('   </div>');
        $html->appendln('</div>');
        $html->appendln('<div class="clear-both"></div>');

        $html->appendln('<style>');
        $html->appendln('
            .collapse-list {}
                .collapse-list .collapse-panels {
                    position: relative;
                }
                .collapse-list .collapse-panel {
                    color: #333;
                    margin-bottom: 3px;
                }
                .collapse-list .collapse-panel .collapse-heading {
                    background-color: #f5f5f5;
                    border: 1px solid #ddd;
                    padding: 10px 15px;
                    cursor: pointer;
                }
                .collapse-list .collapse-panel .collapse-body {
                    border: 1px solid #ddd;
                    padding: 10px 15px;
                    display: none;
                    border-top: 0px;
                }
                .collapse-list .clear-both {
                    clear-both;
                }
                @media (min-width: 992px) {
                    .collapse-list .collapse-panel {
                        margin-bottom: 0px;
                    }
                    .collapse-list .collapse-panel .collapse-body {
                        position: absolute;
                    }
                    .collapse-list .collapse-panel .collapse-body.active {
                        position: relative;
                    }
                }
            ');
        $html->appendln('</style>');
        return $html->text();
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->set_indent($indent);

        $js->appendln("
            var bodyActive = jQuery('.collapse-list .collapse-body.active');
            var panel = bodyActive.parents('.collapse-panel');
            bodyActive.slideDown();
            var data_url = panel.find('.collapse-heading').attr('data-url');
            var method = panel.find('.collapse-heading').attr('data-method');
            if(!method) method='get';
            if (typeof data_url !== 'undefined'){
                var target = 'body-' + panel.attr('id');
                $.cresenity.reload(target, data_url,method);
            }

            jQuery('.collapse-list .collapse-heading').on('click', function(){
                var data_url = jQuery(this).attr('data-url');
                var method = jQuery(this).attr('data-method');
                if(!method) method='get';
                var panel = jQuery(this).parents('.collapse-panel');
                var panelId = panel.attr('id');
                var currentBody = panel.find('.collapse-body');
                var isActive = currentBody.hasClass('active');
                var activePanels = jQuery(this).parents('.collapse-list').find('.collapse-body.active');
                activePanels.slideUp();
                activePanels.removeClass('active');
                console.log('li[data-target=\'' + panelId + '\']');

                var collapseNav = jQuery(this).parents('.collapse-list');
                collapseNav.find('.collapse-nav li.active').removeClass('active');
                collapseNav.find('.collapse-nav a[data-target=\'' + panelId + '\']').parents('li').addClass('active');
                if (isActive) {
                    // this panel is active
                }
                else {
                    currentBody.addClass('active');
                    currentBody.slideDown();
                }

                if (typeof data_url !== 'undefined'){
                    var target = 'body-' + panel.attr('id');
                    $.cresenity.reload(target, data_url,method);
                }
            });
            jQuery('.collapse-list .collapse-nav .nav li a').on('click', function(){
                var target = jQuery(this).attr('data-target');
                var data_url = jQuery(this).attr('data-url');
                var method = jQuery(this).attr('data-method');
                if(!method) method='get';
                var panel = jQuery('#' + target);

                var currentBody = panel.find('.collapse-body');
                var isActive = currentBody.hasClass('active');

                if (isActive) {
                    // do nothing
                }
                else {
                    var activePanels = jQuery(this).parents('.collapse-list').find('.collapse-body.active');
                    activePanels.hide();
                    activePanels.removeClass('active');
                    currentBody.addClass('active');
                    currentBody.slideDown();
                    jQuery(this).parents('.collapse-nav').find('li.active').removeClass('active');
                    jQuery(this).parents('li').addClass('active');
                }
                if (typeof data_url !== 'undefined'){
                    var target = 'body-' + panel.attr('id');
                    $.cresenity.reload(target, data_url,method);
                }

                return false;
            });
            ");

        $js->appendln(parent::js($indent));
        return $js->text();
    }

    public function add_item($id = '') {
        $item = CCollapse::factory($id);
        if (strlen($this->active_tab) == 0) {
            $this->active_tab = $item->id;
        }
        $this->items[] = $item;
        return $item;
    }
}

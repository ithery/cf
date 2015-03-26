<?php

class CTabList extends CElement {

    protected $tabs;
    protected $scrollspy;
    protected $tab_position;
    protected $active_tab;
    protected $ajax;
    protected $have_icon;

    public function __construct($id) {
        parent::__construct($id);

        $this->tab_position = "left";
        $this->active_tab = "";
        $this->ajax = true;
        $this->have_icon = false;
        $this->scrollspy = true;
        $this->tabs = array();
    }

    public static function factory($id) {
        return new CTabList($id);
    }

    public function add_tab($id = "") {
        $tab = CTab::factory($id);
        if (strlen($this->active_tab) == 0)
            $this->active_tab = $tab->id;
        $this->tabs[] = $tab;
        return $tab;
    }

    public function active_tab($tab_id) {
        $this->set_active_tab($tab_id);
        return $this;
    }

    public function set_active_tab($tab_id) {
        $this->active_tab = $tab_id;
        return $this;
    }

    public function set_scrollspy($bool) {
        $this->scrollspy = $bool;
        return $this;
    }

    public function set_ajax($bool) {
        $this->ajax = $bool;
        return $this;
    }

    public function html($indent = 0) {


        /*
          $html = new CStringBuilder();
          $html->set_indent($indent);
          $add_class="";
          if($this->tab_position=="left") {
          $add_class.=" tabs-left";
          }
          $html->appendln('<div class="tabbable '.$add_class.'">');
          $html->appendln('<ul class="nav nav-tabs ">');
          foreach($this->tabs as $tab) {

          if($tab->id==$this->active_tab) {
          $tab->set_active(true);
          }
          $html->appendln($tab->header_html($html->get_indent()));
          }
          $html->appendln('</ul>');
          $html->appendln('<div class="tab-content">');
          foreach($this->tabs as $tab) {
          $html->appendln($tab->html($html->get_indent()));
          }
          $html->appendln('</div>');
          $html->appendln('</div>');
          ;
         */
        $html = new CStringBuilder();
        $html->set_indent($indent);
        $ajax_class = "";
        if ($this->ajax) {
            $ajax_class = "ajax";
            //we create the ajax url if there are no url on tab
        } else {
            foreach($this->tabs as $tab) {
                $tab->set_ajax(false);
            }
        }
        $html->appendln('<div class="row-fluid ' . $ajax_class . '" id="' . $this->id . '">');
        $html->appendln('	<div class="span12">');

        $html->appendln('		<div class="row-fluid">');
        $html->appendln('			<div class="span2">');
        $html->appendln('				<div class="side-nav-container affix-top">');
        $html->appendln('					<ul id="' . $this->id . '-tab-nav" class="nav nav-tabs nav-stacked">');

        $active_tab = null;
        foreach ($this->tabs as $tab) {
            if (strlen($this->active_tab) == 0) {
                $this->active_tab($tab->id);
            }
            if ($tab->id == $this->active_tab) {
                $tab->set_active(true);
                $active_tab = $tab;
            }
            $tab->set_target('' . $this->id . '-ajax-tab-content');

            $html->appendln($tab->header_html($html->get_indent()));
        }
        $active_tab_icon = "";
        $active_tab_label = "";

        if ($active_tab != null) {
            $active_tab_icon = $active_tab->icon;
            $active_tab_label = $active_tab->label;

            if (strlen($active_tab->ajax_url) > 0) {
                $tab_ajax_url = $active_tab->ajax_url;

                /*
                  $url_base = curl::base();
                  if(substr($tab_ajax_url,0,strlen($url_base)) == $url_base) {
                  $tab_ajax_url = substr($tab_ajax_url,strlen($url_base));
                  }
                  $tab_ajax_url = curl::base(true,'http').$tab_ajax_url;
                 */
                //$active_tab_content = CCurl::factory($tab_ajax_url)->exec()->response();
            }
        }
        $html->appendln('					</ul>');
        $html->appendln('				</div>');
        $html->appendln('			</div>');
        $html->appendln('			<div class="span10">');
        $html->appendln('				<div id="' . $this->id . '-tab-widget" class="widget-box nomargin widget-transaction-tab">');
        $html->appendln('					<div class="widget-title">');
        if ($this->have_icon) {
            $html->appendln('						<span class="icon">');
            $html->appendln('							<i class="icon-' . $active_tab_icon . '"></i>');
            $html->appendln('						</span>');
        }
        $html->appendln('						<h5>' . $active_tab_label . '</h5>');
        $html->appendln('					</div>');

        $html->appendln('					<div class="widget-content">');

        if ($this->ajax) {
            $html->appendln('						<div id="' . $this->id . '-ajax-tab-content">');
            $html->appendln('						</div>');
        } else {
            foreach ($this->tabs as $tab) {
                $html->appendln($tab->html($html->get_indent()));
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
        $js->set_indent($indent);
        foreach ($this->tabs as $tab) {
            $js->appendln($tab->js($js->get_indent()));
        }
        $js->appendln("
			
			jQuery('#" . $this->id . " #" . $this->id . "-tab-nav > li > a.tab-ajax-load').click(function(event) {
				event.preventDefault();
				var target = jQuery(this).attr('data-target');
				var url = jQuery(this).attr('data-url');
				var method = jQuery(this).attr('data-method');
				if(!method) method='get';
				var pare = jQuery(this).parent();
		
				if(pare.prop('tagName')=='LI') {
					pare.parent().children().removeClass('active');
					pare.addClass('active');
				}
				jQuery(this).parent().children().removeClass('active');
				jQuery(this).addClass('active');
				var widget_tab = jQuery('#" . $this->id . "-tab-widget');
				if(widget_tab.length>0) {
					
					
					
					var data_icon = jQuery(this).attr('data-icon');
					var data_class = jQuery(this).attr('data-class');
					var data_text = jQuery(this).text();
					if(data_icon) widget_tab.find('.widget-title .icon i').attr('class',data_icon);
					
					if(data_text) widget_tab.find('.widget-title h5').html(data_text);
					var widget_content = widget_tab.find('.widget-content');
					widget_content.removeAttr('class').addClass('widget-content');
					
					if(data_class) widget_content.addClass(data_class);
				}
				
				if(jQuery('#" . $this->id . "').hasClass('ajax')) {
					$.cresenity.reload(target,url,method);
				} else {
					var tab_id = jQuery(this).attr('data-tab');
					jQuery('#'+tab_id).parent().children().hide();
					jQuery('#'+tab_id).show();
					
				}
				
			});
		
		");


        $js->appendln("
			jQuery('#" . $this->id . "').find('li.active a.tab-ajax-load').click();
			
		");

        return $js->text();
    }

}

?>
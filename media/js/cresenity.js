jQuery.fn.extend({
  slideRightShow: function() {
    return this.each(function() {
        $(this).show('slide', {direction: 'right'}, 1000);
    });
  },
  slideLeftHide: function() {
    return this.each(function() {
      $(this).hide('slide', {direction: 'left'}, 1000);
    });
  },
  slideRightHide: function() {
    return this.each(function() {
      $(this).hide('slide', {direction: 'right'}, 1000);
    });
  },
  slideLeftShow: function() {
    return this.each(function() {
      $(this).show('slide', {direction: 'left'}, 1000);
    });
  }
});
var xhr_ajax_load=false;
jQuery(document).ready(function() {

	//jQuery('select').chosen();
	//jQuery('select').select2();
	// === Sidebar navigation === //
	$('.subnavbar ul li.dropdown > a').click(function(e) {
		e.stopPropagation();
		e.preventDefault();
		var li = $(this).parent();
		var submenu = $(this).siblings('ul');
		var submenus = $('.subnavbar ul.mainnav li.dropdown-submenu > ul,.subnavbar ul.mainnav li.dropdown > ul');
		
		if(li.hasClass('open'))
		{
			submenu.slideUp();
			li.removeClass('open');
		} else 
		{
			
			submenus.each(function() {
				$(this).slideUp();
				$(this).parent().removeClass('open');
			});			
			submenu.slideDown();
			li.addClass('open');	
		}
	});
	$('.subnavbar ul li.dropdown-submenu > a').click(function(e) {
		e.stopPropagation();
		e.preventDefault();
		var li = $(this).parent();
		var li_parents = $(this).parents('li');
		var ul_parents = $(this).parents('ul');
		var submenu = $(this).siblings('ul');
		var submenus = $('.subnavbar ul.mainnav li.dropdown-submenu ul');
		if(li.hasClass('open')) {
			submenus.each(function() {
				var this_submenu = $(this);
				var this_parent_ul = false;
				ul_parents.each(function() {
					if(this_submenu.length>0&&$(this).length>0&&this_submenu[0]==$(this)[0]) {
						this_parent_ul = true;
					}
				});
				if(!this_parent_ul) {
					$(this).slideUp();
					$(this).parent().removeClass('open');
				}
			
			});
			submenu.slideUp();
			li.removeClass('open');
		} else {
			submenus.each(function() {
				var this_submenu = $(this);
				var this_parent_ul = false;
				ul_parents.each(function() {
					if(this_submenu.length>0&&$(this).length>0&&this_submenu[0]==$(this)[0]) {
						this_parent_ul = true;
					}
				});
				if(!this_parent_ul) {
					$(this).slideUp();
					$(this).parent().removeClass('open');
				}
			
			});
			submenu.slideDown();
			ul_parents.addClass('open');
			ul_parents.slideDown();
			li.addClass('open');	
		}
	});
	
	



// === Tooltips === //
	$('.tip').tooltip();	
	$('.tip-left').tooltip({ placement: 'left' });	
	$('.tip-right').tooltip({ placement: 'right' });	
	$('.tip-top').tooltip({ placement: 'top' });	
	$('.tip-bottom').tooltip({ placement: 'bottom' });	
	
	// === Search input typeahead === //
	$('#search input[type=text]').typeahead({
		source: ['Dashboard','Form elements','Common Elements','Validation','Wizard','Buttons','Icons','Interface elements','Support','Calendar','Gallery','Reports','Charts','Graphs','Widgets'],
		items: 4
	});
	
	// === Fixes the position of buttons group in content header and top user navigation === //
	function fix_position()
	{
		var uwidth = $('#user-nav > ul').width();
		$('#user-nav > ul').css({width:uwidth,'margin-left':'-' + uwidth / 2 + 'px'});
        
        var cwidth = $('#content-header .btn-group').width();
        $('#content-header .btn-group').css({width:cwidth,'margin-left':'-' + uwidth / 2 + 'px'});
	}
	
	// === Style switcher === //
	$('#style-switcher i').click(function()
	{
		if($(this).hasClass('open'))
		{
			$(this).parent().animate({marginRight:'-=190'});
			$(this).removeClass('open');
		} else 
		{
			$(this).parent().animate({marginRight:'+=190'});
			$(this).addClass('open');
		}
		$(this).toggleClass('icon-arrow-left');
		$(this).toggleClass('icon-arrow-right');
	});
	
	$('#style-switcher a').click(function()
	{
		var style = $(this).attr('href').replace('#','');
		$('.skin-color').attr('href','css/unicorn.'+style+'.css');
		$(this).siblings('a').css({'border-color':'transparent'});
		$(this).css({'border-color':'#aaaaaa'});
	});	
	
	
	$(document).on('click','a.confirm',function(e) {
		var ahref = $(this).attr('href');
		e.preventDefault();
		e.stopPropagation();
		bootbox.confirm("Are you sure?", function(confirmed) {
			if(confirmed) {
				window.location.href=ahref;
			}
		});
	});
	
	$(document).on('click','a.ajax-load',function(e) {
		event.preventDefault();
		var target = jQuery(this).attr('data-target');
		var url = jQuery(this).attr('data-url');
		var method = jQuery(this).attr('data-method');
		if(!method) method='get';
		jQuery(target).addClass('loading');
		jQuery(target).html('<div id="ajax-tab-loading" style="text-align:center; margin-top:100px; margin-bottom:100px;"><i class="icon icon-repeat icon-spin icon-4x"></i></div>');
		var pare = jQuery(this).parent();
		
		if(pare.prop("tagName")=='LI') {
			pare.parent().children().removeClass('active');
			pare.addClass('active');
		}
		jQuery(this).parent().children().removeClass('active');
		jQuery(this).addClass('active');
		
		if(jQuery(target).parent().hasClass('widget-content')) {
			
			var widget_tab = jQuery(target).parent().parent();
			
			var data_icon = jQuery(this).attr('data-icon');
			var data_class = jQuery(this).attr('data-class');
			var data_text = jQuery(this).text();
			if(data_icon) widget_tab.find('.widget-title .icon i').attr('class',data_icon);
			
			if(data_text) widget_tab.find('.widget-title h5').html(data_text);
			var widget_content = widget_tab.find('.widget-content');
			widget_content.removeAttr('class').addClass('widget-content')
			if(data_class) widget_content.addClass(data_class);
		}
		if(xhr_ajax_load!==false) xhr_ajax_load.abort();
		xhr_ajax_load = jQuery.ajax({
			type: method,
			url: url,
			dataType: 'text',
			
		}).done(function( data ) {
			
			jQuery(target).html(data);
			jQuery(target).removeClass('loading');
			
		}).error(function(obj,t,msg) {
			if(msg!='abort') {
				$.cresenity.message('error','Error, please call administrator... (' + msg + ')');
			}
		});
	});
	
	
	
	$('.slimscroll').each(function() {
        var h = '250px';

        if($(this).attr('height')) h=$(this).attr('height');
        $(this).slimScroll({'height':h});
    });
	
	
});
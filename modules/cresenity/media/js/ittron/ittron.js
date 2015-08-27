
$(document).ready(function () {
	
    // Add body-small class if window less than 768px
    if ($(this).width() < 769) {
        $('body').addClass('body-small')
    } else {
        $('body').removeClass('body-small')
    }

    // MetsiMenu
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

	
});
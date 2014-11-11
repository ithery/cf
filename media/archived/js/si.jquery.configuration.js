	$(function(){
		$("ul.nav").superfish({
			animation:{
			height: "show",
			width: "show"
			}, speed : 500
		});
			
		//tooltip
		$(".tooltip").easyTooltip();
		$(".close").click(
			function () {
				$(this).fadeTo(400, 0, function () { // Links with the class "close" will close parent
					$(this).slideUp(400);
				});
			return false;
			}
		);
		//sortable, portlets
		$(".column").sortable({
			connectWith: '.column'
		});
		
		$(".sort").sortable({
			connectWith: '.sort'
		});
		

		$(".portlet").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
		.find(".portlet-header")
		.addClass("ui-widget-header ui-corner-all")
		.prepend('<span class="ui-icon ui-icon-circle-arrow-s"></span>')
		.end()
		.find(".portlet-content");

		$(".portlet-header .ui-icon").click(function() {
			$(this).toggleClass("ui-icon-minusthick");
			$(this).parents(".portlet:first").find(".portlet-content").toggle();
		});

		$(".column").disableSelection();
		// Accordion
		$("#accordion").accordion({ header: "h3" });

		// Tabs
		$('#tabs').tabs();
		
		//hover states on the static widgets
		$('#dialog_link, ul#icons li').hover(
			function() { $(this).addClass('ui-state-hover'); }, 
			function() { $(this).removeClass('ui-state-hover'); }
		);
		$('.overflow-scroll').mousedown(function (event) {
			$(this)
				.data('down', true)
				//.data('y', event.clientY)
				.data('x', event.clientX)
				//.data('scrollTop', this.scrollTop)
				.data('scrollLeft', this.scrollLeft);
				
			return false;
		}).mouseup(function (event) {
			$(this).data('down', false);
		}).mousemove(function (event) {
			if ($(this).data('down') == true) {
				this.scrollLeft = $(this).data('scrollLeft') + $(this).data('x') - event.clientX;
				//this.scrollTop = $(this).data('scrollTop') + $(this).data('y') - event.clientY;
			}
		}).css({
			'overflow' : 'hidden',
			'cursor' : '-moz-grab',
		});
	});
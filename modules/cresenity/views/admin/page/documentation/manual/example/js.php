jQuery(document).ready(function() {
	var $window = $(window)
	var $body   = $(document.body)

    var navHeight = $('.navbar').outerHeight(true) + 10

    $body.scrollspy({
      target: '.bs-docs-sidebar',
      offset: 10
    })
	// back to top
     // side bar
    setTimeout(function () {
		$('body').scrollspy({ target: '#ul-sidenav' });
		console.log($('.bs-docs-sidenav'));
		$('.bs-docs-sidenav').affix({
			offset: {
				top: function () { return $window.width() <= 980 ? 290 : 210 },
				bottom: 270,
			}
		});
    }, 100);
	
	
});

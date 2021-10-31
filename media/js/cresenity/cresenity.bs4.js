// Auto update layout
(function () {

    window.layoutHelpers && window.layoutHelpers.setAutoUpdate(true);

})();

// Collapse menu
(function () {
    if (window.layoutHelpers) {
        if ($('#layout-sidenav').hasClass('sidenav-horizontal') || window.layoutHelpers.isSmallScreen()) {
            return;
        }

        try {
            window.layoutHelpers.setCollapsed(
                    localStorage.getItem('layoutCollapsed') === 'true',
                    false
                    );
        } catch (e) {
            //do nothing
        }
    }
})();

$(function () {
    if (window.layoutHelpers) {
        // Initialize sidenav
        $('#layout-sidenav').each(function () {
            new SideNav(this, {
                orientation: $(this).hasClass('sidenav-horizontal') ? 'horizontal' : 'vertical'
            });
        });

        // Initialize sidenav togglers
        $('body').on('click', '.layout-sidenav-toggle', function (e) {
            e.preventDefault();
            window.layoutHelpers.toggleCollapsed();
            if (!window.layoutHelpers.isSmallScreen()) {
                try {
                    localStorage.setItem('layoutCollapsed', String(window.layoutHelpers.isCollapsed()));
                } catch (e) {
                }
            }
        });

        if ($('html').attr('dir') === 'rtl') {
            $('#layout-navbar .dropdown-menu').toggleClass('dropdown-menu-right');
        }
    }
});
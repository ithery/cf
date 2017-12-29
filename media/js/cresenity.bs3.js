/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {


    // Add body-small class if window less than 768px
    if ($(this).width() < 769) {
        $('body').addClass('body-small')
    } else {
        $('body').removeClass('body-small')
    }

    // MetisMenu
    //$('.navbar-static-side .mainnav').metisMenu();
    $('.navbar-static-side .mainnav .dropdown-submenu a.dropdown-toggle').on("click", function (e) {
        $(this).parent().siblings().find('a.dropdown-toggle').next('ul').hide();
        $(this).next('ul').toggle();
        e.stopPropagation();
        e.preventDefault();
    });
});

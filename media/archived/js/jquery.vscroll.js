/*
* jQuery vScroll
* Copyright (c) 2010 Simon Hibbard
* 
* Permission is hereby granted, free of charge, to any person
* obtaining a copy of this software and associated documentation
* files (the "Software"), to deal in the Software without
* restriction, including without limitation the rights to use,
* copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the
* Software is furnished to do so, subject to the following
* conditions:

* The above copyright notice and this permission notice shall be
* included in all copies or substantial portions of the Software.
* 
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
* EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
* OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
* NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
* HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
* WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
* FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
* OTHER DEALINGS IN THE SOFTWARE. 
*/

/*
* Version: V1.0.0
* Release: 06-08-2010
* Based on jQuery 1.4.2
*/

(function ($) {
    $.fn.vScroll = function (options) {

        var defaults = {
            speed: 500,
            upID: "#up-arrow",
            downID: "#bottom-arrow"
        };
        var options = $.extend(defaults, options);

        return this.each(function () {

            obj = $(this);
            obj.css("overflow","hidden");
            obj.children().each(
                function (intIndex) {
                    $(this).addClass("vscroll-" + intIndex);

                });


            var itemCount = 0;

            $(options.downID).click(function () {
                var nextCount = itemCount + 1;
                if ($('.vscroll-' + nextCount).length) {
                    var divH = $('.vscroll-' + itemCount).outerHeight();
                    itemCount++;
                    $("#vscroller").animate({
                        top: "-=" + divH + "px"
                    }, options.speed);
                }
            });

            $(options.upID).click(function () {
                var prevCount = itemCount - 1;
                if ($('.vscroll-' + prevCount).length) {
                    itemCount--;
                    var divH = $('.vscroll-' + itemCount).outerHeight();
                    $("#vscroller").animate({
                        top: "+=" + divH + "px"
                    }, options.speed);
                }
            });

            obj.children().wrapAll("<div style='position: relative; top: 0' id='vscroller'></div>");
        });

    };

})(jQuery);

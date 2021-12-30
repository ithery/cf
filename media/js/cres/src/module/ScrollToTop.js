import cf from '../CF';
export default class ScrollToTop {
    //startline: Integer. Number of pixels from top of doc scrollbar is scrolled before showing control
    //scrollto: Keyword (Integer, or "Scroll_to_Element_ID"). How far to scroll document up when control is clicked on (0=top).
    constructor() {
        this.setting = {
            startline: 100,
            scrollto: 0,
            scrollduration: 1000,
            fadeduration: [500, 100]
        };
        //HTML for control, which is auto wrapped in DIV w/ ID="topcontrol"
        this.controlHTML = cf.config.scrollToTopHtml || '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADMAAAAqCAYAAAAeeGN5AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjM2RTVENEJCODY3RTExRTI5MTFEQzg2NjQyQ0VGQzhDIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjM2RTVENEJDODY3RTExRTI5MTFEQzg2NjQyQ0VGQzhDIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MzZFNUQ0Qjk4NjdFMTFFMjkxMURDODY2NDJDRUZDOEMiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MzZFNUQ0QkE4NjdFMTFFMjkxMURDODY2NDJDRUZDOEMiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz6Mw5vNAAAAkUlEQVR42uzYoRGAMBBEUbKKlqAKKAKKogm6oCWQTAQawd0lE/5Xa5/dNEzL2TWSuoYCAwYMGDBgwIAB8zPMsW99E5gMGef1igApApJ3BEgRkCdvkKIgESBFQrxBioZ4glQC4gVSKYgHSCUh1iCVhliCVAPECqRaIBYg1QT5Ckp8zWDAgAEDBgwYMGDAvHQLMACw9mxL+kYUJQAAAABJRU5ErkJggg==" style="width:51px; height:42px" />';
        //offset of control relative to right/ bottom of window corner
        this.controlattrs = {
            offsetx: 5,
            offsety: 5
        };
        //Enter href value of HTML anchors on the page that should also act as "Scroll Up" links
        this.anchorkeyword = '#top';

        this.state = {
            isvisible: false,
            shouldvisible: false
        };

        this.keepfixed = function () {
            let $window = jQuery(window);
            let controlx = $window.scrollLeft() + $window.width() - this.$control.width() - this.controlattrs.offsetx;
            let controly = $window.scrollTop() + $window.height() - this.$control.height() - this.controlattrs.offsety;
            this.$control.css({
                left: controlx + 'px',
                top: controly + 'px'
            });
        };
        this.togglecontrol = function () {
            let scrolltop = jQuery(window).scrollTop();
            if (!this.cssfixedsupport) {
                this.keepfixed();
            }
            this.state.shouldvisible = (scrolltop >= this.setting.startline) ? true : false;
            if (this.state.shouldvisible && !this.state.isvisible) {
                this.$control.stop().animate({
                    opacity: 1,
                    zIndex: 99999
                }, this.setting.fadeduration[0]);
                this.state.isvisible = true;
            } else if (this.state.shouldvisible === false && this.state.isvisible) {
                this.$control.stop().animate({
                    opacity: 0,
                    zIndex: -1
                }, this.setting.fadeduration[1]);
                this.state.isvisible = false;
            }
        };
        this.init = function () {
            jQuery(document).ready(($) => {
                let mainobj = this;
                let iebrws = document.all;
                mainobj.cssfixedsupport = !iebrws || iebrws && document.compatMode === 'CSS1Compat' && window.XMLHttpRequest;
                //not IE or IE7+ browsers in standards mode
                mainobj.$body = (window.opera) ? (document.compatMode === 'CSS1Compat' ? $('html') : $('body')) : $('html,body');
                mainobj.$control = $('<div id="cres-topcontrol">' + mainobj.controlHTML + '</div>')
                    .css({
                        position: mainobj.cssfixedsupport ? 'fixed' : 'absolute',
                        bottom: mainobj.controlattrs.offsety,
                        right: mainobj.controlattrs.offsetx,
                        opacity: 0,
                        cursor: 'pointer',
                        zIndex: 99999
                    })
                    .attr({
                        title: 'Scroll Back to Top'
                    })
                    .click(function () {
                        mainobj.scrollup();
                        return false;
                    })
                    .appendTo('body');
                //loose check for IE6 and below, plus whether control contains any text
                if (document.all && !window.XMLHttpRequest && mainobj.$control.text() !== '') {
                    //IE6- seems to require an explicit width on a DIV containing text
                    mainobj.$control.css({
                        width: mainobj.$control.width()
                    });
                }
                mainobj.togglecontrol();
                $('a[href="' + mainobj.anchorkeyword + '"]').click(function () {
                    mainobj.scrollup();
                    return false;
                });
                $(window).bind('scroll resize', ()=> {
                    mainobj.togglecontrol();
                });
            });
        };
    }
    scrollup() {
        if (!this.cssfixedsupport) {
            //if control is positioned using JavaScript
            this.$control.css({
                opacity: 0,
                zIndex: -1
            });
        }
        //hide control immediately after clicking it
        let dest = isNaN(this.setting.scrollto) ? this.setting.scrollto : parseInt(this.setting.scrollto);
        if (typeof dest === 'string' && jQuery('#' + dest).length == 1) {
            //check element set by string exists
            dest = jQuery('#' + dest).offset().top;
        } else {
            dest = 0;
        }

        this.$body.animate({
            scrollTop: dest
        }, this.setting.scrollduration);
    }
}

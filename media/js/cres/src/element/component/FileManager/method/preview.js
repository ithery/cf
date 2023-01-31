export const previewMethod = (items) => {
    let carousel = $('#carouselTemplate').clone().attr('id', 'previewCarousel').removeClass('d-none');
    let imageTemplate = carousel.find('.carousel-item').clone().removeClass('active');
    let indicatorTemplate = carousel.find('.carousel-indicators > li').clone().removeClass('active');
    carousel.children('.carousel-inner').html('');
    carousel.children('.carousel-indicators').html('');
    carousel.children('.carousel-indicators,.carousel-control-prev,.carousel-control-next').toggle(items.length > 1);
    items.forEach(function (item, index) {
        let carouselItem = imageTemplate.clone()
            .addClass(index === 0 ? 'active' : '');
        if (item.thumb_url) {
            carouselItem.find('.carousel-image').css('background-image', 'url(\'' + item.url + '?timestamp=' + item.time + '\')');
        } else {
            carouselItem.find('.carousel-image').css('width', '50vh').append($('<div>').addClass('mime-icon ico-' + item.icon));
        }

        carouselItem.find('.carousel-label').attr('target', '_blank').attr('href', item.url)
            .append(item.name)
            .append($('<i class="fas fa-external-link-alt ml-2"></i>'));
        carousel.children('.carousel-inner').append(carouselItem);
        let carouselIndicator = indicatorTemplate.clone()
            .addClass(index === 0 ? 'active' : '')
            .attr('data-slide-to', index);
        carousel.children('.carousel-indicators').append(carouselIndicator);
    });
    // carousel swipe control
    let touchStartX = null;
    carousel.on('touchstart', function (event) {
        let e = event.originalEvent;
        if (e.touches.length == 1) {
            let touch = e.touches[0];
            touchStartX = touch.pageX;
        }
    }).on('touchmove', function (event) {
        let e = event.originalEvent;
        if (touchStartX != null) {
            let touchCurrentX = e.changedTouches[0].pageX;
            if ((touchCurrentX - touchStartX) > 60) {
                touchStartX = null;
                carousel.carousel('prev');
            } else if ((touchStartX - touchCurrentX) > 60) {
                touchStartX = null;
                carousel.carousel('next');
            }
        }
    }).on('touchend', function () {
        touchStartX = null;
    });
    // end carousel swipe control

    this.notify(carousel);
};

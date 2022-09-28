import { cresQuery } from "../../../../../module/CresQuery";
import { galleryEvent } from "../../event";
import { thumbnailConfig } from "./config";

export default class Thumbnail {
    constructor(gallery) {
        // get lightGallery core plugin instance
        this.gallery = gallery;
        this.config = {
            ...thumbnailConfig,
            ...this.gallery.config
        }
    }

    init() {
        this.thumbOuterWidth = 0;
        this.thumbTotalWidth =
            this.gallery.galleryItems.length *
            (this.config.thumbWidth + this.config.thumbMargin);

        // Thumbnail animation value
        this.translateX = 0;

        this.setAnimateThumbStyles();

        if (!this.gallery.config.allowMediaOverlap) {
            this.config.toggleThumb = false;
        }

        if (this.config.thumbnail) {
            this.build();
            if (this.config.animateThumb) {
                if (this.config.enableThumbDrag) {
                    this.enableThumbDrag();
                }

                if (this.config.enableThumbSwipe) {
                    this.enableThumbSwipe();
                }

                this.thumbClickable = false;
            } else {
                this.thumbClickable = true;
            }

            this.toggleThumbBar();
            this.thumbKeyPress();
        }
    }

    build() {
        this.setThumbMarkup();
        this.manageActiveClassOnSlideChange();
        this.$cresGalleryThumb.first().on('click.cres-gallery touchend.cres-gallery', (e) => {
            const $target = cresQuery(e.target);
            if (!$target.hasAttribute('data-cres-gallery-item-id')) {
                return;
            }
            setTimeout(() => {
                // In IE9 and bellow touch does not support
                // Go to slide if browser does not support css transitions
                if (this.thumbClickable && !this.gallery.cresGalleryBusy) {
                    const index = parseInt($target.attr('data-cres-gallery-item-id'));
                    this.gallery.slide(index, false, true, false);
                }
            }, 50);
        });

        this.gallery.cresEl.on(`${galleryEvent.beforeSlide}.thumb`, (event) => {
            const { index } = event.detail;
            this.animateThumb(index);
        });
        this.gallery.cresEl.on(`${galleryEvent.beforeOpen}.thumb`, () => {
            this.thumbOuterWidth = this.gallery.outer.get().offsetWidth;
        });

        this.gallery.cresEl.on(`${galleryEvent.updateSlides}.thumb`, () => {
            this.rebuildThumbnails();
        });
        this.gallery.cresEl.on(`${galleryEvent.containerResize}.thumb`, () => {
            if (!this.gallery.cresGalleryOpened) return;
            setTimeout(() => {
                this.thumbOuterWidth = this.gallery.outer.get().offsetWidth;
                this.animateThumb(this.gallery.index);
                this.thumbOuterWidth = this.gallery.outer.get().offsetWidth;
            }, 50);
        });
    }

    setThumbMarkup() {
        let thumbOuterClassNames = 'cres-gallery-thumb-outer ';

        if (this.config.alignThumbnails) {
            thumbOuterClassNames += `cres-gallery-thumb-align-${this.config.alignThumbnails}`;
        }

        const html = `<div class="${thumbOuterClassNames}">
        <div class="cres-gallery-thumb cres-gallery-group">
        </div>
        </div>`;

        this.gallery.outer.addClass('cres-gallery-has-thumb');

        if (this.config.appendThumbnailsTo === '.cres-gallery-components') {
            this.gallery.$cresGalleryComponents.append(html);
        } else {
            this.gallery.outer.append(html);
        }

        this.$thumbOuter = this.gallery.outer.find('.cres-gallery-thumb-outer').first();
        this.$cresGalleryThumb = this.gallery.outer.find('.cres-gallery-thumb').first();

        if (this.config.animateThumb) {
            this.gallery.outer
                .find('.cres-gallery-thumb')
                .css('transition-duration', this.gallery.config.speed + 'ms')
                .css('width', this.thumbTotalWidth + 'px')
                .css('position', 'relative');
        }

        this.setThumbItemHtml(this.gallery.galleryItems);
    }

    enableThumbDrag() {
        let thumbDragUtils = {
            cords: {
                startX: 0,
                endX: 0,
            },
            isMoved: false,
            newTranslateX: 0,
            startTime: new Date(),
            endTime: new Date(),
            touchMoveTime: 0,
        };

        let isDragging = false;

        this.$thumbOuter.addClass('cres-gallery-grab');

        this.gallery.outer
            .find('.cres-gallery-thumb')
            .first()
            .on('mousedown.cres-galley.thumb', (e) => {
                if (this.thumbTotalWidth > this.thumbOuterWidth) {
                    // execute only on .cres-gallery-object
                    e.preventDefault();
                    thumbDragUtils.cords.startX = e.pageX;

                    thumbDragUtils.startTime = new Date();
                    this.thumbClickable = false;

                    isDragging = true;

                    // ** Fix for webkit cursor issue https://code.google.com/p/chromium/issues/detail?id=26723
                    this.gallery.outer.get().scrollLeft += 1;
                    this.gallery.outer.get().scrollLeft -= 1;

                    // *
                    this.$thumbOuter
                        .removeClass('cres-gallery-grab')
                        .addClass('cres-gallery-grabbing');
                }
            });

        cresQuery(window).on(
            `mousemove.cres-gallery.thumb.global${this.gallery.cresGalleryId}`,
            (e) => {
                if (!this.gallery.cresGalleryOpened) return;
                if (isDragging) {
                    thumbDragUtils.cords.endX = e.pageX;

                    thumbDragUtils = this.onThumbTouchMove(thumbDragUtils);
                }
            },
        );

        cresQuery(window).on(`mouseup.cres-gallery.thumb.global${this.gallery.cresGalleryId}`, () => {
            if (!this.gallery.cresGalleryOpened) return;
            if (thumbDragUtils.isMoved) {
                thumbDragUtils = this.onThumbTouchEnd(thumbDragUtils);
            } else {
                this.thumbClickable = true;
            }

            if (isDragging) {
                isDragging = false;
                this.$thumbOuter.removeClass('cres-gallery-grabbing').addClass('cres-gallery-grab');
            }
        });
    }

    enableThumbSwipe() {
        let thumbDragUtils = {
            cords: {
                startX: 0,
                endX: 0,
            },
            isMoved: false,
            newTranslateX: 0,
            startTime: new Date(),
            endTime: new Date(),
            touchMoveTime: 0,
        };

        this.$cresGalleryThumb.on('touchstart.cres-gallery', (e) => {
            if (this.thumbTotalWidth > this.thumbOuterWidth) {
                e.preventDefault();
                thumbDragUtils.cords.startX = e.targetTouches[0].pageX;
                this.thumbClickable = false;
                thumbDragUtils.startTime = new Date();
            }
        });

        this.$cresGalleryThumb.on('touchmove.cres-gallery', (e) => {
            if (this.thumbTotalWidth > this.thumbOuterWidth) {
                e.preventDefault();
                thumbDragUtils.cords.endX = e.targetTouches[0].pageX;
                thumbDragUtils = this.onThumbTouchMove(thumbDragUtils);
            }
        });

        this.$cresGalleryThumb.on('touchend.cres-gallery', () => {
            if (thumbDragUtils.isMoved) {
                thumbDragUtils = this.onThumbTouchEnd(thumbDragUtils);
            } else {
                this.thumbClickable = true;
            }
        });
    }

    // Rebuild thumbnails
    rebuildThumbnails() {
        // Remove transitions
        this.$thumbOuter.addClass('cres-gallery-rebuilding-thumbnails');
        setTimeout(() => {
            this.thumbTotalWidth =
                this.gallery.galleryItems.length *
                (this.config.thumbWidth + this.config.thumbMargin);
            this.$cresGalleryThumb.css('width', this.thumbTotalWidth + 'px');
            this.$cresGalleryThumb.empty();
            this.setThumbItemHtml(this.gallery.galleryItems);
            this.animateThumb(this.gallery.index);
        }, 50);
        setTimeout(() => {
            this.$thumbOuter.removeClass('cres-gallery-rebuilding-thumbnails');
        }, 200);
    }

    // @ts-check

    setTranslate(value) {
        this.$cresGalleryThumb.css(
            'transform',
            'translate3d(-' + value + 'px, 0px, 0px)',
        );
    }

    getPossibleTransformX(left) {
        if (left > this.thumbTotalWidth - this.thumbOuterWidth) {
            left = this.thumbTotalWidth - this.thumbOuterWidth;
        }

        if (left < 0) {
            left = 0;
        }
        return left;
    }

    animateThumb(index) {
        this.$cresGalleryThumb.css(
            'transition-duration',
            this.gallery.config.speed + 'ms',
        );
        if (this.config.animateThumb) {
            let position = 0;
            switch (this.config.currentPagerPosition) {
                case 'left':
                    position = 0;
                    break;
                case 'middle':
                    position =
                        this.thumbOuterWidth / 2 - this.config.thumbWidth / 2;
                    break;
                case 'right':
                    position = this.thumbOuterWidth - this.config.thumbWidth;
            }
            this.translateX =
                (this.config.thumbWidth + this.config.thumbMargin) * index -
                1 -
                position;
            if (this.translateX > this.thumbTotalWidth - this.thumbOuterWidth) {
                this.translateX = this.thumbTotalWidth - this.thumbOuterWidth;
            }

            if (this.translateX < 0) {
                this.translateX = 0;
            }

            this.setTranslate(this.translateX);
        }
    }

    onThumbTouchMove(thumbDragUtils) {
        thumbDragUtils.newTranslateX = this.translateX;
        thumbDragUtils.isMoved = true;

        thumbDragUtils.touchMoveTime = new Date().valueOf();

        thumbDragUtils.newTranslateX -=
            thumbDragUtils.cords.endX - thumbDragUtils.cords.startX;

        thumbDragUtils.newTranslateX = this.getPossibleTransformX(
            thumbDragUtils.newTranslateX,
        );

        // move current slide
        this.setTranslate(thumbDragUtils.newTranslateX);
        this.$thumbOuter.addClass('cres-gallery-dragging');

        return thumbDragUtils;
    }

    onThumbTouchEnd(thumbDragUtils) {
        thumbDragUtils.isMoved = false;
        thumbDragUtils.endTime = new Date();
        this.$thumbOuter.removeClass('cres-gallery-dragging');

        const touchDuration =
            thumbDragUtils.endTime.valueOf() -
            thumbDragUtils.startTime.valueOf();
        let distanceXnew =
            thumbDragUtils.cords.endX - thumbDragUtils.cords.startX;
        let speedX = Math.abs(distanceXnew) / touchDuration;
        // Some magical numbers
        // Can be improved
        if (
            speedX > 0.15 &&
            thumbDragUtils.endTime.valueOf() - thumbDragUtils.touchMoveTime < 30
        ) {
            speedX += 1;

            if (speedX > 2) {
                speedX += 1;
            }
            speedX =
                speedX +
                speedX * (Math.abs(distanceXnew) / this.thumbOuterWidth);
            this.$cresGalleryThumb.css(
                'transition-duration',
                Math.min(speedX - 1, 2) + 'settings',
            );

            distanceXnew = distanceXnew * speedX;

            this.translateX = this.getPossibleTransformX(
                this.translateX - distanceXnew,
            );
            this.setTranslate(this.translateX);
        } else {
            this.translateX = thumbDragUtils.newTranslateX;
        }
        if (
            Math.abs(thumbDragUtils.cords.endX - thumbDragUtils.cords.startX) <
            this.config.thumbnailSwipeThreshold
        ) {
            this.thumbClickable = true;
        }

        return thumbDragUtils;
    }

    getThumbHtml(thumb, index) {
        const slideVideoInfo =
            this.gallery.galleryItems[index].__slideVideoInfo || {};
        let thumbImg;

        if (slideVideoInfo.youtube) {
            if (this.config.loadYouTubeThumbnail) {
                thumbImg =
                    '//img.youtube.com/vi/' +
                    slideVideoInfo.youtube[1] +
                    '/' +
                    this.config.youTubeThumbSize +
                    '.jpg';
            } else {
                thumbImg = thumb;
            }
        } else {
            thumbImg = thumb;
        }

        return `<div data-cres-gallery-item-id="${index}" class="cres-gallery-thumb-item ${
            index === this.gallery.index ? ' active' : ''
        }"
        style="width:${this.config.thumbWidth}px; height: ${
            this.config.thumbHeight
        };
            margin-right: ${this.config.thumbMargin}px;">
            <img data-cres-gallery-item-id="${index}" src="${thumbImg}" />
        </div>`;
    }

    getThumbItemHtml(items) {
        let thumbList = '';
        for (let i = 0; i < items.length; i++) {
            thumbList += this.getThumbHtml(items[i].thumb, i);
        }

        return thumbList;
    }

    setThumbItemHtml(items) {
        const thumbList = this.getThumbItemHtml(items);
        this.$cresGalleryThumb.html(thumbList);
    }

    setAnimateThumbStyles() {
        if (this.config.animateThumb) {
            this.gallery.outer.addClass('cres-gallery-animate-thumb');
        }
    }

    // Manage thumbnail active calss
    manageActiveClassOnSlideChange() {
        // manage active class for thumbnail
        this.gallery.cresEl.on(
            `${galleryEvent.beforeSlide}.thumb`,
            (event) => {
                const $thumb = this.gallery.outer.find('.cres-gallery-thumb-item');
                const { index } = event.detail;
                $thumb.removeClass('active');
                $thumb.eq(index).addClass('active');
            },
        );
    }

    // Toggle thumbnail bar
    toggleThumbBar() {
        if (this.config.toggleThumb) {
            this.gallery.outer.addClass('cres-gallery-can-toggle');
            this.gallery.$toolbar.append(
                '<button type="button" aria-label="' +
                    this.config.thumbnailPluginStrings['toggleThumbnails'] +
                    '" class="cres-gallery-toggle-thumb cres-gallery-icon"></button>',
            );
            this.gallery.outer
                .find('.cres-gallery-toggle-thumb')
                .first()
                .on('click.cres-gallery', () => {
                    this.gallery.outer.toggleClass('cres-gallery-components-open');
                });
        }
    }

    thumbKeyPress() {
        cresQuery(window).on(`keydown.cres-gallery.thumb.global${this.gallery.cresGalleryId}`, (e) => {
            if (!this.gallery.cresGalleryOpened || !this.config.toggleThumb) return;

            if (e.keyCode === 38) {
                e.preventDefault();
                this.gallery.outer.addClass('cres-gallery-components-open');
            } else if (e.keyCode === 40) {
                e.preventDefault();
                this.gallery.outer.removeClass('cres-gallery-components-open');
            }
        });
    }

    destroy() {
        if (this.config.thumbnail) {
            cresQuery(window).off(`.cres-gallery.thumb.global${this.gallery.cresGalleryId}`);
            this.gallery.cresEl.off('.cres-gallery.thumb');
            this.gallery.cresEl.off('.thumb');
            this.$thumbOuter.remove();
            this.gallery.outer.removeClass('cres-gallery-has-thumb');
        }
    }
}

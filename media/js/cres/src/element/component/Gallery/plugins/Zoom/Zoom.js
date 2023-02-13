import { cresQuery } from "../../../../../module/CresQuery";
import { galleryEvent } from "../../event";
import { zoomConfig } from "./config";
const ZOOM_TRANSITION_DURATION = 500;
export default class Zoom {
    /**
     * @param {Gallery} gallery - Gallery object
     */
    constructor(gallery) {
        // get lightGallery core plugin instance
        this.gallery = gallery;
        this.config = {
            ...zoomConfig,
            ...this.gallery.config
        }
    }

    // Append Zoom controls. Actual size, Zoom-in, Zoom-out
    buildTemplates() {
        let zoomIcons = this.config.showZoomInOutIcons
            ? `<button id="${this.gallery.getIdName(
                  'cres-gallery-zoom-in',
              )}" type="button" aria-label="${
                  this.config.zoomPluginStrings['zoomIn']
              }" class="cres-gallery-zoom-in cres-gallery-icon"></button><button id="${this.gallery.getIdName(
                  'cres-gallery-zoom-out',
              )}" type="button" aria-label="${
                  this.config.zoomPluginStrings['zoomIn']
              }" class="cres-gallery-zoom-out cres-gallery-icon"></button>`
            : '';

        if (this.config.actualSize) {
            zoomIcons += `<button id="${this.gallery.getIdName(
                'cres-gallery-actual-size',
            )}" type="button" aria-label="${
                this.config.zoomPluginStrings['viewActualSize']
            }" class="${
                this.config.actualSizeIcons.zoomIn
            } cres-gallery-icon"></button>`;
        }

        this.gallery.outer.addClass('cres-gallery-use-transition-for-zoom');

        this.gallery.$toolbar.first().append(zoomIcons);
    }

    /**
     * @desc Enable zoom option only once the image is completely loaded
     * If zoomFromOrigin is true, Zoom is enabled once the dummy image has been inserted
     *
     * Zoom styles are defined under cres-gallery-zoomable CSS class.
     */
     enableZoom(event) {
        // delay will be 0 except first time
        let _speed = this.config.enableZoomAfter + event.detail.delay;

        // set _speed value 0 if gallery opened from direct url and if it is first slide
        if (
            cresQuery('body').first().hasClass('cres-gallery-from-hash') &&
            event.detail.delay
        ) {
            // will execute only once
            _speed = 0;
        } else {
            // Remove cres-gallery-from-hash to enable starting animation.
            cresQuery('body').first().removeClass('cres-gallery-from-hash');
        }

        this.zoomableTimeout = setTimeout(() => {
            if (!this.isImageSlide(this.gallery.index)) {
                return;
            }
            this.gallery.getSlideItem(event.detail.index).addClass('cres-gallery-zoomable');
            if (event.detail.index === this.gallery.index) {
                this.setZoomEssentials();
            }
        }, _speed + 30);
    }

    enableZoomOnSlideItemLoad() {
        // Add zoomable class
        this.gallery.cresEl.on(
            `${galleryEvent.slideItemLoad}.zoom`,
            this.enableZoom.bind(this),
        );
    }

    getDragCords(e) {
        return {
            x: e.pageX,
            y: e.pageY,
        };
    }
    getSwipeCords(e) {
        const x = e.touches[0].pageX;
        const y = e.touches[0].pageY;
        return {
            x,
            y,
        };
    }

    getDragAllowedAxises(scale, scaleDiff) {
        const $image = this.gallery
            .getSlideItem(this.gallery.index)
            .find('.cres-gallery-image')
            .first()
            .get();

        let height = 0;
        let width = 0;
        const rect = $image.getBoundingClientRect();
        if (scale) {
            height = $image.offsetHeight * scale;
            width = $image.offsetWidth * scale;
        } else if (scaleDiff) {
            height = rect.height + scaleDiff * rect.height;
            width = rect.width + scaleDiff * rect.width;
        } else {
            height = rect.height;
            width = rect.width;
        }
        const allowY = height > this.containerRect.height;
        const allowX = width > this.containerRect.width;
        return {
            allowX,
            allowY,
        };
    }

    setZoomEssentials() {
        this.containerRect = this.gallery.$content.get().getBoundingClientRect();
    }

    /**
     * @desc Image zoom
     * Translate the wrap and scale the image to get better user experience
     *
     * @param {String} scale - Zoom decrement/increment value
     */
    zoomImage(
        scale,
        scaleDiff,
        reposition,
        resetToMax,
    ) {
        if (Math.abs(scaleDiff) <= 0) return;

        const offsetX = this.containerRect.width / 2 + this.containerRect.left;

        const offsetY =
            this.containerRect.height / 2 +
            this.containerRect.top +
            this.scrollTop;

        let originalX;
        let originalY;

        if (scale === 1) {
            this.positionChanged = false;
        }

        const dragAllowedAxises = this.getDragAllowedAxises(0, scaleDiff);

        const { allowY, allowX } = dragAllowedAxises;
        if (this.positionChanged) {
            originalX = this.left / (this.scale - scaleDiff);
            originalY = this.top / (this.scale - scaleDiff);
            this.pageX = offsetX - originalX;
            this.pageY = offsetY - originalY;

            this.positionChanged = false;
        }

        const possibleSwipeCords = this.getPossibleSwipeDragCords(scaleDiff);

        let x;
        let y;
        let _x = offsetX - this.pageX;
        let _y = offsetY - this.pageY;

        if (scale - scaleDiff > 1) {
            const scaleVal = (scale - scaleDiff) / Math.abs(scaleDiff);
            _x =
                (scaleDiff < 0 ? -_x : _x) +
                this.left * (scaleVal + (scaleDiff < 0 ? -1 : 1));
            _y =
                (scaleDiff < 0 ? -_y : _y) +
                this.top * (scaleVal + (scaleDiff < 0 ? -1 : 1));
            x = _x / scaleVal;
            y = _y / scaleVal;
        } else {
            const scaleVal = (scale - scaleDiff) * scaleDiff;
            x = _x * scaleVal;
            y = _y * scaleVal;
        }

        if (reposition) {
            if (allowX) {
                if (this.isBeyondPossibleLeft(x, possibleSwipeCords.minX)) {
                    x = possibleSwipeCords.minX;
                } else if (
                    this.isBeyondPossibleRight(x, possibleSwipeCords.maxX)
                ) {
                    x = possibleSwipeCords.maxX;
                }
            } else {
                if (scale > 1) {
                    if (x < possibleSwipeCords.minX) {
                        x = possibleSwipeCords.minX;
                    } else if (x > possibleSwipeCords.maxX) {
                        x = possibleSwipeCords.maxX;
                    }
                }
            }
            // @todo fix this
            if (allowY) {
                if (this.isBeyondPossibleTop(y, possibleSwipeCords.minY)) {
                    y = possibleSwipeCords.minY;
                } else if (
                    this.isBeyondPossibleBottom(y, possibleSwipeCords.maxY)
                ) {
                    y = possibleSwipeCords.maxY;
                }
            } else {
                // If the translate value based on index of beyond the viewport, utilize the available space to prevent image being cut out
                if (scale > 1) {
                    //If image goes beyond viewport top, use the minim possible translate value
                    if (y < possibleSwipeCords.minY) {
                        y = possibleSwipeCords.minY;
                    } else if (y > possibleSwipeCords.maxY) {
                        y = possibleSwipeCords.maxY;
                    }
                }
            }
        }

        this.setZoomStyles({
            x: x,
            y: y,
            scale,
        });

        this.left = x;
        this.top = y;

        if (resetToMax) {
            this.setZoomImageSize();
        }
    }

    resetImageTranslate(index) {
        if (!this.isImageSlide(index)) {
            return;
        }
        const $image = this.gallery.getSlideItem(index).find('.cres-gallery-image').first();
        this.imageReset = false;
        $image.removeClass(
            'reset-transition reset-transition-y reset-transition-x',
        );
        this.gallery.outer.removeClass('cres-gallery-actual-size');
        $image.css('width', 'auto').css('height', 'auto');
        setTimeout(() => {
            $image.removeClass('no-transition');
        }, 10);
    }

    setZoomImageSize() {
        const $image = this.gallery
            .getSlideItem(this.gallery.index)
            .find('.cres-gallery-image')
            .first();

        setTimeout(() => {
            const actualSizeScale = this.getCurrentImageActualSizeScale();

            if (this.scale >= actualSizeScale) {
                $image.addClass('no-transition');
                this.imageReset = true;
            }
        }, ZOOM_TRANSITION_DURATION);

        setTimeout(() => {
            const actualSizeScale = this.getCurrentImageActualSizeScale();

            if (this.scale >= actualSizeScale) {
                const dragAllowedAxises = this.getDragAllowedAxises(this.scale);

                $image
                    .css(
                        'width',
                        ($image.get()).naturalWidth + 'px',
                    )
                    .css(
                        'height',
                        ($image.get()).naturalHeight + 'px',
                    );

                this.gallery.outer.addClass('cres-gallery-actual-size');

                if (dragAllowedAxises.allowX && dragAllowedAxises.allowY) {
                    $image.addClass('reset-transition');
                } else if (
                    dragAllowedAxises.allowX &&
                    !dragAllowedAxises.allowY
                ) {
                    $image.addClass('reset-transition-x');
                } else if (
                    !dragAllowedAxises.allowX &&
                    dragAllowedAxises.allowY
                ) {
                    $image.addClass('reset-transition-y');
                }
            }
        }, ZOOM_TRANSITION_DURATION + 50);
    }

    /**
     * @desc apply scale3d to image and translate to image wrap
     * @param {style} X,Y and scale
     */
    setZoomStyles(style) {
        const $imageWrap = this.gallery
            .getSlideItem(this.gallery.index)
            .find('.cres-gallery-img-wrap')
            .first();
        const $image = this.gallery
            .getSlideItem(this.gallery.index)
            .find('.cres-gallery-image')
            .first();
        const $dummyImage = this.gallery.outer
            .find('.cres-gallery-current .cres-gallery-dummy-img')
            .first();
        this.scale = style.scale;
        $image.css(
            'transform',
            'scale3d(' + style.scale + ', ' + style.scale + ', 1)',
        );

        $dummyImage.css(
            'transform',
            'scale3d(' + style.scale + ', ' + style.scale + ', 1)',
        );

        const transform =
            'translate3d(' + style.x + 'px, ' + style.y + 'px, 0)';
        $imageWrap.css('transform', transform);
    }

    /**
     * @param index - Index of the current slide
     * @param event - event will be available only if the function is called on clicking/taping the imags
     */
    setActualSize(index, event) {
        const currentItem = this.gallery.galleryItems[this.gallery.index];
        this.resetImageTranslate(index);
        setTimeout(() => {
            // Allow zoom only on image
            if (
                !currentItem.src ||
                this.gallery.outer.hasClass('cres-gallery-first-slide-loading')
            ) {
                return;
            }
            const scale = this.getCurrentImageActualSizeScale();
            const prevScale = this.scale;
            if (this.gallery.outer.hasClass('cres-gallery-zoomed')) {
                this.scale = 1;
            } else {
                this.scale = this.getScale(scale);
            }
            this.setPageCords(event);

            this.beginZoom(this.scale);
            this.zoomImage(this.scale, this.scale - prevScale, true, true);

            setTimeout(() => {
                this.gallery.outer.removeClass('cres-gallery-grabbing').addClass('cres-gallery-grab');
            }, 10);
        }, 50);
    }

    getNaturalWidth(index) {
        const $image = this.gallery.getSlideItem(index).find('.cres-gallery-image').first();

        const naturalWidth = this.gallery.galleryItems[index].width;
        return naturalWidth
            ? parseFloat(naturalWidth)
            : undefined || ($image.get()).naturalWidth;
    }

    getActualSizeScale(naturalWidth, width) {
        let _scale;
        let scale;
        if (naturalWidth >= width) {
            _scale = naturalWidth / width;
            scale = _scale || 2;
        } else {
            scale = 1;
        }
        return scale;
    }

    getCurrentImageActualSizeScale() {
        const $image = this.gallery
            .getSlideItem(this.gallery.index)
            .find('.cres-gallery-image')
            .first();
        const width = $image.get().offsetWidth;
        const naturalWidth = this.getNaturalWidth(this.gallery.index) || width;
        return this.getActualSizeScale(naturalWidth, width);
    }

    getPageCords(event) {
        const cords = {};
        if (event) {
            cords.x = event.pageX || event.touches[0].pageX;
            cords.y = event.pageY || event.touches[0].pageY;
        } else {
            const containerRect = this.gallery.$content
                .get()
                .getBoundingClientRect();
            cords.x = containerRect.width / 2 + containerRect.left;
            cords.y =
                containerRect.height / 2 + this.scrollTop + containerRect.top;
        }
        return cords;
    }

    setPageCords(event) {
        const pageCords = this.getPageCords(event);

        this.pageX = pageCords.x;
        this.pageY = pageCords.y;
    }

    manageActualPixelClassNames() {
        const $actualSize = this.gallery.getElementById('cres-gallery-actual-size');
        $actualSize
            .removeClass(this.config.actualSizeIcons.zoomIn)
            .addClass(this.config.actualSizeIcons.zoomOut);
    }

    // If true, zoomed - in else zoomed out
    beginZoom(scale) {
        this.gallery.outer.removeClass('cres-gallery-zoom-drag-transition cres-gallery-zoom-dragging');
        if (scale > 1) {
            this.gallery.outer.addClass('cres-gallery-zoomed');
            this.manageActualPixelClassNames();
        } else {
            this.resetZoom();
        }
        return scale > 1;
    }

    getScale(scale) {
        const actualSizeScale = this.getCurrentImageActualSizeScale();
        if (scale < 1) {
            scale = 1;
        } else if (scale > actualSizeScale) {
            scale = actualSizeScale;
        }
        return scale;
    }

    init() {
        if (!this.config.zoom) {
            return;
        }
        this.buildTemplates();
        this.enableZoomOnSlideItemLoad();

        let tapped = null;

        this.gallery.outer.on('dblclick.cres-gallery', (event) => {
            if (!cresQuery(event.target).hasClass('cres-gallery-image')) {
                return;
            }
            this.setActualSize(this.gallery.index, event);
        });

        this.gallery.outer.on('touchstart.cres-gallery', (event) => {
            const $target = cresQuery(event.target);
            if (event.touches.length === 1 && $target.hasClass('cres-gallery-image')) {
                if (!tapped) {
                    tapped = setTimeout(() => {
                        tapped = null;
                    }, 300);
                } else {
                    clearTimeout(tapped);
                    tapped = null;
                    event.preventDefault();
                    this.setActualSize(this.gallery.index, event);
                }
            }
        });

        this.gallery.cresEl.on(
            `${galleryEvent.containerResize}.zoom ${galleryEvent.rotateRight}.zoom ${galleryEvent.rotateLeft}.zoom ${galleryEvent.flipHorizontal}.zoom ${galleryEvent.flipVertical}.zoom`,
            () => {
                if (
                    !this.gallery.cresGalleryOpened ||
                    !this.isImageSlide(this.gallery.index) ||
                    this.gallery.touchAction
                ) {
                    return;
                }
                const _cresEl = this.gallery
                    .getSlideItem(this.gallery.index)
                    .find('.cres-gallery-img-wrap')
                    .first();
                this.top = 0;
                this.left = 0;
                this.setZoomEssentials();
                this.setZoomSwipeStyles(_cresEl, { x: 0, y: 0 });
                this.positionChanged = true;
            },
        );
        // Update zoom on resize and orientationchange
        cresQuery(window).on(`scroll.cres-gallery.zoom.global${this.gallery.cresGalleryId}`, () => {
            if (!this.gallery.cresGalleryOpened) return;
            this.scrollTop = cresQuery(window).scrollTop();
        });

        this.gallery.getElementById('cres-gallery-zoom-out').on('click.cres-gallery', () => {
            // Allow zoom only on image
            if (!this.isImageSlide(this.gallery.index)) {
                return;
            }

            let timeout = 0;
            if (this.imageReset) {
                this.resetImageTranslate(this.gallery.index);
                timeout = 50;
            }
            setTimeout(() => {
                let scale = this.scale - this.config.scale;

                if (scale < 1) {
                    scale = 1;
                }
                this.beginZoom(scale);
                this.zoomImage(scale, -this.config.scale, true, true);
            }, timeout);
        });

        this.gallery.getElementById('cres-gallery-zoom-in').on('click.cres-gallery', () => {
            this.zoomIn();
        });

        this.gallery.getElementById('cres-gallery-actual-size').on('click.cres-gallery', () => {
            this.setActualSize(this.gallery.index);
        });

        this.gallery.cresEl.on(`${galleryEvent.beforeOpen}.zoom`, () => {
            this.gallery.outer.find('.cres-gallery-item').removeClass('cres-gallery-zoomable');
        });
        this.gallery.cresEl.on(`${galleryEvent.afterOpen}.zoom`, () => {
            this.scrollTop = cresQuery(window).scrollTop();

            // Set the initial value center
            this.pageX = this.gallery.outer.width() / 2;
            this.pageY = this.gallery.outer.height() / 2 + this.scrollTop;

            this.scale = 1;
        });

        // Reset zoom on slide change
        this.gallery.cresEl.on(
            `${galleryEvent.afterSlide}.zoom`,
            (event) => {
                const { prevIndex } = event.detail;
                this.scale = 1;
                this.positionChanged = false;
                this.resetZoom(prevIndex);
                this.resetImageTranslate(prevIndex);
                if (this.isImageSlide(this.gallery.index)) {
                    this.setZoomEssentials();
                }
            },
        );

        // Drag option after zoom
        this.zoomDrag();

        this.pinchZoom();

        this.zoomSwipe();

        // Store the zoomable timeout value just to clear it while closing
        this.zoomableTimeout = false;
        this.positionChanged = false;
    }

    zoomIn() {
        // Allow zoom only on image
        if (!this.isImageSlide(this.gallery.index)) {
            return;
        }

        let scale = this.scale + this.config.scale;

        scale = this.getScale(scale);
        this.beginZoom(scale);
        this.zoomImage(scale, this.config.scale, true, true);
    }

    // Reset zoom effect
    resetZoom(index) {
        this.gallery.outer.removeClass('cres-gallery-zoomed cres-gallery-zoom-drag-transition');
        const $actualSize = this.gallery.getElementById('cres-gallery-actual-size');
        const $item = this.gallery.getSlideItem(
            index !== undefined ? index : this.gallery.index,
        );
        $actualSize
            .removeClass(this.config.actualSizeIcons.zoomOut)
            .addClass(this.config.actualSizeIcons.zoomIn);
        $item.find('.cres-gallery-img-wrap').first().removeAttr('style');
        $item.find('.cres-gallery-image').first().removeAttr('style');
        this.scale = 1;
        this.left = 0;
        this.top = 0;

        // Reset pagx pagy values to center
        this.setPageCords();
    }

    getTouchDistance(e) {
        return Math.sqrt(
            (e.touches[0].pageX - e.touches[1].pageX) *
                (e.touches[0].pageX - e.touches[1].pageX) +
                (e.touches[0].pageY - e.touches[1].pageY) *
                    (e.touches[0].pageY - e.touches[1].pageY),
        );
    }

    pinchZoom() {
        let startDist = 0;
        let pinchStarted = false;
        let initScale = 1;
        let prevScale = 0;

        let $item = this.gallery.getSlideItem(this.gallery.index);

        this.gallery.outer.on('touchstart.cres-gallery', (e) => {
            $item = this.gallery.getSlideItem(this.gallery.index);
            if (!this.isImageSlide(this.gallery.index)) {
                return;
            }
            if (e.touches.length === 2) {
                e.preventDefault();
                if (this.gallery.outer.hasClass('cres-gallery-first-slide-loading')) {
                    return;
                }
                initScale = this.scale || 1;
                this.gallery.outer.removeClass(
                    'cres-gallery-zoom-drag-transition cres-gallery-zoom-dragging',
                );

                this.setPageCords(e);
                this.resetImageTranslate(this.gallery.index);

                this.gallery.touchAction = 'pinch';

                startDist = this.getTouchDistance(e);
            }
        });

        this.gallery.$inner.on('touchmove.cres-gallery', (e) => {
            if (
                e.touches.length === 2 &&
                this.gallery.touchAction === 'pinch' &&
                (cresQuery(e.target).hasClass('cres-gallery-item') ||
                    $item.get().contains(e.target))
            ) {
                e.preventDefault();
                const endDist = this.getTouchDistance(e);

                const distance = startDist - endDist;
                if (!pinchStarted && Math.abs(distance) > 5) {
                    pinchStarted = true;
                }
                if (pinchStarted) {
                    prevScale = this.scale;
                    const _scale = Math.max(1, initScale + -distance * 0.02);
                    this.scale =
                        Math.round((_scale + Number.EPSILON) * 100) / 100;
                    const diff = this.scale - prevScale;
                    this.zoomImage(
                        this.scale,
                        Math.round((diff + Number.EPSILON) * 100) / 100,
                        false,
                        false,
                    );
                }
            }
        });

        this.gallery.$inner.on('touchend.cres-gallery', (e) => {
            if (
                this.gallery.touchAction === 'pinch' &&
                (cresQuery(e.target).hasClass('cres-gallery-item') ||
                    $item.get().contains(e.target))
            ) {
                pinchStarted = false;
                startDist = 0;
                if (this.scale <= 1) {
                    this.resetZoom();
                } else {
                    const actualSizeScale = this.getCurrentImageActualSizeScale();

                    if (this.scale >= actualSizeScale) {
                        let scaleDiff = actualSizeScale - this.scale;
                        if (scaleDiff === 0) {
                            scaleDiff = 0.01;
                        }
                        this.zoomImage(actualSizeScale, scaleDiff, false, true);
                    }
                    this.manageActualPixelClassNames();

                    this.gallery.outer.addClass('cres-gallery-zoomed');
                }
                this.gallery.touchAction = undefined;
            }
        });
    }

    touchendZoom(
        startCoords,
        endCoords,
        allowX,
        allowY,
        touchDuration,
    ) {
        let distanceXnew = endCoords.x - startCoords.x;
        let distanceYnew = endCoords.y - startCoords.y;

        let speedX = Math.abs(distanceXnew) / touchDuration + 1;
        let speedY = Math.abs(distanceYnew) / touchDuration + 1;

        if (speedX > 2) {
            speedX += 1;
        }

        if (speedY > 2) {
            speedY += 1;
        }

        distanceXnew = distanceXnew * speedX;
        distanceYnew = distanceYnew * speedY;

        const _cresEl = this.gallery
            .getSlideItem(this.gallery.index)
            .find('.cres-gallery-img-wrap')
            .first();
        const distance = {};

        distance.x = this.left + distanceXnew;
        distance.y = this.top + distanceYnew;

        const possibleSwipeCords = this.getPossibleSwipeDragCords();

        if (Math.abs(distanceXnew) > 15 || Math.abs(distanceYnew) > 15) {
            if (allowY) {
                if (
                    this.isBeyondPossibleTop(
                        distance.y,
                        possibleSwipeCords.minY,
                    )
                ) {
                    distance.y = possibleSwipeCords.minY;
                } else if (
                    this.isBeyondPossibleBottom(
                        distance.y,
                        possibleSwipeCords.maxY,
                    )
                ) {
                    distance.y = possibleSwipeCords.maxY;
                }
            }

            if (allowX) {
                if (
                    this.isBeyondPossibleLeft(
                        distance.x,
                        possibleSwipeCords.minX,
                    )
                ) {
                    distance.x = possibleSwipeCords.minX;
                } else if (
                    this.isBeyondPossibleRight(
                        distance.x,
                        possibleSwipeCords.maxX,
                    )
                ) {
                    distance.x = possibleSwipeCords.maxX;
                }
            }

            if (allowY) {
                this.top = distance.y;
            } else {
                distance.y = this.top;
            }

            if (allowX) {
                this.left = distance.x;
            } else {
                distance.x = this.left;
            }

            this.setZoomSwipeStyles(_cresEl, distance);

            this.positionChanged = true;
        }
    }

    getZoomSwipeCords(
        startCoords,
        endCoords,
        allowX,
        allowY,
        possibleSwipeCords,
    ) {
        const distance = {} ;
        if (allowY) {
            distance.y = this.top + (endCoords.y - startCoords.y);
            if (this.isBeyondPossibleTop(distance.y, possibleSwipeCords.minY)) {
                const diffMinY = possibleSwipeCords.minY - distance.y;
                distance.y = possibleSwipeCords.minY - diffMinY / 6;
            } else if (
                this.isBeyondPossibleBottom(distance.y, possibleSwipeCords.maxY)
            ) {
                const diffMaxY = distance.y - possibleSwipeCords.maxY;
                distance.y = possibleSwipeCords.maxY + diffMaxY / 6;
            }
        } else {
            distance.y = this.top;
        }

        if (allowX) {
            distance.x = this.left + (endCoords.x - startCoords.x);
            if (
                this.isBeyondPossibleLeft(distance.x, possibleSwipeCords.minX)
            ) {
                const diffMinX = possibleSwipeCords.minX - distance.x;
                distance.x = possibleSwipeCords.minX - diffMinX / 6;
            } else if (
                this.isBeyondPossibleRight(distance.x, possibleSwipeCords.maxX)
            ) {
                const difMaxX = distance.x - possibleSwipeCords.maxX;
                distance.x = possibleSwipeCords.maxX + difMaxX / 6;
            }
        } else {
            distance.x = this.left;
        }

        return distance;
    }

    isBeyondPossibleLeft(x, minX) {
        return x >= minX;
    }
    isBeyondPossibleRight(x, maxX) {
        return x <= maxX;
    }
    isBeyondPossibleTop(y, minY) {
        return y >= minY;
    }
    isBeyondPossibleBottom(y, maxY) {
        return y <= maxY;
    }

    isImageSlide(index) {
        const currentItem = this.gallery.galleryItems[index];
        return this.gallery.getSlideType(currentItem) === 'image';
    }

    getPossibleSwipeDragCords(scale) {
        const $image = this.gallery
            .getSlideItem(this.gallery.index)
            .find('.cres-gallery-image')
            .first();

        const { bottom } = this.gallery.mediaContainerPosition;

        const imgRect = $image.get().getBoundingClientRect();

        let imageHeight = imgRect.height;
        let imageWidth = imgRect.width;

        if (scale) {
            imageHeight = imageHeight + scale * imageHeight;
            imageWidth = imageWidth + scale * imageWidth;
        }

        const minY = (imageHeight - this.containerRect.height) / 2;
        const maxY = (this.containerRect.height - imageHeight) / 2 + bottom;

        const minX = (imageWidth - this.containerRect.width) / 2;

        const maxX = (this.containerRect.width - imageWidth) / 2;

        const possibleSwipeCords = {
            minY: minY,
            maxY: maxY,
            minX: minX,
            maxX: maxX,
        };
        return possibleSwipeCords;
    }

    setZoomSwipeStyles(
        LGel,
        distance,
    ) {
        LGel.css(
            'transform',
            'translate3d(' + distance.x + 'px, ' + distance.y + 'px, 0)',
        );
    }

    zoomSwipe() {
        let startCoords = {};
        let endCoords = {};
        let isMoved = false;

        // Allow x direction drag
        let allowX = false;

        // Allow Y direction drag
        let allowY = false;

        let startTime = new Date();
        let endTime = new Date();
        let possibleSwipeCords;

        let _cresEl;

        let $item = this.gallery.getSlideItem(this.gallery.index);

        this.gallery.$inner.on('touchstart.cres-gallery', (e) => {
            // Allow zoom only on image
            if (!this.isImageSlide(this.gallery.index)) {
                return;
            }
            $item = this.gallery.getSlideItem(this.gallery.index);
            if (
                (cresQuery(e.target).hasClass('cres-gallery-item') ||
                    $item.get().contains(e.target)) &&
                e.touches.length === 1 &&
                this.gallery.outer.hasClass('cres-gallery-zoomed')
            ) {
                e.preventDefault();
                startTime = new Date();
                this.gallery.touchAction = 'zoomSwipe';
                _cresEl = this.gallery
                    .getSlideItem(this.gallery.index)
                    .find('.cres-gallery-img-wrap')
                    .first();

                const dragAllowedAxises = this.getDragAllowedAxises(0);

                allowY = dragAllowedAxises.allowY;
                allowX = dragAllowedAxises.allowX;
                if (allowX || allowY) {
                    startCoords = this.getSwipeCords(e);
                }

                possibleSwipeCords = this.getPossibleSwipeDragCords();

                // reset opacity and transition duration
                this.gallery.outer.addClass(
                    'cres-gallery-zoom-dragging cres-gallery-zoom-drag-transition',
                );
            }
        });

        this.gallery.$inner.on('touchmove.cres-gallery', (e) => {
            if (
                e.touches.length === 1 &&
                this.gallery.touchAction === 'zoomSwipe' &&
                (cresQuery(e.target).hasClass('cres-gallery-item') ||
                    $item.get().contains(e.target))
            ) {
                e.preventDefault();
                this.gallery.touchAction = 'zoomSwipe';

                endCoords = this.getSwipeCords(e);

                const distance = this.getZoomSwipeCords(
                    startCoords,
                    endCoords,
                    allowX,
                    allowY,
                    possibleSwipeCords,
                );

                if (
                    Math.abs(endCoords.x - startCoords.x) > 15 ||
                    Math.abs(endCoords.y - startCoords.y) > 15
                ) {
                    isMoved = true;
                    this.setZoomSwipeStyles(_cresEl, distance);
                }
            }
        });

        this.gallery.$inner.on('touchend.cres-gallery', (e) => {
            if (
                this.gallery.touchAction === 'zoomSwipe' &&
                (cresQuery(e.target).hasClass('cres-gallery-item') ||
                    $item.get().contains(e.target))
            ) {
                e.preventDefault();
                this.gallery.touchAction = undefined;
                this.gallery.outer.removeClass('cres-gallery-zoom-dragging');
                if (!isMoved) {
                    return;
                }
                isMoved = false;
                endTime = new Date();
                const touchDuration = endTime.valueOf() - startTime.valueOf();
                this.touchendZoom(
                    startCoords,
                    endCoords,
                    allowX,
                    allowY,
                    touchDuration,
                );
            }
        });
    }

    zoomDrag() {
        let startCoords = {};
        let endCoords = {};
        let isDragging = false;
        let isMoved = false;

        // Allow x direction drag
        let allowX = false;

        // Allow Y direction drag
        let allowY = false;

        let startTime;
        let endTime;

        let possibleSwipeCords;

        let _cresEl;

        this.gallery.outer.on('mousedown.cres-gallery.zoom', (e) => {
            // Allow zoom only on image
            if (!this.isImageSlide(this.gallery.index)) {
                return;
            }
            const $item = this.gallery.getSlideItem(this.gallery.index);
            if (
                cresQuery(e.target).hasClass('cres-gallery-item') ||
                $item.get().contains(e.target)
            ) {
                startTime = new Date();
                _cresEl = this.gallery
                    .getSlideItem(this.gallery.index)
                    .find('.cres-gallery-img-wrap')
                    .first();

                const dragAllowedAxises = this.getDragAllowedAxises(0);

                allowY = dragAllowedAxises.allowY;
                allowX = dragAllowedAxises.allowX;

                if (this.gallery.outer.hasClass('cres-gallery-zoomed')) {
                    if (
                        cresQuery(e.target).hasClass('cres-gallery-object') &&
                        (allowX || allowY)
                    ) {
                        e.preventDefault();
                        startCoords = this.getDragCords(e);

                        possibleSwipeCords = this.getPossibleSwipeDragCords();

                        isDragging = true;

                        this.gallery.outer
                            .removeClass('cres-gallery-grab')
                            .addClass(
                                'cres-gallery-grabbing cres-gallery-zoom-drag-transition cres-gallery-zoom-dragging',
                            );
                        // reset opacity and transition duration
                    }
                }
            }
        });

        cresQuery(window).on(
            `mousemove.cres-gallery.zoom.global${this.gallery.cresGalleryId}`,
            (e) => {
                if (isDragging) {
                    isMoved = true;
                    endCoords = this.getDragCords(e);

                    const distance = this.getZoomSwipeCords(
                        startCoords,
                        endCoords,
                        allowX,
                        allowY,
                        possibleSwipeCords,
                    );

                    this.setZoomSwipeStyles(_cresEl, distance);
                }
            },
        );

        cresQuery(window).on(`mouseup.cres-gallery.zoom.global${this.gallery.cresGalleryId}`, (e) => {
            if (isDragging) {
                endTime = new Date();
                isDragging = false;
                this.gallery.outer.removeClass('cres-gallery-zoom-dragging');

                // Fix for chrome mouse move on click
                if (
                    isMoved &&
                    (startCoords.x !== endCoords.x ||
                        startCoords.y !== endCoords.y)
                ) {
                    endCoords = this.getDragCords(e);

                    const touchDuration =
                        endTime.valueOf() - startTime.valueOf();
                    this.touchendZoom(
                        startCoords,
                        endCoords,
                        allowX,
                        allowY,
                        touchDuration,
                    );
                }

                isMoved = false;
            }

            this.gallery.outer.removeClass('cres-gallery-grabbing').addClass('cres-gallery-grab');
        });
    }

    closeGallery() {
        this.resetZoom();
    }

    destroy() {
        // Unbind all events added by lightGallery zoom plugin
        cresQuery(window).off(`.cres-gallery.zoom.global${this.gallery.cresGalleryId}`);
        this.gallery.cresEl.off('.cres-gallery.zoom');
        this.gallery.cresEl.off('.zoom');
        clearTimeout(this.zoomableTimeout);
        this.zoomableTimeout = false;
    }
}

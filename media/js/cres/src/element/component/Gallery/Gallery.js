import { CresQuery, cresQuery } from '../../../module/CresQuery';
import { galleryConfig } from './config';
import { galleryEvent } from './event';
import { Thumbnail } from './plugins';
import utils from './utils';

/**
 * @typedef {Object} ImageSize
 * @property {number} width
 * @property {number} height
 */

/**
 * @typedef {Object} ImageSources
 * @property {string} media
 * @property {string} srcset
 * @property {string} sizes
 * @property {string} type
 */

/**
 * @typedef {Object} GalleryItem
 * @property {string} src - Url of the media
 * @property {ImageSources[]} sources - Source attributes
 */

let cresGalleryId = 0;
export default class Gallery {
    constructor(className, config = {}) {
        window.mygallery = this;

        cresGalleryId++;
        this.cresGalleryId = cresGalleryId;
        // all html elements
        this.elements =
            className instanceof Element
                ? [className]
                : [].slice.call(document.querySelectorAll(className));
        if (this.elements.length < 1) {
            return;
        }
        this.element = this.elements[0];
        const cresConfig = JSON.parse(this.element.getAttribute('cres-config'));
        this.generateConfig({ ...config, ...cresConfig });
        this.plugins = [];
        this.buildPlugins();
        this.cresEl = cresQuery(this.element);
        // When using dynamic mode, ensure dynamicEl is an array
        if (
            this.config.dynamic &&
            this.config.dynamicEl !== undefined &&
            !Array.isArray(this.config.dynamicEl)
        ) {
            throw 'When using dynamic mode, you must also define dynamicEl as an Array.';
        }

        this.galleryItems = this.getItems();
        this.normalizeConfig();
        this.init();
    }
    init() {
        this.addSlideVideoInfo(this.galleryItems);
        this.buildStructure();
        this.cresEl.trigger(galleryEvent.init, {
            instance: this
        });
        if (this.config.keyPress) {
            this.keyPress();
        }

        setTimeout(() => {
            this.enableDrag();
            this.enableSwipe();
            this.triggerPosterClick();
        }, 50);

        this.arrow();
        if (this.config.mousewheel) {
            this.mousewheel();
        }

        if (!this.config.dynamic) {
            this.openGalleryOnItemClick();
        }
    }


    openGalleryOnItemClick() {
        // Using for loop instead of using bubbling as the items can be any html element.
        for (let index = 0; index < this.items.length; index++) {
            const element = this.items[index];
            const $element = cresQuery(element);
            // Using different namespace for click because click event should not unbind if selector is same object('this')
            // @todo manage all event listners - should have namespace that represent element
            const uuid = CresQuery.generateUUID();
            $element
                .attr('data-cres-gallery-id', uuid)
                .on(`click.cres-gallerycustom-item-${uuid}`, (e) => {
                    e.preventDefault();
                    const currentItemIndex = this.config.index || index;
                    this.openGallery(currentItemIndex, element);
                });
        }
    }

    buildPlugins() {
        this.plugins.push(new Thumbnail(this));

        this.config.plugins.forEach((plugin) => {
            this.plugins.push(new plugin(this));
        });
    }
    generateConfig(options) {
        // cresGallery settings
        this.config = {
            ...galleryConfig,
            ...options
        };
        if (
            this.config.isMobile && typeof this.config.isMobile === 'function'
                ? this.config.isMobile()
                : utils.isMobile()
        ) {
            const mobileSettings = this.config.mobileSettings;
            this.config = { ...this.config, ...mobileSettings };
        }
    }
    normalizeConfig() {
        if (this.config.slideEndAnimation) {
            this.config.hideControlOnEnd = false;
        }
        if (!this.config.closable) {
            this.config.swipeToClose = false;
        }

        // And reset it on close to get the correct value next time
        this.zoomFromOrigin = this.config.zoomFromOrigin;

        // At the moment, Zoom from image doesn't support dynamic options
        // @todo add zoomFromOrigin support for dynamic images
        if (this.config.dynamic) {
            this.zoomFromOrigin = false;
        }

        if (!this.config.container) {
            this.config.container = document.body;
        }

        // settings.preload should not be grater than $item.length
        this.config.preload = Math.min(
            this.config.preload,
            this.galleryItems.length
        );
    }

    // Get gallery items based on multiple conditions
    /**
     *
     * @returns {GalleryItem[]}
     */
    getItems() {
        // Gallery items
        this.items = [];
        if (!this.config.dynamic) {
            if (this.config.selector === 'this') {
                this.items.push(this.el);
            } else if (this.config.selector) {
                if (typeof this.config.selector === 'string') {
                    if (this.config.selectWithin) {
                        const selectWithin = cresQuery(
                            this.config.selectWithin
                        );
                        this.items = selectWithin
                            .find(this.config.selector)
                            .get();
                    } else {
                        this.items = this.el.querySelectorAll(
                            this.config.selector
                        );
                    }
                } else {
                    this.items = this.config.selector;
                }
            } else {
                this.items = this.element.children;
            }
            return utils.getDynamicOptions(
                this.items,
                this.config.extraProps,
                this.config.getCaptionFromTitleOrAlt,
                this.config.exThumbImage
            );
        } else {
            return this.config.dynamicEl || [];
        }
    }

    buildStructure() {
        const container = this.$container && this.$container.get();
        if (container) {
            return;
        }
        let controls = '';
        let subHtmlCont = '';

        // Create controls
        if (this.config.controls) {
            controls = `<button type="button" id="${this.getIdName(
                'cres-gallery-prev'
            )}" aria-label="${
                this.config.strings['previousSlide']
            }" class="cres-gallery-prev cres-gallery-icon"> ${
                this.config.prevHtml
            } </button>
                <button type="button" id="${this.getIdName(
                    'cres-gallery-next'
                )}" aria-label="${
                this.config.strings['nextSlide']
            }" class="cres-gallery-next cres-gallery-icon"> ${
                this.config.nextHtml
            } </button>`;
        }

        if (this.config.appendSubHtmlTo !== '.cres-gallery-item') {
            subHtmlCont =
                '<div class="cres-gallery-sub-html" role="status" aria-live="polite"></div>';
        }

        let addClasses = '';

        if (this.config.allowMediaOverlap) {
            // Do not remove space before last single quote
            addClasses += 'cres-gallery-media-overlap ';
        }

        const ariaLabelledby = this.config.ariaLabelledby
            ? 'aria-labelledby="' + this.config.ariaLabelledby + '"'
            : '';
        const ariaDescribedby = this.config.ariaDescribedby
            ? 'aria-describedby="' + this.config.ariaDescribedby + '"'
            : '';

        const containerClassName = `cres-gallery-container ${
            this.config.addClass
        } ${
            document.body !== this.config.container ? 'cres-gallery-inline' : ''
        }`;
        const closeIcon =
            this.config.closable && this.config.showCloseIcon
                ? `<button type="button" aria-label="${
                      this.config.strings['closeGallery']
                  }" id="${this.getIdName(
                      'cres-gallery-close'
                  )}" class="cres-gallery-close cres-gallery-icon"></button>`
                : '';
        const maximizeIcon = this.config.showMaximizeIcon
            ? `<button type="button" aria-label="${
                  this.config.strings['toggleMaximize']
              }" id="${this.getIdName(
                  'cres-gallery-maximize'
              )}" class="cres-gallery-maximize cres-gallery-icon"></button>`
            : '';
        const template = `
        <div class="${containerClassName}" id="${this.getIdName(
            'cres-gallery-container'
        )}" tabindex="-1" aria-modal="true" ${ariaLabelledby} ${ariaDescribedby} role="dialog"
        >
            <div id="${this.getIdName(
                'cres-gallery-backdrop'
            )}" class="cres-gallery-backdrop"></div>
            <div id="${this.getIdName(
                'cres-gallery-outer'
            )}" class="cres-gallery-outer cres-gallery-use-css3 cres-gallery-css3 cres-gallery-hide-items ${addClasses} ">
              <div id="${this.getIdName(
                  'cres-gallery-content'
              )}" class="cres-gallery-content">
                <div id="${this.getIdName(
                    'cres-gallery-inner'
                )}" class="cres-gallery-inner">
                </div>
                ${controls}
              </div>
                <div id="${this.getIdName(
                    'cres-gallery-toolbar'
                )}" class="cres-gallery-toolbar cres-gallery-group">
                    ${maximizeIcon}
                    ${closeIcon}
                    </div>
                    ${
                        this.config.appendSubHtmlTo === '.cres-gallery-outer'
                            ? subHtmlCont
                            : ''
                    }
                <div id="${this.getIdName(
                    'cres-gallery-components'
                )}" class="cres-gallery-components">
                    ${
                        this.config.appendSubHtmlTo === '.cres-gallery-sub-html'
                            ? subHtmlCont
                            : ''
                    }
                </div>
            </div>
        </div>
        `;

        cresQuery(this.config.container).append(template);

        if (document.body !== this.config.container) {
            cresQuery(this.config.container).css('position', 'relative');
        }

        this.outer = this.getElementById('cres-gallery-outer');
        this.$cresGalleryComponents = this.getElementById('cres-gallery-components');
        this.$backdrop = this.getElementById('cres-gallery-backdrop');
        this.$container = this.getElementById('cres-gallery-container');
        this.$inner = this.getElementById('cres-gallery-inner');
        this.$content = this.getElementById('cres-gallery-content');
        this.$toolbar = this.getElementById('cres-gallery-toolbar');

        this.$backdrop.css(
            'transition-duration',
            this.config.backdropDuration + 'ms'
        );

        let outerClassNames = `${this.config.mode} `;

        this.manageSingleSlideClassName();

        if (this.config.enableDrag) {
            outerClassNames += 'cres-gallery-grab ';
        }

        this.outer.addClass(outerClassNames);

        this.$inner.css('transition-timing-function', this.config.easing);
        this.$inner.css('transition-duration', this.config.speed + 'ms');

        if (this.config.download) {
            this.$toolbar.append(
                `<a id="${this.getIdName(
                    'cres-gallery-download'
                )}" target="_blank" rel="noopener" aria-label="${
                    this.config.strings['download']
                }" download class="cres-gallery-download cres-gallery-icon"></a>`
            );
        }

        this.counter();

        cresQuery(window).on(
            `resize.cres-gallery.global${this.cresGalleryId} orientationchange.cres-gallery.global${this.cresGalleryId}`,
            () => {
                this.refreshOnResize();
            }
        );

        this.hideBars();

        this.manageCloseGallery();
        this.toggleMaximize();

        this.initModules();
    }
    /**
     * @param {number} index
     * @returns {CresQuery}
     */
    getSlideItem(index) {
        return cresQuery(this.getSlideItemId(index));
    }
    /**
     * @param {number} index
     * @returns {string}
     */
    getSlideItemId(index) {
        return `#cres-gallery-item-${this.cresGalleryId}-${index}`;
    }

    /**
     * @param {string} id
     * @returns {string}
     */
    getIdName(id) {
        return `${id}-${this.cresGalleryId}`;
    }
    /**
     * @param {string} id
     * @returns {CresQuery}
     */
    getElementById(id) {
        return cresQuery(`#${this.getIdName(id)}`);
    }
    manageSingleSlideClassName() {
        if (this.galleryItems.length < 2) {
            this.outer.addClass('cres-gallery-single-item');
        } else {
            this.outer.removeClass('cres-gallery-single-item');
        }
    }
    /**
     * @return {void}
     */
    refreshOnResize() {
        if (this.cresGalleryOpened) {
            const currentGalleryItem = this.galleryItems[this.index];
            const { __slideVideoInfo } = currentGalleryItem;

            this.mediaContainerPosition = this.getMediaContainerPosition();
            const { top, bottom } = this.mediaContainerPosition;
            this.currentImageSize = utils.getSize(
                this.items[this.index],
                this.outer,
                top + bottom,
                __slideVideoInfo && this.config.videoMaxSize
            );
            if (__slideVideoInfo) {
                this.resizeVideoSlide(this.index, this.currentImageSize);
            }
            if (this.zoomFromOrigin && !this.isDummyImageRemoved) {
                const imgStyle = this.getDummyImgStyles(this.currentImageSize);
                this.outer
                    .find('.cres-gallery-current .cres-gallery-dummy-img')
                    .first()
                    .attr('style', imgStyle);
            }
            this.cresEl.trigger(galleryEvent.containerResize);
        }
    }
    /**
     * @param {number} index
     * @param {ImageSize} imageSize
     * @return {void}
     */
    resizeVideoSlide(index, imageSize) {
        const cresGalleryVideoStyle = this.getVideoContStyle(imageSize);
        const currentSlide = this.getSlideItem(index);
        currentSlide
            .find('.cres-gallery-video-cont')
            .attr('style', cresGalleryVideoStyle);
    }

    updateSlides(items, index) {
        if (this.index > items.length - 1) {
            this.index = items.length - 1;
        }
        if (items.length === 1) {
            this.index = 0;
        }
        if (!items.length) {
            this.closeGallery();
            return;
        }
        const currentSrc = this.galleryItems[index].src;
        this.galleryItems = items;
        this.updateControls();
        this.$inner.empty();
        this.currentItemsInDom = [];

        let _index = 0;
        // Find the current index based on source value of the slide
        this.galleryItems.some((galleryItem, itemIndex) => {
            if (galleryItem.src === currentSrc) {
                _index = itemIndex;
                return true;
            }
            return false;
        });

        this.currentItemsInDom = this.organizeSlideItems(_index, -1);
        this.loadContent(_index, true);
        this.getSlideItem(_index).addClass('cres-gallery-current');

        this.index = _index;
        this.updateCurrentCounter(_index);
        this.cresEl.trigger(galleryEvent.updateSlides);
    }
    shouldHideScrollbar() {
        return (
            this.config.hideScrollbar && document.body === this.config.container
        );
    }
    hideScrollbar() {
        if (!this.shouldHideScrollbar()) {
            return;
        }
        this.bodyPaddingRight = parseFloat(
            cresQuery('body').style().paddingRight
        );
        const bodyRect = document.documentElement.getBoundingClientRect();
        const scrollbarWidth = window.innerWidth - bodyRect.width;

        cresQuery(document.body).css(
            'padding-right',
            scrollbarWidth + this.bodyPaddingRight + 'px'
        );
        cresQuery(document.body).addClass('cres-gallery-overlay-open');
    }
    resetScrollBar() {
        if (!this.shouldHideScrollbar()) {
            return;
        }
        cresQuery(document.body).css(
            'padding-right',
            this.bodyPaddingRight + 'px'
        );
        cresQuery(document.body).removeClass('cres-gallery-overlay-open');
    }
    openGallery(index = this.config.index, element) {
        // prevent accidental double execution
        if (this.cresGalleryOpened) return;
        this.cresGalleryOpened = true;
        this.outer.removeClass('cres-gallery-hide-items');

        this.hideScrollbar();

        // Add display block, but still has opacity 0
        this.$container.addClass('cres-gallery-show');

        const itemsToBeInsertedToDom = this.getItemsToBeInsertedToDom(
            index,
            index
        );
        this.currentItemsInDom = itemsToBeInsertedToDom;

        let items = '';
        itemsToBeInsertedToDom.forEach((item) => {
            items =
                items + `<div id="${item}" class="cres-gallery-item"></div>`;
        });

        this.$inner.append(items);
        this.addHtml(index);
        let transform = '';
        this.mediaContainerPosition = this.getMediaContainerPosition();
        const { top, bottom } = this.mediaContainerPosition;
        if (!this.config.allowMediaOverlap) {
            this.setMediaContainerPosition(top, bottom);
        }
        const { __slideVideoInfo } = this.galleryItems[index];
        if (this.zoomFromOrigin && element) {
            this.currentImageSize = utils.getSize(
                element,
                this.outer,
                top + bottom,
                __slideVideoInfo && this.config.videoMaxSize
            );
            transform = utils.getTransform(
                element,
                this.outer,
                top,
                bottom,
                this.currentImageSize
            );
        }
        if (!this.zoomFromOrigin || !transform) {
            this.outer.addClass(this.config.startClass);
            this.getSlideItem(index).removeClass('cres-gallery-complete');
        }
        const timeout = this.config.zoomFromOrigin
            ? 100
            : this.config.backdropDuration;
        setTimeout(() => {
            this.outer.addClass('cres-gallery-components-open');
        }, timeout);
        this.index = index;
        this.cresEl.trigger(galleryEvent.beforeOpen);

        // add class cres-gallery-current to remove initial transition
        this.getSlideItem(index).addClass('cres-gallery-current');

        this.cresGalleryOn = false;
        // Store the current scroll top value to scroll back after closing the gallery..
        this.prevScrollTop = cresQuery(window).scrollTop();

        setTimeout(() => {
            // Need to check both zoomFromOrigin and transform values as we need to set set the
            // default opening animation if user missed to add the cres-gallery-size attribute

            if (this.zoomFromOrigin && transform) {
                const currentSlide = this.getSlideItem(index);
                currentSlide.css('transform', transform);
                setTimeout(() => {
                    currentSlide
                        .addClass(
                            'cres-gallery-start-progress cres-gallery-start-end-progress'
                        )
                        .css(
                            'transition-duration',
                            this.config.startAnimationDuration + 'ms'
                        );
                    this.outer.addClass('cres-gallery-zoom-from-image');
                });
                setTimeout(() => {
                    currentSlide.css('transform', 'translate3d(0, 0, 0)');
                }, 100);
            }

            setTimeout(() => {
                this.$backdrop.addClass('in');
                this.$container.addClass('cres-gallery-show-in');
            }, 10);

            setTimeout(() => {
                if (
                    this.config.trapFocus &&
                    document.body === this.config.container
                ) {
                    this.trapFocus();
                }
            }, this.config.backdropDuration + 50);

            // cres-gallery-visible class resets gallery opacity to 1
            if (!this.zoomFromOrigin || !transform) {
                setTimeout(() => {
                    this.outer.addClass('cres-gallery-visible');
                }, this.config.backdropDuration);
            }

            // initiate slide function
            this.slide(index, false, false, false);

            this.cresEl.trigger(galleryEvent.afterOpen);
        });

        if (document.body === this.config.container) {
            cresQuery('html').addClass('cres-gallery-on');
        }
    }
    /**
     * Note - Changing the position of the media on every slide transition creates a flickering effect.
     * Therefore, The height of the caption is calculated dynamically, only once based on the first slide caption.
     * if you have dynamic captions for each media,
     * you can provide an appropriate height for the captions via allowMediaOverlap option
     */
    getMediaContainerPosition() {
        if (this.config.allowMediaOverlap) {
            return {
                top: 0,
                bottom: 0
            };
        }
        const top = this.$toolbar.get().clientHeight || 0;
        const subHtml = this.outer
            .find('.cres-gallery-components .cres-gallery-sub-html')
            .get();
        const captionHeight =
            this.config.defaultCaptionHeight ||
            (subHtml && subHtml.clientHeight) ||
            0;
        const thumbContainer = this.outer
            .find('.cres-gallery-thumb-outer')
            .get();
        const thumbHeight = thumbContainer ? thumbContainer.clientHeight : 0;
        const bottom = thumbHeight + captionHeight;
        return {
            top,
            bottom
        };
    }

    setMediaContainerPosition(top = 0, bottom = 0) {
        this.$content.css('top', top + 'px').css('bottom', bottom + 'px');
    }
    hideBars() {
        // Hide controllers if mouse doesn't move for some period
        setTimeout(() => {
            this.outer.removeClass('cres-gallery-hide-items');
            if (this.config.hideBarsDelay > 0) {
                this.outer.on(
                    'mousemove.cres-gallery click.cres-gallery touchstart.cres-gallery',
                    () => {
                        this.outer.removeClass('cres-gallery-hide-items');

                        clearTimeout(this.hideBarTimeout);

                        // Timeout will be cleared on each slide movement also
                        this.hideBarTimeout = setTimeout(() => {
                            this.outer.addClass('cres-gallery-hide-items');
                        }, this.config.hideBarsDelay);
                    }
                );
                this.outer.trigger('mousemove.cres-gallery');
            }
        }, this.config.showBarsAfter);
    }

    initPictureFill($img) {
        if (this.config.supportLegacyBrowser) {
            try {
                picturefill({
                    elements: [$img.get()]
                });
            } catch (e) {
                console.warn(
                    'cres-gallery :- If you want srcset or picture tag to be supported for older browser please include picturefil javascript library in your document.'
                );
            }
        }
    }

    /**
     *  @desc Create image counter
     *  Ex: 1/10
     */
    counter() {
        if (this.config.counter) {
            const counterHtml = `<div class="cres-gallery-counter" role="status" aria-live="polite">
                <span id="${this.getIdName(
                    'cres-gallery-counter-current'
                )}" class="cres-gallery-counter-current">${
                this.index + 1
            } </span> /
                <span id="${this.getIdName(
                    'cres-gallery-counter-all'
                )}" class="cres-gallery-counter-all">${
                this.galleryItems.length
            } </span></div>`;
            this.outer.find(this.config.appendCounterTo).append(counterHtml);
        }
    }
    /**
     * @desc add sub-html into the slide
     * @param {number} index - index of the slide
     * @return {void}
     */
    addHtml(index) {
        let subHtml;
        let subHtmlUrl;
        if (this.galleryItems[index].subHtmlUrl) {
            subHtmlUrl = this.galleryItems[index].subHtmlUrl;
        } else {
            subHtml = this.galleryItems[index].subHtml;
        }

        if (!subHtmlUrl) {
            if (subHtml) {
                // get first letter of sub-html
                // if first letter starts with . or # get the html form the jQuery object
                const fL = subHtml.substring(0, 1);
                if (fL === '.' || fL === '#') {
                    if (
                        this.config.subHtmlSelectorRelative &&
                        !this.config.dynamic
                    ) {
                        subHtml = cresQuery(this.items)
                            .eq(index)
                            .find(subHtml)
                            .first()
                            .html();
                    } else {
                        subHtml = cresQuery(subHtml).first().html();
                    }
                }
            } else {
                subHtml = '';
            }
        }

        if (this.config.appendSubHtmlTo !== '.cres-gallery-item') {
            if (subHtmlUrl) {
                this.outer.find('.cres-gallery-sub-html').load(subHtmlUrl);
            } else {
                this.outer.find('.cres-gallery-sub-html').html(subHtml);
            }
        } else {
            const currentSlide = cresQuery(this.getSlideItemId(index));
            if (subHtmlUrl) {
                currentSlide.load(subHtmlUrl);
            } else {
                currentSlide.append(
                    `<div class="cres-gallery-sub-html">${subHtml}</div>`
                );
            }
        }

        // Add cres-gallery-empty-html class if title doesn't exist
        if (typeof subHtml !== 'undefined' && subHtml !== null) {
            if (subHtml === '') {
                this.outer
                    .find(this.config.appendSubHtmlTo)
                    .addClass('cres-gallery-empty-html');
            } else {
                this.outer
                    .find(this.config.appendSubHtmlTo)
                    .removeClass('cres-gallery-empty-html');
            }
        }

        this.cresEl.trigger(galleryEvent.afterAppendSubHtml, {
            index
        });
    }
    /**
     *  @desc Preload slides
     *  @param {Number} index - index of the slide
     * @todo preload not working for the first slide, Also, should work for the first and last slide as well
     */
    preload(index) {
        for (let i = 1; i <= this.config.preload; i++) {
            if (i >= this.galleryItems.length - index) {
                break;
            }

            this.loadContent(index + i, false);
        }

        for (let j = 1; j <= this.config.preload; j++) {
            if (index - j < 0) {
                break;
            }

            this.loadContent(index - j, false);
        }
    }

    /**
     *
     * @param {ImageSize} imageSize
     * @returns {string}
     */
    getDummyImgStyles(imageSize) {
        if (!imageSize) return '';
        return `width:${imageSize.width}px;
                margin-left: -${imageSize.width / 2}px;
                margin-top: -${imageSize.height / 2}px;
                height:${imageSize.height}px`;
    }

    /**
     *
     * @param {ImageSize} imageSize
     * @returns {string}
     */
    getVideoContStyle(imageSize) {
        if (!imageSize) return '';
        return `width:${imageSize.width}px;
                height:${imageSize.height}px`;
    }

    /**
     *
     * @param {CresQuery} $currentSlide
     * @param {number} index
     * @param {string} alt
     * @returns {string}
     */
    getDummyImageContent($currentSlide, index, alt) {
        let $currentItem;
        if (!this.config.dynamic) {
            $currentItem = cresQuery(this.items).eq(index);
        }
        if ($currentItem) {
            let _dummyImgSrc;
            if (!this.config.exThumbImage) {
                _dummyImgSrc = $currentItem.find('img').first().attr('src');
            } else {
                _dummyImgSrc = $currentItem.attr(this.config.exThumbImage);
            }
            if (!_dummyImgSrc) return '';
            const imgStyle = this.getDummyImgStyles(this.currentImageSize);
            const dummyImgContent = `<img ${alt} style="${imgStyle}" class="cres-gallery-dummy-img" src="${_dummyImgSrc}" />`;

            $currentSlide.addClass('cres-gallery-first-slide');
            this.outer.addClass('cres-gallery-first-slide-loading');

            return dummyImgContent;
        }
        return '';
    }

    /**
     *
     * @param {string} src
     * @param {CresQuery} $currentSlide
     * @param {number} index
     * @returns {void}
     */
    setImgMarkup(src, $currentSlide, index) {
        const currentGalleryItem = this.galleryItems[index];
        const { alt, srcset, sizes, sources } = currentGalleryItem;

        // Use the thumbnail as dummy image which will be resized to actual image size and
        // displayed on top of actual image
        let imgContent = '';
        const altAttr = alt ? 'alt="' + alt + '"' : '';

        if (this.isFirstSlideWithZoomAnimation()) {
            imgContent = this.getDummyImageContent(
                $currentSlide,
                index,
                altAttr
            );
        } else {
            imgContent = utils.getImgMarkup(
                index,
                src,
                altAttr,
                srcset,
                sizes,
                sources
            );
        }
        const imgMarkup = `<picture class="cres-gallery-img-wrap"> ${imgContent}</picture>`;
        $currentSlide.prepend(imgMarkup);
    }

    onSlideObjectLoad($slide, isHTML5VideoWithoutPoster, onLoad, onError) {
        const mediaObject = $slide.find('.cres-gallery-object').first();
        if (
            utils.isImageLoaded(mediaObject.get()) ||
            isHTML5VideoWithoutPoster
        ) {
            onLoad();
        } else {
            mediaObject.on('load.cres-gallery error.cres-gallery', () => {
                onLoad && onLoad();
            });
            mediaObject.on('error.cres-gallery', () => {
                onError && onError();
            });
        }
    }

    onLgObjectLoad(
        currentSlide,
        index,
        delay,
        speed,
        isFirstSlide,
        isHTML5VideoWithoutPoster
    ) {
        this.onSlideObjectLoad(
            currentSlide,
            isHTML5VideoWithoutPoster,
            () => {
                this.triggerSlideItemLoad(
                    currentSlide,
                    index,
                    delay,
                    speed,
                    isFirstSlide
                );
            },
            () => {
                currentSlide.addClass(
                    'cres-gallery-complete cres-gallery-complete_'
                );
                currentSlide.html(
                    '<span class="cres-gallery-error-msg">Oops... Failed to load content...</span>'
                );
            }
        );
    }

    triggerSlideItemLoad($currentSlide, index, delay, speed, isFirstSlide) {
        const currentGalleryItem = this.galleryItems[index];

        // Adding delay for video slides without poster for better performance and user experience
        // Videos should start playing once once the gallery is completely loaded
        const _speed =
            isFirstSlide &&
            this.getSlideType(currentGalleryItem) === 'video' &&
            !currentGalleryItem.poster
                ? speed
                : 0;
        setTimeout(() => {
            $currentSlide.addClass(
                'cres-gallery-complete cres-gallery-complete_'
            );
            this.cresEl.trigger(galleryEvent.slideItemLoad,
                {
                    index,
                    delay: delay || 0,
                    isFirstSlide
                });
        }, _speed);
    }

    isFirstSlideWithZoomAnimation() {
        return !!(
            !this.cresGalleryOn &&
            this.zoomFromOrigin &&
            this.currentImageSize
        );
    }

    // Add video slideInfo
    addSlideVideoInfo(items) {
        items.forEach((element, index) => {
            element.__slideVideoInfo = utils.isVideo(
                element.src,
                !!element.video,
                index
            );
            if (
                element.__slideVideoInfo &&
                this.config.loadYouTubePoster &&
                !element.poster &&
                element.__slideVideoInfo.youtube
            ) {
                element.poster = `//img.youtube.com/vi/${element.__slideVideoInfo.youtube[1]}/maxresdefault.jpg`;
            }
        });
    }

    /**
     *  Load slide content into slide.
     *  This is used to load content into slides that is not visible too
     *  @param {Number} index - index of the slide.
     *  @param {Boolean} rec - if true call loadcontent() function again.
     */
    loadContent(index, rec) {
        const currentGalleryItem = this.galleryItems[index];
        const $currentSlide = cresQuery(this.getSlideItemId(index));

        const { poster, srcset, sizes, sources } = currentGalleryItem;
        let { src } = currentGalleryItem;

        const video = currentGalleryItem.video;

        const _html5Video =
            video && typeof video === 'string' ? JSON.parse(video) : video;

        if (currentGalleryItem.responsive) {
            const srcDyItms = currentGalleryItem.responsive.split(',');
            src = utils.getResponsiveSrc(srcDyItms) || src;
        }

        const videoInfo = currentGalleryItem.__slideVideoInfo;
        let cresGalleryVideoStyle = '';

        const iframe = !!currentGalleryItem.iframe;

        const isFirstSlide = !this.cresGalleryOn;

        // delay for adding complete class. it is 0 except first time.
        let delay = 0;
        if (isFirstSlide) {
            if (this.zoomFromOrigin && this.currentImageSize) {
                delay = this.config.startAnimationDuration + 10;
            } else {
                delay = this.config.backdropDuration + 10;
            }
        }

        if (!$currentSlide.hasClass('cres-gallery-loaded')) {
            if (videoInfo) {
                const { top, bottom } = this.mediaContainerPosition;
                const videoSize = utils.getSize(
                    this.items[index],
                    this.outer,
                    top + bottom,
                    videoInfo && this.config.videoMaxSize
                );
                cresGalleryVideoStyle = this.getVideoContStyle(videoSize);
            }
            if (iframe) {
                const markup = utils.getIframeMarkup(
                    this.config.iframeWidth,
                    this.config.iframeHeight,
                    this.config.iframeMaxWidth,
                    this.config.iframeMaxHeight,
                    src,
                    currentGalleryItem.iframeTitle
                );
                $currentSlide.prepend(markup);
            } else if (poster) {
                let dummyImg = '';
                const hasStartAnimation =
                    isFirstSlide &&
                    this.zoomFromOrigin &&
                    this.currentImageSize;
                if (hasStartAnimation) {
                    dummyImg = this.getDummyImageContent(
                        $currentSlide,
                        index,
                        ''
                    );
                }

                const markup = utils.getVideoPosterMarkup(
                    poster,
                    dummyImg || '',
                    cresGalleryVideoStyle,
                    this.config.strings['playVideo'],
                    videoInfo
                );
                $currentSlide.prepend(markup);
            } else if (videoInfo) {
                const markup = `<div class="cres-gallery-video-cont " style="${cresGalleryVideoStyle}"></div>`;
                $currentSlide.prepend(markup);
            } else {
                this.setImgMarkup(src, $currentSlide, index);
                if (srcset || sources) {
                    const $img = $currentSlide.find('.cres-gallery-object');
                    this.initPictureFill($img);
                }
            }
            if (poster || videoInfo) {
                this.cresEl.trigger(galleryEvent.hasVideo, {
                    index,
                    src: src,
                    html5Video: _html5Video,
                    hasPoster: !!poster
                });
            }

            this.cresEl.trigger(galleryEvent.afterAppendSlide, { index });

            if (
                this.cresGalleryOn &&
                this.config.appendSubHtmlTo === '.cres-gallery-item'
            ) {
                this.addHtml(index);
            }
        }

        // For first time add some delay for displaying the start animation.
        let _speed = 0;

        // Do not change the delay value because it is required for zoom plugin.
        // If gallery opened from direct url (hash) speed value should be 0
        if (
            delay &&
            !cresQuery(document.body).hasClass('cres-gallery-from-hash')
        ) {
            _speed = delay;
        }

        // Only for first slide and zoomFromOrigin is enabled
        if (this.isFirstSlideWithZoomAnimation()) {
            setTimeout(() => {
                $currentSlide
                    .removeClass(
                        'cres-gallery-start-end-progress cres-gallery-start-progress'
                    )
                    .removeAttr('style');
            }, this.config.startAnimationDuration + 100);
            if (!$currentSlide.hasClass('cres-gallery-loaded')) {
                setTimeout(() => {
                    if (this.getSlideType(currentGalleryItem) === 'image') {
                        const { alt } = currentGalleryItem;
                        const altAttr = alt ? 'alt="' + alt + '"' : '';

                        $currentSlide
                            .find('.cres-gallery-img-wrap')
                            .append(
                                utils.getImgMarkup(
                                    index,
                                    src,
                                    altAttr,
                                    srcset,
                                    sizes,
                                    currentGalleryItem.sources
                                )
                            );
                        if (srcset || sources) {
                            const $img = $currentSlide.find(
                                '.cres-gallery-object'
                            );
                            this.initPictureFill($img);
                        }
                    }
                    if (
                        this.getSlideType(currentGalleryItem) === 'image' ||
                        (this.getSlideType(currentGalleryItem) === 'video' &&
                            poster)
                    ) {
                        this.onLgObjectLoad(
                            $currentSlide,
                            index,
                            delay,
                            _speed,
                            true,
                            false
                        );

                        // load remaining slides once the slide is completely loaded
                        this.onSlideObjectLoad(
                            $currentSlide,
                            !!(videoInfo && videoInfo.html5 && !poster),
                            () => {
                                this.loadContentOnFirstSlideLoad(
                                    index,
                                    $currentSlide,
                                    _speed
                                );
                            },
                            () => {
                                this.loadContentOnFirstSlideLoad(
                                    index,
                                    $currentSlide,
                                    _speed
                                );
                            }
                        );
                    }
                }, this.config.startAnimationDuration + 100);
            }
        }

        // SLide content has been added to dom
        $currentSlide.addClass('cres-gallery-loaded');

        if (
            !this.isFirstSlideWithZoomAnimation() ||
            (this.getSlideType(currentGalleryItem) === 'video' && !poster)
        ) {
            this.onLgObjectLoad(
                $currentSlide,
                index,
                delay,
                _speed,
                isFirstSlide,
                !!(videoInfo && videoInfo.html5 && !poster)
            );
        }

        // When gallery is opened once content is loaded (second time) need to add cres-gallery-complete class for css styling
        if (
            (!this.zoomFromOrigin || !this.currentImageSize) &&
            $currentSlide.hasClass('cres-gallery-complete_') &&
            !this.cresGalleryOn
        ) {
            setTimeout(() => {
                $currentSlide.addClass('cres-gallery-complete');
            }, this.config.backdropDuration);
        }

        // Content loaded
        // Need to set lGalleryOn before calling preload function
        this.cresGalleryOn = true;

        if (rec === true) {
            if (!$currentSlide.hasClass('cres-gallery-complete_')) {
                $currentSlide
                    .find('.cres-gallery-object')
                    .first()
                    .on('load.cres-gallery error.cres-gallery', () => {
                        this.preload(index);
                    });
            } else {
                this.preload(index);
            }
        }
    }

    /**
     * @desc Remove dummy image content and load next slides
     * Called only for the first time if zoomFromOrigin animation is enabled
     * @param index
     * @param $currentSlide
     * @param speed
     */
    loadContentOnFirstSlideLoad(index, $currentSlide, speed) {
        setTimeout(() => {
            $currentSlide.find('.cres-gallery-dummy-img').remove();
            $currentSlide.removeClass('cres-gallery-first-slide');
            this.outer.removeClass('cres-gallery-first-slide-loading');
            this.isDummyImageRemoved = true;
            this.preload(index);
        }, speed + 300);
    }

    getItemsToBeInsertedToDom(index, prevIndex, numberOfItems = 0) {
        const itemsToBeInsertedToDom = [];
        // Minimum 2 items should be there
        let possibleNumberOfItems = Math.max(numberOfItems, 3);
        possibleNumberOfItems = Math.min(
            possibleNumberOfItems,
            this.galleryItems.length
        );
        const prevIndexItem = `cres-gallery-item-${this.cresGalleryId}-${prevIndex}`;
        if (this.galleryItems.length <= 3) {
            this.galleryItems.forEach((_element, index) => {
                itemsToBeInsertedToDom.push(
                    `cres-gallery-item-${this.cresGalleryId}-${index}`
                );
            });
            return itemsToBeInsertedToDom;
        }

        if (index < (this.galleryItems.length - 1) / 2) {
            for (
                let idx = index;
                idx > index - possibleNumberOfItems / 2 && idx >= 0;
                idx--
            ) {
                itemsToBeInsertedToDom.push(
                    `cres-gallery-item-${this.cresGalleryId}-${idx}`
                );
            }
            const numberOfExistingItems = itemsToBeInsertedToDom.length;
            for (
                let idx = 0;
                idx < possibleNumberOfItems - numberOfExistingItems;
                idx++
            ) {
                itemsToBeInsertedToDom.push(
                    `cres-gallery-item-${this.cresGalleryId}-${index + idx + 1}`
                );
            }
        } else {
            for (
                let idx = index;
                idx <= this.galleryItems.length - 1 &&
                idx < index + possibleNumberOfItems / 2;
                idx++
            ) {
                itemsToBeInsertedToDom.push(
                    `cres-gallery-item-${this.cresGalleryId}-${idx}`
                );
            }
            const numberOfExistingItems = itemsToBeInsertedToDom.length;
            for (
                let idx = 0;
                idx < possibleNumberOfItems - numberOfExistingItems;
                idx++
            ) {
                itemsToBeInsertedToDom.push(
                    `cres-gallery-item-${this.cresGalleryId}-${index - idx - 1}`
                );
            }
        }
        if (this.config.loop) {
            if (index === this.galleryItems.length - 1) {
                itemsToBeInsertedToDom.push(
                    `cres-gallery-item-${this.cresGalleryId}-${0}`
                );
            } else if (index === 0) {
                itemsToBeInsertedToDom.push(
                    `cres-gallery-item-${this.cresGalleryId}-${
                        this.galleryItems.length - 1
                    }`
                );
            }
        }
        if (itemsToBeInsertedToDom.indexOf(prevIndexItem) === -1) {
            itemsToBeInsertedToDom.push(
                `cres-gallery-item-${this.cresGalleryId}-${prevIndex}`
            );
        }

        return itemsToBeInsertedToDom;
    }

    organizeSlideItems(index, prevIndex) {
        const itemsToBeInsertedToDom = this.getItemsToBeInsertedToDom(
            index,
            prevIndex,
            this.config.numberOfSlideItemsInDom
        );

        itemsToBeInsertedToDom.forEach((item) => {
            if (this.currentItemsInDom.indexOf(item) === -1) {
                this.$inner.append(
                    `<div id="${item}" class="cres-gallery-item"></div>`
                );
            }
        });

        this.currentItemsInDom.forEach((item) => {
            if (itemsToBeInsertedToDom.indexOf(item) === -1) {
                cresQuery(`#${item}`).remove();
            }
        });
        return itemsToBeInsertedToDom;
    }

    /**
     * Get previous index of the slide
     */
    getPreviousSlideIndex() {
        let prevIndex = 0;
        try {
            const currentItemId = this.outer
                .find('.cres-gallery-current')
                .first()
                .attr('id');
            prevIndex = parseInt(currentItemId.split('-')[4]) || 0;
        } catch (error) {
            prevIndex = 0;
        }
        return prevIndex;
    }

    setDownloadValue(index) {
        if (this.config.download) {
            const currentGalleryItem = this.galleryItems[index];
            const hideDownloadBtn =
                currentGalleryItem.downloadUrl === false ||
                currentGalleryItem.downloadUrl === 'false';
            if (hideDownloadBtn) {
                this.outer.addClass('cres-gallery-hide-download');
            } else {
                const $download = this.getElementById('cres-gallery-download');
                this.outer.removeClass('cres-gallery-hide-download');
                $download.attr(
                    'href',
                    currentGalleryItem.downloadUrl || currentGalleryItem.src
                );
                if (currentGalleryItem.download) {
                    $download.attr('download', currentGalleryItem.download);
                }
            }
        }
    }

    makeSlideAnimation(direction, currentSlideItem, previousSlideItem) {
        if (this.cresGalleryOn) {
            previousSlideItem.addClass('cres-gallery-slide-progress');
        }
        setTimeout(
            () => {
                // remove all transitions
                this.outer.addClass('cres-gallery-no-trans');

                this.outer
                    .find('.cres-gallery-item')
                    .removeClass(
                        'cres-gallery-prev-slide cres-gallery-next-slide'
                    );

                if (direction === 'prev') {
                    //prevslide
                    currentSlideItem.addClass('cres-gallery-prev-slide');
                    previousSlideItem.addClass('cres-gallery-next-slide');
                } else {
                    // next slide
                    currentSlideItem.addClass('cres-gallery-next-slide');
                    previousSlideItem.addClass('cres-gallery-prev-slide');
                }

                // give 50 ms for browser to add/remove class
                setTimeout(() => {
                    this.outer
                        .find('.cres-gallery-item')
                        .removeClass('cres-gallery-current');

                    currentSlideItem.addClass('cres-gallery-current');

                    // reset all transitions
                    this.outer.removeClass('cres-gallery-no-trans');
                }, 50);
            },
            this.cresGalleryOn ? this.config.slideDelay : 0
        );
    }

    /**
     * Goto a specific slide.
     * @param {Number} index - index of the slide
     * @param {Boolean} fromTouch - true if slide function called via touch event or mouse drag
     * @param {Boolean} fromThumb - true if slide function called via thumbnail click
     * @param {String} direction - Direction of the slide(next/prev)
     * @category lGPublicMethods
     * @example
     *  const plugin = cresGallery();
     *  // to go to 3rd slide
     *  plugin.slide(2);
     *
     */
    slide(index, fromTouch, fromThumb, direction) {
        const prevIndex = this.getPreviousSlideIndex();
        this.currentItemsInDom = this.organizeSlideItems(index, prevIndex);
        // Prevent multiple call, Required for hsh plugin
        if (this.cresGalleryOn && prevIndex === index) {
            return;
        }

        const numberOfGalleryItems = this.galleryItems.length;

        if (!this.cresGalleryBusy) {
            if (this.config.counter) {
                this.updateCurrentCounter(index);
            }

            const currentSlideItem = this.getSlideItem(index);
            const previousSlideItem = this.getSlideItem(prevIndex);

            const currentGalleryItem = this.galleryItems[index];
            const videoInfo = currentGalleryItem.__slideVideoInfo;

            this.outer.attr(
                'data-cres-gallery-slide-type',
                this.getSlideType(currentGalleryItem)
            );
            this.setDownloadValue(index);

            if (videoInfo) {
                const { top, bottom } = this.mediaContainerPosition;
                const videoSize = utils.getSize(
                    this.items[index],
                    this.outer,
                    top + bottom,
                    videoInfo && this.config.videoMaxSize
                );
                this.resizeVideoSlide(index, videoSize);
            }

            this.cresEl.trigger(galleryEvent.beforeSlide,
                {
                    prevIndex,
                    index,
                    fromTouch: !!fromTouch,
                    fromThumb: !!fromThumb
                });

            this.cresGalleryBusy = true;

            clearTimeout(this.hideBarTimeout);

            this.arrowDisable(index);

            if (!direction) {
                if (index < prevIndex) {
                    direction = 'prev';
                } else if (index > prevIndex) {
                    direction = 'next';
                }
            }

            if (!fromTouch) {
                this.makeSlideAnimation(
                    direction,
                    currentSlideItem,
                    previousSlideItem
                );
            } else {
                this.outer
                    .find('.cres-gallery-item')
                    .removeClass(
                        'cres-gallery-prev-slide cres-gallery-current cres-gallery-next-slide'
                    );
                let touchPrev;
                let touchNext;
                if (numberOfGalleryItems > 2) {
                    touchPrev = index - 1;
                    touchNext = index + 1;

                    if (index === 0 && prevIndex === numberOfGalleryItems - 1) {
                        // next slide
                        touchNext = 0;
                        touchPrev = numberOfGalleryItems - 1;
                    } else if (
                        index === numberOfGalleryItems - 1 &&
                        prevIndex === 0
                    ) {
                        // prev slide
                        touchNext = 0;
                        touchPrev = numberOfGalleryItems - 1;
                    }
                } else {
                    touchPrev = 0;
                    touchNext = 1;
                }

                if (direction === 'prev') {
                    this.getSlideItem(touchNext).addClass(
                        'cres-gallery-next-slide'
                    );
                } else {
                    this.getSlideItem(touchPrev).addClass(
                        'cres-gallery-prev-slide'
                    );
                }

                currentSlideItem.addClass('cres-gallery-current');
            }

            // Do not put load content in set timeout as it needs to load immediately when the gallery is opened
            if (!this.cresGalleryOn) {
                this.loadContent(index, true);
            } else {
                setTimeout(() => {
                    this.loadContent(index, true);
                    // Add title if this.config.appendSubHtmlTo === cres-gallery-sub-html
                    if (this.config.appendSubHtmlTo !== '.cres-gallery-item') {
                        this.addHtml(index);
                    }
                }, this.config.speed + 50 + (fromTouch ? 0 : this.config.slideDelay));
            }

            setTimeout(() => {
                this.cresGalleryBusy = false;
                previousSlideItem.removeClass('cres-gallery-slide-progress');
                this.cresEl.trigger(galleryEvent.afterSlide, {
                    prevIndex: prevIndex,
                    index,
                    fromTouch,
                    fromThumb
                });
            }, (this.cresGalleryOn ? this.config.speed + 100 : 100) + (fromTouch ? 0 : this.config.slideDelay));
        }

        this.index = index;
    }

    updateCurrentCounter(index) {
        this.getElementById('cres-gallery-counter-current').html(
            index + 1 + ''
        );
    }

    updateCounterTotal() {
        this.getElementById('cres-gallery-counter-all').html(
            this.galleryItems.length + ''
        );
    }

    getSlideType(item) {
        if (item.__slideVideoInfo) {
            return 'video';
        } else if (item.iframe) {
            return 'iframe';
        } else {
            return 'image';
        }
    }

    touchMove(startCoords, endCoords, e) {
        const distanceX = endCoords.pageX - startCoords.pageX;
        const distanceY = endCoords.pageY - startCoords.pageY;
        let allowSwipe = false;

        if (this.swipeDirection) {
            allowSwipe = true;
        } else {
            if (Math.abs(distanceX) > 15) {
                this.swipeDirection = 'horizontal';
                allowSwipe = true;
            } else if (Math.abs(distanceY) > 15) {
                this.swipeDirection = 'vertical';
                allowSwipe = true;
            }
        }

        if (!allowSwipe) {
            return;
        }

        const $currentSlide = this.getSlideItem(this.index);

        if (this.swipeDirection === 'horizontal') {
            e?.preventDefault();
            // reset opacity and transition duration
            this.outer.addClass('cres-gallery-dragging');

            // move current slide
            this.setTranslate($currentSlide, distanceX, 0);

            // move next and prev slide with current slide
            const width = $currentSlide.get().offsetWidth;
            const slideWidthAmount = (width * 15) / 100;
            const gutter = slideWidthAmount - Math.abs((distanceX * 10) / 100);
            this.setTranslate(
                this.outer.find('.cres-gallery-prev-slide').first(),
                -width + distanceX - gutter,
                0
            );

            this.setTranslate(
                this.outer.find('.cres-gallery-next-slide').first(),
                width + distanceX + gutter,
                0
            );
        } else if (this.swipeDirection === 'vertical') {
            if (this.config.swipeToClose) {
                e?.preventDefault();
                this.$container.addClass('cres-gallery-dragging-vertical');

                const opacity = 1 - Math.abs(distanceY) / window.innerHeight;
                this.$backdrop.css('opacity', opacity);

                const scale = 1 - Math.abs(distanceY) / (window.innerWidth * 2);
                this.setTranslate($currentSlide, 0, distanceY, scale, scale);
                if (Math.abs(distanceY) > 100) {
                    this.outer
                        .addClass('cres-gallery-hide-items')
                        .removeClass('cres-gallery-components-open');
                }
            }
        }
    }

    touchEnd(endCoords, startCoords, event) {
        let distance;

        // keep slide animation for any mode while dragg/swipe
        if (this.config.mode !== 'cres-gallery-slide') {
            this.outer.addClass('cres-gallery-slide');
        }

        // set transition duration
        setTimeout(() => {
            this.$container.removeClass('cres-gallery-dragging-vertical');
            this.outer
                .removeClass('cres-gallery-dragging cres-gallery-hide-items')
                .addClass('cres-gallery-components-open');

            let triggerClick = true;

            if (this.swipeDirection === 'horizontal') {
                distance = endCoords.pageX - startCoords.pageX;
                const distanceAbs = Math.abs(
                    endCoords.pageX - startCoords.pageX
                );
                if (distance < 0 && distanceAbs > this.config.swipeThreshold) {
                    this.goToNextSlide(true);
                    triggerClick = false;
                } else if (
                    distance > 0 &&
                    distanceAbs > this.config.swipeThreshold
                ) {
                    this.goToPrevSlide(true);
                    triggerClick = false;
                }
            } else if (this.swipeDirection === 'vertical') {
                distance = Math.abs(endCoords.pageY - startCoords.pageY);
                if (
                    this.config.closable &&
                    this.config.swipeToClose &&
                    distance > 100
                ) {
                    this.closeGallery();
                    return;
                } else {
                    this.$backdrop.css('opacity', 1);
                }
            }
            this.outer.find('.cres-gallery-item').removeAttr('style');

            if (
                triggerClick &&
                Math.abs(endCoords.pageX - startCoords.pageX) < 5
            ) {
                // Trigger click if distance is less than 5 pix
                const target = cresQuery(event.target);
                if (this.isPosterElement(target)) {
                    this.cresEl.trigger(galleryEvent.posterClick);
                }
            }

            this.swipeDirection = undefined;
        });

        // remove slide class once drag/swipe is completed if mode is not slide
        setTimeout(() => {
            if (
                !this.outer.hasClass('cres-gallery-dragging') &&
                this.config.mode !== 'cres-gallery-slide'
            ) {
                this.outer.removeClass('cres-gallery-slide');
            }
        }, this.config.speed + 100);
    }

    enableSwipe() {
        let startCoords = {};
        let endCoords = {};
        let isMoved = false;
        let isSwiping = false;

        if (this.config.enableSwipe) {
            this.$inner.on('touchstart.cres-gallery', (e) => {
                this.dragOrSwipeEnabled = true;
                const $item = this.getSlideItem(this.index);
                if (
                    (cresQuery(e.target).hasClass('cres-gallery-item') ||
                        $item.get().contains(e.target)) &&
                    !this.outer.hasClass('cres-gallery-zoomed') &&
                    !this.cresGalleryBusy &&
                    e.targetTouches.length === 1
                ) {
                    isSwiping = true;
                    this.touchAction = 'swipe';
                    this.manageSwipeClass();
                    startCoords = {
                        pageX: e.targetTouches[0].pageX,
                        pageY: e.targetTouches[0].pageY
                    };
                }
            });

            this.$inner.on('touchmove.cres-gallery', (e) => {
                if (
                    isSwiping &&
                    this.touchAction === 'swipe' &&
                    e.targetTouches.length === 1
                ) {
                    endCoords = {
                        pageX: e.targetTouches[0].pageX,
                        pageY: e.targetTouches[0].pageY
                    };
                    this.touchMove(startCoords, endCoords, e);
                    isMoved = true;
                }
            });

            this.$inner.on('touchend.cres-gallery', (event) => {
                if (this.touchAction === 'swipe') {
                    if (isMoved) {
                        isMoved = false;
                        this.touchEnd(endCoords, startCoords, event);
                    } else if (isSwiping) {
                        const target = cresQuery(event.target);
                        if (this.isPosterElement(target)) {
                            this.cresEl.trigger(galleryEvent.posterClick);
                        }
                    }
                    this.touchAction = undefined;
                    isSwiping = false;
                }
            });
        }
    }

    enableDrag() {
        let startCoords = {};
        let endCoords = {};
        let isDraging = false;
        let isMoved = false;
        if (this.config.enableDrag) {
            this.outer.on('mousedown.cres-gallery', (e) => {
                this.dragOrSwipeEnabled = true;
                const $item = this.getSlideItem(this.index);
                if (
                    cresQuery(e.target).hasClass('cres-gallery-item') ||
                    $item.get().contains(e.target)
                ) {
                    if (
                        !this.outer.hasClass('cres-gallery-zoomed') &&
                        !this.cresGalleryBusy
                    ) {
                        e.preventDefault();
                        if (!this.cresGalleryBusy) {
                            this.manageSwipeClass();
                            startCoords = {
                                pageX: e.pageX,
                                pageY: e.pageY
                            };
                            isDraging = true;

                            // ** Fix for webkit cursor issue https://code.google.com/p/chromium/issues/detail?id=26723
                            this.outer.get().scrollLeft += 1;
                            this.outer.get().scrollLeft -= 1;

                            // *

                            this.outer
                                .removeClass('cres-gallery-grab')
                                .addClass('cres-gallery-grabbing');

                            this.cresEl.trigger(galleryEvent.dragStart);
                        }
                    }
                }
            });

            cresQuery(window).on(
                `mousemove.cres-gallery.global${this.cresGalleryId}`,
                (e) => {
                    if (isDraging && this.cresGalleryOpened) {
                        isMoved = true;
                        endCoords = {
                            pageX: e.pageX,
                            pageY: e.pageY
                        };
                        this.touchMove(startCoords, endCoords);
                        this.cresEl.trigger(galleryEvent.dragMove);
                    }
                }
            );

            cresQuery(window).on(
                `mouseup.cres-gallery.global${this.cresGalleryId}`,
                (event) => {
                    if (!this.cresGalleryOpened) {
                        return;
                    }
                    const target = cresQuery(event.target);
                    if (isMoved) {
                        isMoved = false;
                        this.touchEnd(endCoords, startCoords, event);
                        this.cresEl.trigger(galleryEvent.dragEnd);
                    } else if (this.isPosterElement(target)) {
                        this.cresEl.trigger(galleryEvent.posterClick);
                    }

                    // Prevent execution on click
                    if (isDraging) {
                        isDraging = false;
                        this.outer
                            .removeClass('cres-gallery-grabbing')
                            .addClass('cres-gallery-grab');
                    }
                }
            );
        }
    }

    triggerPosterClick() {
        this.$inner.on('click.cres-gallery', (event) => {
            if (
                !this.dragOrSwipeEnabled &&
                this.isPosterElement(cresQuery(event.target))
            ) {
                this.cresEl.trigger(galleryEvent.posterClick);
            }
        });
    }

    manageSwipeClass() {
        let _touchNext = this.index + 1;
        let _touchPrev = this.index - 1;
        if (this.config.loop && this.galleryItems.length > 2) {
            if (this.index === 0) {
                _touchPrev = this.galleryItems.length - 1;
            } else if (this.index === this.galleryItems.length - 1) {
                _touchNext = 0;
            }
        }

        this.outer
            .find('.cres-gallery-item')
            .removeClass('cres-gallery-next-slide cres-gallery-prev-slide');
        if (_touchPrev > -1) {
            this.getSlideItem(_touchPrev).addClass('cres-gallery-prev-slide');
        }

        this.getSlideItem(_touchNext).addClass('cres-gallery-next-slide');
    }

    goToNextSlide(fromTouch) {
        let _loop = this.config.loop;
        if (fromTouch && this.galleryItems.length < 3) {
            _loop = false;
        }

        if (!this.cresGalleryBusy) {
            if (this.index + 1 < this.galleryItems.length) {
                this.index++;
                this.cresEl.trigger(galleryEvent.beforeNextSlide, {
                    index: this.index
                });
                this.slide(this.index, !!fromTouch, false, 'next');
            } else {
                if (_loop) {
                    this.index = 0;
                    this.cresEl.trigger(galleryEvent.beforeNextSlide, {
                        index: this.index
                    });
                    this.slide(this.index, !!fromTouch, false, 'next');
                } else if (this.config.slideEndAnimation && !fromTouch) {
                    this.outer.addClass('cres-gallery-right-end');
                    setTimeout(() => {
                        this.outer.removeClass('cres-gallery-right-end');
                    }, 400);
                }
            }
        }
    }

    goToPrevSlide(fromTouch) {
        let _loop = this.config.loop;
        if (fromTouch && this.galleryItems.length < 3) {
            _loop = false;
        }

        if (!this.cresGalleryBusy) {
            if (this.index > 0) {
                this.index--;
                this.cresEl.trigger(galleryEvent.beforePrevSlide, {
                    index: this.index,
                    fromTouch
                });
                this.slide(this.index, !!fromTouch, false, 'prev');
            } else {
                if (_loop) {
                    this.index = this.galleryItems.length - 1;
                    this.cresEl.trigger(galleryEvent.beforePrevSlide, {
                        index: this.index,
                        fromTouch
                    });
                    this.slide(this.index, !!fromTouch, false, 'prev');
                } else if (this.config.slideEndAnimation && !fromTouch) {
                    this.outer.addClass('cres-gallery-left-end');
                    setTimeout(() => {
                        this.outer.removeClass('cres-gallery-left-end');
                    }, 400);
                }
            }
        }
    }

    keyPress() {
        cresQuery(window).on(
            `keydown.cres-gallery.global${this.cresGalleryId}`,
            (e) => {
                if (
                    this.cresGalleryOpened &&
                    this.config.escKey === true &&
                    e.keyCode === 27
                ) {
                    e.preventDefault();
                    if (
                        this.config.allowMediaOverlap &&
                        this.outer.hasClass('cres-gallery-can-toggle') &&
                        this.outer.hasClass('cres-gallery-components-open')
                    ) {
                        this.outer.removeClass('cres-gallery-components-open');
                    } else {
                        this.closeGallery();
                    }
                }
                if (this.cresGalleryOpened && this.galleryItems.length > 1) {
                    if (e.keyCode === 37) {
                        e.preventDefault();
                        this.goToPrevSlide();
                    }

                    if (e.keyCode === 39) {
                        e.preventDefault();
                        this.goToNextSlide();
                    }
                }
            }
        );
    }

    arrow() {
        this.getElementById('cres-gallery-prev').on('click.cres-gallery', () => {
            this.goToPrevSlide();
        });
        this.getElementById('cres-gallery-next').on('click.cres-gallery', () => {
            this.goToNextSlide();
        });
    }

    arrowDisable(index) {
        // Disable arrows if settings.hideControlOnEnd is true
        if (!this.config.loop && this.config.hideControlOnEnd) {
            const $prev = this.getElementById('cres-gallery-prev');
            const $next = this.getElementById('cres-gallery-next');
            if (index + 1 === this.galleryItems.length) {
                $next.attr('disabled', 'disabled').addClass('disabled');
            } else {
                $next.removeAttr('disabled').removeClass('disabled');
            }

            if (index === 0) {
                $prev.attr('disabled', 'disabled').addClass('disabled');
            } else {
                $prev.removeAttr('disabled').removeClass('disabled');
            }
        }
    }

    setTranslate($el, xValue, yValue, scaleX = 1, scaleY = 1) {
        $el.css(
            'transform',
            'translate3d(' +
                xValue +
                'px, ' +
                yValue +
                'px, 0px) scale3d(' +
                scaleX +
                ', ' +
                scaleY +
                ', 1)'
        );
    }

    mousewheel() {
        let lastCall = 0;
        this.outer.on('wheel.cres-gallery', (e) => {
            if (!e.deltaY || this.galleryItems.length < 2) {
                return;
            }
            e.preventDefault();
            const now = new Date().getTime();
            if (now - lastCall < 1000) {
                return;
            }
            lastCall = now;
            if (e.deltaY > 0) {
                this.goToNextSlide();
            } else if (e.deltaY < 0) {
                this.goToPrevSlide();
            }
        });
    }

    isSlideElement(target) {
        return (
            target.hasClass('cres-gallery-outer') ||
            target.hasClass('cres-gallery-item') ||
            target.hasClass('cres-gallery-img-wrap')
        );
    }

    isPosterElement(target) {
        const playButton = this.getSlideItem(this.index)
            .find('.cres-gallery-video-play-button')
            .get();
        return (
            target.hasClass('cres-gallery-video-poster') ||
            target.hasClass('cres-gallery-video-play-button') ||
            (playButton && playButton.contains(target.get()))
        );
    }

    /**
     * Maximize minimize inline gallery.
     * @category lGPublicMethods
     */
    toggleMaximize() {
        this.getElementById('cres-gallery-maximize').on('click.cres-gallery', () => {
            this.$container.toggleClass('cres-gallery-inline');
            this.refreshOnResize();
        });
    }

    invalidateItems() {
        for (let index = 0; index < this.items.length; index++) {
            const element = this.items[index];
            const $element = cresQuery(element);
            $element.off(
                `click.cres-gallerycustom-item-${$element.attr('data-cres-gallery-id')}`
            );
        }
    }

    trapFocus() {
        this.$container.get().focus({
            preventScroll: true
        });
        cresQuery(window).on(
            `keydown.cres-gallery.global${this.cresGalleryId}`,
            (e) => {
                if (!this.cresGalleryOpened) {
                    return;
                }

                const isTabPressed = e.key === 'Tab' || e.keyCode === 9;
                if (!isTabPressed) {
                    return;
                }
                const focusableEls = utils.getFocusableElements(
                    this.$container.get()
                );
                const firstFocusableEl = focusableEls[0];
                const lastFocusableEl = focusableEls[focusableEls.length - 1];

                if (e.shiftKey) {
                    if (document.activeElement === firstFocusableEl) {
                        lastFocusableEl.focus();
                        e.preventDefault();
                    }
                } else {
                    if (document.activeElement === lastFocusableEl) {
                        firstFocusableEl.focus();
                        e.preventDefault();
                    }
                }
            }
        );
    }

    manageCloseGallery() {
        if (!this.config.closable) return;
        let mousedown = false;
        this.getElementById('cres-gallery-close').on('click.cres-gallery', () => {
            this.closeGallery();
        });

        if (this.config.closeOnTap) {
            // If you drag the slide and release outside gallery gets close on chrome
            // for preventing this check mousedown and mouseup happened on .cres-gallery-item or cres-gallery-outer
            this.outer.on('mousedown.cres-gallery', (e) => {
                const target = cresQuery(e.target);
                if (this.isSlideElement(target)) {
                    mousedown = true;
                } else {
                    mousedown = false;
                }
            });

            this.outer.on('mousemove.cres-gallery', () => {
                mousedown = false;
            });

            this.outer.on('mouseup.cres-gallery', (e) => {
                const target = cresQuery(e.target);
                if (this.isSlideElement(target) && mousedown) {
                    if (!this.outer.hasClass('cres-gallery-dragging')) {
                        this.closeGallery();
                    }
                }
            });
        }
    }

    /**
     * Close cresGallery if it is opened.
     *
     * @description If closable is false in the settings, you need to pass true via closeGallery method to force close gallery
     * @return {number} returns the estimated time to close gallery completely including the close animation duration
     */
    closeGallery(force) {
        if (!this.cresGalleryOpened || (!this.config.closable && !force)) {
            return 0;
        }
        this.cresEl.trigger(galleryEvent.beforeClose);

        if (this.config.resetScrollPosition && !this.config.hideScrollbar) {
            cresQuery(window).scrollTop(this.prevScrollTop);
        }

        const currentItem = this.items[this.index];
        let transform;
        if (this.zoomFromOrigin && currentItem) {
            const { top, bottom } = this.mediaContainerPosition;
            const { __slideVideoInfo, poster } = this.galleryItems[this.index];
            const imageSize = utils.getSize(
                currentItem,
                this.outer,
                top + bottom,
                __slideVideoInfo && poster && this.config.videoMaxSize
            );
            transform = utils.getTransform(
                currentItem,
                this.outer,
                top,
                bottom,
                imageSize
            );
        }
        if (this.zoomFromOrigin && transform) {
            this.outer.addClass(
                'cres-gallery-closing cres-gallery-zoom-from-image'
            );
            this.getSlideItem(this.index)
                .addClass('cres-gallery-start-end-progress')
                .css(
                    'transition-duration',
                    this.config.startAnimationDuration + 'ms'
                )
                .css('transform', transform);
        } else {
            this.outer.addClass('cres-gallery-hide-items');
            // cres-gallery-zoom-from-image is used for setting the opacity to 1 if zoomFromOrigin is true
            // If the closing item doesn't have the cres-gallery-size attribute, remove this class to avoid the closing css conflicts
            this.outer.removeClass('cres-gallery-zoom-from-image');
        }

        // Unbind all events added by cresGallery
        // @todo
        //this.$el.off('.cres-gallery.tm');

        this.destroyModules();

        this.cresGalleryOn = false;
        this.isDummyImageRemoved = false;
        this.zoomFromOrigin = this.config.zoomFromOrigin;

        clearTimeout(this.hideBarTimeout);
        this.hideBarTimeout = false;
        cresQuery('html').removeClass('cres-gallery-on');

        this.outer.removeClass(
            'cres-gallery-visible cres-gallery-components-open'
        );

        // Resetting opacity to 0 isd required as  vertical swipe to close function adds inline opacity.
        this.$backdrop.removeClass('in').css('opacity', 0);

        const removeTimeout =
            this.zoomFromOrigin && transform
                ? Math.max(
                      this.config.startAnimationDuration,
                      this.config.backdropDuration
                  )
                : this.config.backdropDuration;
        this.$container.removeClass('cres-gallery-show-in');

        // Once the closign animation is completed and gallery is invisible
        setTimeout(() => {
            if (this.zoomFromOrigin && transform) {
                this.outer.removeClass('cres-gallery-zoom-from-image');
            }
            this.$container.removeClass('cres-gallery-show');

            // Reset scrollbar
            this.resetScrollBar();

            // Need to remove inline opacity as it is used in the stylesheet as well
            this.$backdrop
                .removeAttr('style')
                .css(
                    'transition-duration',
                    this.config.backdropDuration + 'ms'
                );

            this.outer.removeClass(
                `cres-gallery-closing ${this.config.startClass}`
            );

            this.getSlideItem(this.index).removeClass(
                'cres-gallery-start-end-progress'
            );
            this.$inner.empty();
            if (this.cresGalleryOpened) {
                this.cresEl.trigger(galleryEvent.afterClose, {
                    instance: this
                });
            }
            if (this.$container.get()) {
                this.$container.get().blur();
            }

            this.cresGalleryOpened = false;
        }, removeTimeout + 100);
        return removeTimeout + 100;
    }

    initModules() {
        this.plugins.forEach((module) => {

            module.init();

        });
    }

    /**
     *
     * @param {boolean} destroy
     */
    destroyModules(destroy) {
        this.plugins.forEach((module) => {
            try {
                if (destroy) {
                    module.destroy();
                } else {
                    module.closeGallery && module.closeGallery();
                }
            } catch (err) {
                console.warn(
                    `cresGallery:- make sure cresGallery module is properly destroyed`
                );
            }
        });
    }

    /**
     * @param {GalleryItem[]} galleryItems
     * @return {void}
     */
    refresh(galleryItems) {
        if (!this.config.dynamic) {
            this.invalidateItems();
        }
        if (galleryItems) {
            this.galleryItems = galleryItems;
        } else {
            this.galleryItems = this.getItems();
        }
        this.updateControls();
        this.openGalleryOnItemClick();
        this.cresEl.trigger(galleryEvent.updateSlides);
    }

    updateControls() {
        this.addSlideVideoInfo(this.galleryItems);
        this.updateCounterTotal();
        this.manageSingleSlideClassName();
    }

    destroy() {
        const closeTimeout = this.closeGallery(true);
        setTimeout(() => {
            this.destroyModules(true);
            if (!this.config.dynamic) {
                this.invalidateItems();
            }
            cresQuery(window).off(`.cres-gallery.global${this.cresGalleryId}`);
            this.cresEl.off('.cres-gallery');
            this.$container.remove();
        }, closeTimeout);
        return closeTimeout;
    }
}

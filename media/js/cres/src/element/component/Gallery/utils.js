import { cresQuery } from '../../../module/CresQuery';


const defaultDynamicOptions = [
    'src',
    'sources',
    'subHtml',
    'subHtmlUrl',
    'html',
    'video',
    'poster',
    'slideName',
    'responsive',
    'srcset',
    'sizes',
    'iframe',
    'downloadUrl',
    'download',
    'width',
    'facebookShareUrl',
    'tweetText',
    'iframeTitle',
    'twitterShareUrl',
    'pinterestShareUrl',
    'pinterestText',
    'fbHtml',
    'disqusIdentifier',
    'disqusUrl',
];


// Convert html data-attribute to camalcase
export function convertToData(attr) {
    // FInd a way for lgsize
    if (attr === 'href') {
        return 'src';
    }
    attr = attr.replace('data-', '');
    attr = attr.charAt(0).toLowerCase() + attr.slice(1);
    attr = attr.replace(/-([a-z])/g, (g) => g[1].toUpperCase());

    return attr;
}
const utils = {
    /**
     * get possible width and height from the lgSize attribute. Used for ZoomFromOrigin option
     */
    getSize(
        el,
        container,
        spacing = 0,
        defaultGallerySize,
    ) {
        const cresEl = cresQuery(el);
        let gallerySize = cresEl.attr('data-gallery-size') || defaultGallerySize;

        if (!gallerySize) {
            return;
        }

        const isResponsiveSizes = gallerySize.split(',');
        // if at-least two viewport sizes are available
        if (isResponsiveSizes[1]) {
            const wWidth = window.innerWidth;
            for (let i = 0; i < isResponsiveSizes.length; i++) {
                const size = isResponsiveSizes[i];
                const responsiveWidth = parseInt(size.split('-')[2], 10);
                if (responsiveWidth > wWidth) {
                    gallerySize = size;
                    break;
                }

                // take last item as last option
                if (i === isResponsiveSizes.length - 1) {
                    gallerySize = size;
                }
            }
        }

        const size = gallerySize.split('-');

        const width = parseInt(size[0], 10);
        const height = parseInt(size[1], 10);

        const cWidth = container.width();
        const cHeight = container.height() - spacing;

        const maxWidth = Math.min(cWidth, width);
        const maxHeight = Math.min(cHeight, height);

        const ratio = Math.min(maxWidth / width, maxHeight / height);

        return { width: width * ratio, height: height * ratio };
    },

    /**
     * @desc Get transform value based on the imageSize. Used for ZoomFromOrigin option
     * @param {jQuery Element}
     * @returns {String} Transform CSS string
     */
    getTransform(
        el,
        container,
        top,
        bottom,
        imageSize,
    ) {
        if (!imageSize) {
            return;
        }
        const cresEl = cresQuery(el).find('img').first();
        if (!cresEl.get()) {
            return;
        }

        const containerRect = container.get().getBoundingClientRect();

        const wWidth = containerRect.width;

        // using innerWidth to include mobile safari bottom bar
        const wHeight = container.height() - (top + bottom);

        const elWidth = cresEl.width();
        const elHeight = cresEl.height();

        const elStyle = cresEl.style();
        let x =
            (wWidth - elWidth) / 2 -
            cresEl.offset().left +
            (parseFloat(elStyle.paddingLeft) || 0) +
            (parseFloat(elStyle.borderLeft) || 0) +
            cresQuery(window).scrollLeft() +
            containerRect.left;
        let y =
            (wHeight - elHeight) / 2 -
            cresEl.offset().top +
            (parseFloat(elStyle.paddingTop) || 0) +
            (parseFloat(elStyle.borderTop) || 0) +
            cresQuery(window).scrollTop() +
            top;

        const scX = elWidth / imageSize.width;
        const scY = elHeight / imageSize.height;

        const transform =
            'translate3d(' +
            (x *= -1) +
            'px, ' +
            (y *= -1) +
            'px, 0) scale3d(' +
            scX +
            ', ' +
            scY +
            ', 1)';
        return transform;
    },

    getIframeMarkup(
        iframeWidth,
        iframeHeight,
        iframeMaxWidth,
        iframeMaxHeight,
        src,
        iframeTitle,
    ) {
        const title = iframeTitle ? 'title="' + iframeTitle + '"' : '';
        return `<div class="cres-gallery-video-cont cres-gallery-has-iframe" style="width:${iframeWidth}; max-width:${iframeMaxWidth}; height: ${iframeHeight}; max-height:${iframeMaxHeight}">
                    <iframe class="cres-gallery-object" frameborder="0" ${title} src="${src}"  allowfullscreen="true"></iframe>
                </div>`;
    },

    getImgMarkup(
        index,
        src,
        altAttr,
        srcset,
        sizes,
        sources,
    ) {
        const srcsetAttr = srcset ? `srcset="${srcset}"` : '';
        const sizesAttr = sizes ? `sizes="${sizes}"` : '';
        const imgMarkup = `<img ${altAttr} ${srcsetAttr}  ${sizesAttr} class="cres-gallery-object cres-gallery-image" data-index="${index}" src="${src}" />`;
        let sourceTag = '';
        if (sources) {
            const sourceObj =
                typeof sources === 'string' ? JSON.parse(sources) : sources;

            sourceTag = sourceObj.map((source) => {
                let attrs = '';
                Object.keys(source).forEach((key) => {
                    // Do not remove the first space as it is required to separate the attributes
                    attrs += ` ${key}="${source[key]}"`;
                });
                return `<source ${attrs}></source>`;
            });
        }
        return `${sourceTag}${imgMarkup}`;
    },

    // Get src from responsive src
    getResponsiveSrc(srcItms) {
        const rsWidth = [];
        const rsSrc = [];
        let src = '';
        for (let i = 0; i < srcItms.length; i++) {
            const _src = srcItms[i].split(' ');

            // Manage empty space
            if (_src[0] === '') {
                _src.splice(0, 1);
            }

            rsSrc.push(_src[0]);
            rsWidth.push(_src[1]);
        }

        const wWidth = window.innerWidth;
        for (let j = 0; j < rsWidth.length; j++) {
            if (parseInt(rsWidth[j], 10) > wWidth) {
                src = rsSrc[j];
                break;
            }
        }
        return src;
    },

    isImageLoaded(img) {
        if (!img) return false;
        // During the onload event, IE correctly identifies any images that
        // weren’t downloaded as not complete. Others should too. Gecko-based
        // browsers act like NS4 in that they report this incorrectly.
        if (!img.complete) {
            return false;
        }

        // However, they do have two very useful properties: naturalWidth and
        // naturalHeight. These give the true size of the image. If it failed
        // to load, either of these should be zero.
        if (img.naturalWidth === 0) {
            return false;
        }

        // No other way of checking: assume it’s ok.
        return true;
    },

    getVideoPosterMarkup(
        _poster,
        dummyImg,
        videoContStyle,
        playVideoString,
        _isVideo,
    ) {
        let videoClass = '';
        if (_isVideo && _isVideo.youtube) {
            videoClass = 'cres-gallery-has-youtube';
        } else if (_isVideo && _isVideo.vimeo) {
            videoClass = 'cres-gallery-has-vimeo';
        } else {
            videoClass = 'cres-gallery-has-html5';
        }

        return `<div class="cres-gallery-video-cont ${videoClass}" style="${videoContStyle}">
                <div class="cres-gallery-video-play-button">
                <svg
                    viewBox="0 0 20 20"
                    preserveAspectRatio="xMidYMid"
                    focusable="false"
                    aria-labelledby="${playVideoString}"
                    role="img"
                    class="cres-gallery-video-play-icon"
                >
                    <title>${playVideoString}</title>
                    <polygon class="cres-gallery-video-play-icon-inner" points="1,0 20,10 1,20"></polygon>
                </svg>
                <svg class="cres-gallery-video-play-icon-bg" viewBox="0 0 50 50" focusable="false">
                    <circle cx="50%" cy="50%" r="20"></circle></svg>
                <svg class="cres-gallery-video-play-icon-circle" viewBox="0 0 50 50" focusable="false">
                    <circle cx="50%" cy="50%" r="20"></circle>
                </svg>
            </div>
            ${dummyImg || ''}
            <img class="cres-gallery-object cres-gallery-video-poster" src="${_poster}" />
        </div>`;
    },

    getFocusableElements(container) {
        const elements = container.querySelectorAll(
            'a[href]:not([disabled]), button:not([disabled]), textarea:not([disabled]), input[type="text"]:not([disabled]), input[type="radio"]:not([disabled]), input[type="checkbox"]:not([disabled]), select:not([disabled])',
        );
        const visibleElements = [].filter.call(elements, (element) => {
            const style = window.getComputedStyle(element);
            return style.display !== 'none' && style.visibility !== 'hidden';
        });
        return visibleElements;
    },

    /**
     * @desc Create dynamic elements array from gallery items when dynamic option is false
     * It helps to avoid frequent DOM interaction
     * and avoid multiple checks for dynamic elments
     *
     * @returns {Array} dynamicEl
     */
    getDynamicOptions(
        items,
        extraProps,
        getCaptionFromTitleOrAlt,
        exThumbImage,
    ) {
        const dynamicElements = [];
        const availableDynamicOptions = [
            ...defaultDynamicOptions,
            ...extraProps,
        ];
        [].forEach.call(items, (item) => {
            const dynamicEl = {};
            for (let i = 0; i < item.attributes.length; i++) {
                const attr = item.attributes[i];
                if (attr.specified) {
                    const dynamicAttr = convertToData(attr.name);
                    let label = '';
                    if (availableDynamicOptions.indexOf(dynamicAttr) > -1) {
                        label = dynamicAttr;
                    }
                    if (label) {
                        dynamicEl[label] = attr.value;
                    }
                }
            }
            const currentItem = cresQuery(item);
            const alt = currentItem.find('img').first().attr('alt');
            const title = currentItem.attr('title');

            const thumb = exThumbImage
                ? currentItem.attr(exThumbImage)
                : currentItem.find('img').first().attr('src');
            dynamicEl.thumb = thumb;

            if (getCaptionFromTitleOrAlt && !dynamicEl.subHtml) {
                dynamicEl.subHtml = title || alt || '';
            }
            dynamicEl.alt = alt || title || '';
            dynamicElements.push(dynamicEl);
        });
        return dynamicElements;
    },
    isMobile() {
        return /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    },
    /**
     * @desc Check the given src is video
     * @param {String} src
     * @return {Object} video type
     * Ex:{ youtube  :  ["//www.youtube.com/watch?v=c0asJgSyxcY", "c0asJgSyxcY"] }
     *
     * @todo - this information can be moved to dynamicEl to avoid frequent calls
     */

    isVideo(
        src,
        isHTML5VIdeo,
        index,
    ) {
        if (!src) {
            if (isHTML5VIdeo) {
                return {
                    html5: true,
                };
            } else {
                console.error(
                    'cresGallery :- data-src is not provided on slide item ' +
                        (index + 1) +
                        '. Please make sure the selector property is properly configured',
                );
                return;
            }
        }

        const youtube = src.match(
            /\/\/(?:www\.)?youtu(?:\.be|be\.com|be-nocookie\.com)\/(?:watch\?v=|embed\/)?([a-z0-9\-\_\%]+)([\&|?][\S]*)*/i,
        );
        const vimeo = src.match(
            /\/\/(?:www\.)?(?:player\.)?vimeo.com\/(?:video\/)?([0-9a-z\-_]+)(.*)?/i,
        );
        const wistia = src.match(
            /https?:\/\/(.+)?(wistia\.com|wi\.st)\/(medias|embed)\/([0-9a-z\-_]+)(.*)/,
        );

        if (youtube) {
            return {
                youtube,
            };
        } else if (vimeo) {
            return {
                vimeo,
            };
        } else if (wistia) {
            return {
                wistia,
            };
        }
    },
};

export default utils;

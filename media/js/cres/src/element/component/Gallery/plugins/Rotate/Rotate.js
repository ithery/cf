import { cresQuery } from "../../../../../module/CresQuery";
import { galleryEvent } from "../../event";
import { rotateConfig } from "./config";

export default class Rotate {
    constructor(gallery) {
        // get lightGallery core plugin instance
        this.gallery = gallery;
        this.config = {
            ...rotateConfig,
            ...this.gallery.config
        }
        this.rotateValuesList = {};
    }
    buildTemplates() {
        let rotateIcons = '';
        if (this.config.flipVertical) {
            rotateIcons += `<button type="button" id="cres-gallery-flip-ver" aria-label="${this.config.rotatePluginStrings['flipVertical']}" class="cres-gallery-flip-ver cres-gallery-icon"></button>`;
        }
        if (this.config.flipHorizontal) {
            rotateIcons += `<button type="button" id="cres-gallery-flip-hor" aria-label="${this.config.rotatePluginStrings['flipHorizontal']}" class="cres-gallery-flip-hor cres-gallery-icon"></button>`;
        }
        if (this.config.rotateLeft) {
            rotateIcons += `<button type="button" id="cres-gallery-rotate-left" aria-label="${this.config.rotatePluginStrings['rotateLeft']}" class="cres-gallery-rotate-left cres-gallery-icon"></button>`;
        }
        if (this.config.rotateRight) {
            rotateIcons += `<button type="button" id="cres-gallery-rotate-right" aria-label="${this.config.rotatePluginStrings['rotateRight']}" class="cres-gallery-rotate-right cres-gallery-icon"></button>`;
        }
        this.gallery.$toolbar.append(rotateIcons);
    }

    init() {
        if (!this.config.rotate) {
            return;
        }
        this.buildTemplates();
        // Save rotate config for each item to persist its rotate, flip values
        // even after navigating to diferent slides
        this.rotateValuesList = {};
        // event triggered after appending slide content
        this.gallery.cresEl.on(`${galleryEvent.slideItemLoad}.rotate`, (event) => {
            const { index } = event.detail;

            const rotateEl = this.gallery
                .getSlideItem(index)
                .find('.cres-gallery-img-rotate')
                .get();
            if (!rotateEl) {
                const imageWrap = this.gallery
                    .getSlideItem(index)
                    .find('.cres-gallery-object')
                    .first();

                imageWrap.wrap('cres-gallery-img-rotate');
                //this.rotateValuesList[this.gallery.index]
                this.gallery
                    .getSlideItem(this.gallery.index)
                    .find('.cres-gallery-img-rotate')
                    .css(
                        'transition-duration',
                        this.config.rotateSpeed + 'ms',
                    );
            }
        });

        this.gallery.outer
            .find('#cres-gallery-rotate-left')
            .first()
            .on('click.cres-gallery', this.rotateLeft.bind(this));

        this.gallery.outer
            .find('#cres-gallery-rotate-right')
            .first()
            .on('click.cres-gallery', this.rotateRight.bind(this));

        this.gallery.outer
            .find('#cres-gallery-flip-hor')
            .first()
            .on('click.cres-gallery', this.flipHorizontal.bind(this));

        this.gallery.outer
            .find('#cres-gallery-flip-ver')
            .first()
            .on('click.cres-gallery', this.flipVertical.bind(this));

        // Reset rotate on slide change
        this.gallery.cresEl.on(`${galleryEvent.beforeSlide}.rotate`, (event) => {
            if (!this.rotateValuesList[event.detail.index]) {
                this.rotateValuesList[event.detail.index] = {
                    rotate: 0,
                    flipHorizontal: 1,
                    flipVertical: 1,
                };
            }
        });
    }

    applyStyles() {
        const $image = this.gallery
            .getSlideItem(this.gallery.index)
            .find('.cres-gallery-img-rotate')
            .first();

        $image.css(
            'transform',
            'rotate(' +
                this.rotateValuesList[this.gallery.index].rotate +
                'deg)' +
                ' scale3d(' +
                this.rotateValuesList[this.gallery.index].flipHorizontal +
                ', ' +
                this.rotateValuesList[this.gallery.index].flipVertical +
                ', 1)',
        );
    }
    rotateLeft() {
        this.rotateValuesList[this.gallery.index].rotate -= 90;
        this.applyStyles();
        this.triggerEvents(galleryEvent.rotateLeft, {
            rotate: this.rotateValuesList[this.gallery.index].rotate,
        });
    }

    rotateRight() {
        this.rotateValuesList[this.gallery.index].rotate += 90;
        this.applyStyles();
        this.triggerEvents(galleryEvent.rotateRight, {
            rotate: this.rotateValuesList[this.gallery.index].rotate,
        });
    }
    getCurrentRotation(el) {
        if (!el) {
            return 0;
        }
        const st = cresQuery(el).style();
        const tm =
            st.getPropertyValue('-webkit-transform') ||
            st.getPropertyValue('-moz-transform') ||
            st.getPropertyValue('-ms-transform') ||
            st.getPropertyValue('-o-transform') ||
            st.getPropertyValue('transform') ||
            'none';
        if (tm !== 'none') {
            const values = tm.split('(')[1].split(')')[0].split(',');
            if (values) {
                const angle = Math.round(
                    Math.atan2(values[1], values[0]) * (180 / Math.PI),
                );
                return angle < 0 ? angle + 360 : angle;
            }
        }
        return 0;
    }
    flipHorizontal() {
        const rotateEl = this.gallery
            .getSlideItem(this.gallery.index)
            .find('.cres-gallery-img-rotate')
            .first()
            .get();
        const currentRotation = this.getCurrentRotation(rotateEl);
        let rotateAxis = 'flipHorizontal';
        if (currentRotation === 90 || currentRotation === 270) {
            rotateAxis = 'flipVertical';
        }
        this.rotateValuesList[this.gallery.index][rotateAxis] *= -1;
        this.applyStyles();
        this.triggerEvents(galleryEvent.flipHorizontal, {
            flipHorizontal: this.rotateValuesList[this.gallery.index][rotateAxis],
        });
    }
    flipVertical() {
        const rotateEl = this.gallery
            .getSlideItem(this.gallery.index)
            .find('.cres-gallery-img-rotate')
            .first()
            .get();
        const currentRotation = this.getCurrentRotation(rotateEl);
        let rotateAxis = 'flipVertical';
        if (currentRotation === 90 || currentRotation === 270) {
            rotateAxis = 'flipHorizontal';
        }
        this.rotateValuesList[this.gallery.index][rotateAxis] *= -1;

        this.applyStyles();

        this.triggerEvents(galleryEvent.flipVertical, {
            flipVertical: this.rotateValuesList[this.gallery.index][rotateAxis],
        });
    }

    triggerEvents(event, detail) {
        setTimeout(() => {
            this.gallery.cresEl.trigger(event, detail);
        }, this.config.rotateSpeed + 10);
    }

    isImageOrientationChanged() {
        const rotateValue = this.rotateValuesList[this.gallery.index];
        const isRotated = Math.abs(rotateValue.rotate) % 360 !== 0;
        const ifFlippedHor = rotateValue.flipHorizontal < 0;
        const ifFlippedVer = rotateValue.flipVertical < 0;
        return isRotated || ifFlippedHor || ifFlippedVer;
    }

    closeGallery() {
        if (this.isImageOrientationChanged()) {
            this.gallery.getSlideItem(this.gallery.index).css('opacity', 0);
        }
        this.rotateValuesList = {};
    }

    destroy() {
        // Unbind all events added by cresGallery rotate plugin
        this.gallery.cresEl.off('.cres-gallery.rotate');
        this.gallery.cresEl.off('.rotate');
    }
}

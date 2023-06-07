import { easeInOutQuad } from './math';

const scrollTo = function (final, duration, cb, scrollEl) {
    let element = scrollEl || window;
    let start = element.scrollTop || document.documentElement.scrollTop,
        currentTime = null;

    if (!scrollEl) {start = window.scrollY || document.documentElement.scrollTop;}

    let animateScroll = function (timestamp) {
        if (!currentTime) {currentTime = timestamp;}
        let progress = timestamp - currentTime;
        if (progress > duration) {progress = duration;}
        let val = easeInOutQuad(progress, start, final - start, duration);
        element.scrollTo(0, val);
        if (progress < duration) {
            window.requestAnimationFrame(animateScroll);
        } else {
            cb && cb();
        }
    };

    window.requestAnimationFrame(animateScroll);
};

export default scrollTo;

import * as mathFunction from './math';
/* Animate height of an element */
const setHeight = function (start, to, element, duration, cb, timeFunction) {
    let change = to - start,
        currentTime = null;

    let animateHeight = function (timestamp) {
        if (!currentTime) {currentTime = timestamp;}
        let progress = timestamp - currentTime;
        if (progress > duration) {progress = duration;}
        let val = parseInt((progress / duration) * change + start);
        if (timeFunction) {
            val = mathFunction[timeFunction](progress, start, to - start, duration);
        }
        element.style.height = val + 'px';
        if (progress < duration) {
            window.requestAnimationFrame(animateHeight);
        } else {
            if (cb) {cb();}
        }
    };

    //set the height of the element before starting animation -> fix bug on Safari
    element.style.height = start + 'px';
    window.requestAnimationFrame(animateHeight);
};

export default setHeight;

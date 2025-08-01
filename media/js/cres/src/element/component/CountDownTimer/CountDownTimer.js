/**
 * @class CountDownTimer
 */
export default class CountDownTimer {
    constructor(className, config = {}) {
        // all html elements
        const defaultConfig = {
            autoStart: true,
            expiredText: 'Expired',
            timerInterval: 1000,
            displayFormat: '%DD:%HH:%mm:%ss'
        };
        this.elements =
            className instanceof Element
                ? [className]
                : [].slice.call(document.querySelectorAll(className));
        if (this.elements.length < 1) {
            return;
        }
        this.element = this.elements[0];
        const cresConfig = JSON.parse(this.element.getAttribute('cres-config'));
        this.config = { ...defaultConfig, ...config, ...cresConfig };
        this.countdownInterval = null;
        this.displayFormat = this.config.displayFormat;
        this.defaultDisplay = (days, hours, minutes, seconds) => {
            let format = this.displayFormat;
            let haveDay = format.includes('%DD') || format.includes('%D');
            let haveHour = format.includes('%HH') || format.includes('%H');
            let haveMinute = format.includes('%mm') || format.includes('%m');
            let haveSecond = format.includes('%ss') || format.includes('%s');

            let isPadDay = format.includes('%DD');
            let isPadHour = format.includes('%HH');
            let isPadMinute = format.includes('%mm');
            let isPadSecond = format.includes('%ss');

            if(!haveDay) {
                hours += days * 24;
            }
            if(!haveHour) {
                minutes += hours * 60;
            }
            if(!haveMinute) {
                seconds += minutes * 60;
            }
            let string = '';
            let daysString = '' + days;
            let hoursString = '' + hours;
            let minutesString = '' + minutes;
            let secondsString = '' + seconds;
            if(isPadDay) {
                daysString = daysString.padStart(2, '0');
            }
            if(isPadHour) {
                hoursString = hoursString.padStart(2, '0');
            }
            if(isPadMinute) {
                minutesString = minutesString.padStart(2, '0');
            }
            if(isPadSecond) {
                secondsString = secondsString.padStart(2, '0');
            }
            string = format
                .replace('%DD', daysString)
                .replace('%D', days)
                .replace('%HH', hoursString)
                .replace('%H', hours)
                .replace('%mm', minutesString)
                .replace('%m', minutes)
                .replace('%ss', secondsString)
                .replace('%s', seconds);
            return string;
        };
        this.timestamp = parseInt(this.config.timestamp);
        this.expiredText = this.config.expiredText;
        this.displayCallback = this.config.displayCallback;
        this.autoStart = this.config.autoStart;
        this.timerInterval = this.config.timerInterval;
        if(this.autoStart) {
            this.startTimer();
        } else {
            this.refreshDisplay();
        }
    }
    startTimer() {
        this.countdownInterval = setInterval(() => {
            this.refreshDisplay();
        }, this.timerInterval ?? 1000); // 1000 milliseconds = 1 second
    }
    stopTimer() {
        clearInterval(this.countdownInterval);
    }
    refreshDisplay() {
        // Get the current date and time
        let now = new Date().getTime();

        // Calculate the difference between now and the countdown date
        let distance = this.timestamp - now;

        // Calculate the days, hours, minutes and seconds remaining
        let days = Math.floor(distance / (1000 * 60 * 60 * 24));
        let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((distance % (1000 * 60)) / 1000);

        let displayCallbackFunction = this.displayCallback || this.defaultDisplay;
        // Output the countdown
        this.element.innerHTML = displayCallbackFunction(days, hours, minutes, seconds);

        // If the countdown is finished, stop the countdown interval
        if (distance < 0) {
            clearInterval(this.countdownInterval);
            this.element.innerHTML = this.expiredText;
        }
    }
}

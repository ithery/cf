import { fadeOut } from "./animation";

const removePreloader = (callback) =>{
    let preloader = document.getElementById('cres-preloader');
    if(preloader) {
        fadeOut(preloader).then(()=>{
            preloader.remove();
            if(callback) {
                callback();
            }

        });
    } else {
        if(callback) {
            callback();
        }
    }
};

export default removePreloader;

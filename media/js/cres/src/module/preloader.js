import { fadeOut } from "./animation";

const removePreloader = () =>{
    let preloader = document.getElementById('cres-preloader');
    if(preloader) {
        fadeOut(preloader).then(()=>preloader.remove());
    }
};

export default removePreloader;

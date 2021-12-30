const removePreloader = () =>{
    let preloader = document.querySelector('#capp-preloader');
    if(preloader) {
        preloader.style.display = 'none';
    }
};

export default removePreloader;

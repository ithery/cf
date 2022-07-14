const removePreloader = () =>{
    let preloader = document.getElementById('capp-preloader');
    if(preloader) {
        preloader.style.display = 'none';
    }
};

export default removePreloader;

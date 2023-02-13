
class CresenityDemo {
    constructor() {
        this.init();
    }

    init() {
        this.initButtonShowCode();
    }

    initButtonShowCode() {
        window.addEventListener('cresenity:loaded', ()=>{
            const btnDemo = document.getElementById('btn-demo-show-code');
            if(btnDemo) {
                // eslint-disable-next-line no-unused-vars
                btnDemo.addEventListener('click', (e)=>{
                    const url = window.capp.baseUrl + 'demo/code/show?uri='+ btnDemo.getAttribute('data-uri');
                    window.cresenity.modal({
                        title: 'Code',
                        isSidebar: true,
                        modalClass: 'modal-large',
                        backdrop: true,
                        reload: {
                            url: url
                        }
                    });
                });
            }
        });
    }
}


window.cresenityDemo = new CresenityDemo();

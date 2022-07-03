
class CresenityDemo {
    constructor() {
        this.init();
    }

    init() {
        this.initButtonShowCode();
    }

    initButtonShowCode() {
        window.addEventListener('cresenity:loaded',()=>{
            const btnDemo = document.getElementById('btn-demo-show-code');
            if(btnDemo) {
                btnDemo.addEventListener('click',(e)=>{
                    const url = capp.baseUrl + 'demo/code/show?uri='+ btnDemo.getAttribute('data-uri')
                    cresenity.modal({
                        title:'Code',
                        isSidebar:true,
                        modalClass:'modal-large',
                        reload: {
                            url:url
                        }
                    })
                });
            }
        })
    }
}


window.cresenityDemo = new CresenityDemo();

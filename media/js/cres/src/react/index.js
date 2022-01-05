

class CresReact {
    constructor() {

    }

    createTestComponent(container) {
        window.ReactDOM.render(
            <div>halo</div>,
            container
        );
    }
}


const cresReact = new CresReact();

export default cresReact;




const pwaInstall = () => {
    let defferedPrompt;
    window.addEventListener('beforeinstallprompt', event => {
        event.preventDefault();
        defferedPrompt = event
    });

    defferedPrompt.prompt();
    defferedPrompt.userChoice.then(choice => {
        if(choice.outcome === 'accepted'){
            console.log('user accepted the prompt')
        }
        defferedPrompt = null;
    })
}

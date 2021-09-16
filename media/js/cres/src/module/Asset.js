export default class Asset {
    constructor() {
        this.filesAdded = [];
    }
    removeJsCss(filename, filetype) {
        //determine element type to create nodelist from
        let targetelement = (filetype == 'js') ? 'script' : (filetype == 'css') ? 'link' : 'none';
        //determine corresponding attribute to test for
        let targetattr = (filetype == 'js') ? 'src' : (filetype == 'css') ? 'href' : 'none';
        let allsuspects = document.getElementsByTagName(targetelement);
        //search backwards within nodelist for matching elements to remove
        for (let i = allsuspects.length; i >= 0; i--) {
            if (allsuspects[i] && allsuspects[i].getAttribute(targetattr) != null && allsuspects[i].getAttribute(targetattr).indexOf(filename) != -1) {
                //remove element by calling parentNode.removeChild()
                allsuspects[i].parentNode.removeChild(allsuspects[i]);
            }
        }
    }
}

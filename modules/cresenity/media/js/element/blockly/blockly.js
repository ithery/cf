/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var CBlockly = function (options) {
    this.settings = $.extend({

    }, options);
    this.mediaFolder = this.settings.mediaFolder;
    this.toolboxElementId = this.settings.toolboxElementId;

    this.blocklyElementId = this.settings.blocklyElementId;
    this.workspace = null;


    this.inited = function () {
        this.workspace !== null
    }
    this.createVariable = function (variable) {
        this.workspace.createVariable(variable);

    };

    this.init = function () {
        if (this.inited()) {
            return;
        }
        this.workspace = Blockly.inject(this.blocklyElementId, {
            grid: {
                spacing: 25,
                length: 3,
                colour: '#ccc',
                snap: true
            },
            media: this.mediaFolder,
            toolbox: this.toolboxElementId,
            //toolboxPosition: 'left',
            //horizontalLayout: true,
            //scrollbars: true,
        });
    };

    
    
    this.init();

}
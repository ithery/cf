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

    this.save = function (event) {
        var code = this.getPhp();
        var saveUrl = this.settings.saveUrl;



        $.ajax({
            url:saveUrl,
            type:'post',
            data: {
                code:code,
            },
            success: function(response) {
                alert(response);
            }
        })
        

    }


    this.inited = function () {
        this.workspace !== null
    }
    this.createVariable = function (variable) {
        this.workspace.createVariable(variable);

    };


    this.getXml = function () {

        var xmlDom = Blockly.Xml.workspaceToDom(this.workspace);
        var xmlText = Blockly.Xml.domToPrettyText(xmlDom);
        return xmlText;
    }

    this.getPhp = function () {
        var generator = Blockly.PHP;
        //var xml = this.getXml()

        if (this.checkAllGeneratorFunctionsDefined(generator)) {
            var code = generator.workspaceToCode(this.workspace);
            return code;
        }
        return null;
    }

    this.checkAllGeneratorFunctionsDefined = function (generator) {
        var blocks = this.workspace.getAllBlocks(false);
        var missingBlockGenerators = [];
        for (var i = 0; i < blocks.length; i++) {
            var blockType = blocks[i].type;
            if (!generator[blockType]) {
                if (missingBlockGenerators.indexOf(blockType) == -1) {
                    missingBlockGenerators.push(blockType);
                }
            }
        }

        var valid = missingBlockGenerators.length == 0;
        if (!valid) {
            var msg = 'The generator code for the following blocks not specified for ' +
                    generator.name_ + ':\n - ' + missingBlockGenerators.join('\n - ');
            this.alert(msg);  // Assuming synchronous. No callback.
        }
        return valid;
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
            toolbox: document.getElementById(this.toolboxElementId),
            //toolboxPosition: 'left',
            //horizontalLayout: true,
            //scrollbars: true,
        });
        if (typeof this.settings.saveElementId !== 'undefined') {
            document.getElementById(this.settings.saveElementId).addEventListener('click', (event) => {
                this.save(event);

            }, true);
        }

        if (typeof this.settings.variables !== 'undefined') {
            this.settings.variables.forEach((item) => this.createVariable(item));
        }

        if (typeof this.settings.defaultXml !== 'undefined') {
            var xml = Blockly.Xml.textToDom(this.settings.defaultXml);
            Blockly.Xml.domToWorkspace(xml, this.workspace);
        }


    };



    this.init();
    window.bworkspace = this.workspace;
}
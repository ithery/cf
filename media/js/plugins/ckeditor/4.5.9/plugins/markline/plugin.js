CKEDITOR.plugins.add('markline', {
    icons: 'markline',
    hidpi: true,
    init: function (editor) {
        editor.addCommand('insertMarkLine', {
            exec: function (editor, data) {
                // get selected text
                var selection = editor.getSelection();
                var selText = selection.getSelectedText();
                var newText = selText.split("\n").join("<br/>");
                
                editor.config.markLinePath = editor.config.markLinePath || ( CKEDITOR.basePath + 'plugins/markLine/images/' );
                
                // create left bracket image html
                var styleCSS = '';
                styleCSS += 'height: 100%;';
                var leftBracketImage = '<img src="' + editor.config.markLinePath + 'left_bracket.png" style="' + styleCSS+ '"/>';
                
                // create div for left bracket
                var imgWrapCSS = '';
                imgWrapCSS += 'position: absolute;';
                imgWrapCSS += 'top: 0px;';
                imgWrapCSS += 'left: -13px;';
                imgWrapCSS += 'height: 100%;';
                var imgWrapper = '<div style="' + imgWrapCSS + '">';
                imgWrapper += leftBracketImage;
                imgWrapper += '</div>';
                
                // generate new html, combine left bracket and old htlm or selection text
                var newHtmlCSS = '';
                newHtmlCSS += 'position: relative;';
                var newHtml = '<div style="' + newHtmlCSS + '">';
                newHtml += imgWrapper;
                newHtml += newText;
                newHtml += '</div>';
                editor.insertHtml(newHtml);
            }
        });
        editor.ui.addButton('Markline', {
            label: 'Insert Makrline',
            command: 'insertMarkLine',
            toolbar: 'paragraph'
        });
    },
    buildCSS: function(){
        
    }
});
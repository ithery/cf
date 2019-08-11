<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 7, 2018, 7:50:27 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_PrismCode extends CElement_Component {

    protected $prismLanguage = 'php';
    protected $prismTheme = 'okaidia';
    protected $codeElement;
    protected $haveCopyToClipboard;
    protected $haveSelectCode;

    public function __construct($id = "", $tag = "div") {
        parent::__construct($id, $tag);
        $this->tag = 'pre';
        $this->codeElement = $this->addCode();
        $this->wrapper = $this->codeElement;
        $this->haveIndent = false;
    }

    public function setLanguage($lang) {
        $this->prismLanguage = $lang;
        return $this;
    }

    public function setTheme($theme) {
        $this->prismTheme = $theme;
        return $this;
    }

    protected function build() {
        $cs = CManager::clientScript();
        $cs->registerJsFile('plugins/prism/prism.min.js');
        $cs->registerJsFile('plugins/prism/plugins/prism-toolbar.js');
        $cs->registerJsFile('plugins/prism/components/prism-' . $this->prismLanguage . '.js');
        $cs->registerCssFile('plugins/prism/themes/prism-' . $this->prismTheme . '.css');
        $cs->registerCssFile('plugins/prism/plugins/prism-toolbar.css');
        $this->codeElement->addClass('language-' . $this->prismLanguage);
    }

    public function setHaveCopyToClipboard($bool = true) {
        $this->haveCopyToClipboard = $bool;
        return $this;
    }
     public function setHaveSelectCode($bool = true) {
        $this->haveSelectCode = $bool;
        return $this;
    }

    public function js($indent = 0) {
        $js = '';

        if ($this->haveSelectCode) {
            $js .= "Prism.plugins.toolbar.registerButton('select-code', function(env) {
                var button = document.createElement('button');
                button.innerHTML = 'Select Code';

                button.addEventListener('click', function () {
                        // Source: http://stackoverflow.com/a/11128179/2757940
                        if (document.body.createTextRange) { // ms
                                var range = document.body.createTextRange();
                                range.moveToElementText(env.element);
                                range.select();
                        } else if (window.getSelection) { // moz, opera, webkit
                                var selection = window.getSelection();
                                var range = document.createRange();
                                range.selectNodeContents(env.element);
                                selection.removeAllRanges();
                                selection.addRange(range);
                        }
                });

                return button;
            });";
        }
        if ($this->haveCopyToClipboard) {
            CManager::registerModule('clipboard');
            $js .= "Prism.plugins.toolbar.registerButton('copy-to-clipboard', function (env) {
		var linkCopy = document.createElement('button');
		linkCopy.textContent = 'Copy';

		if (!ClipboardJS) {
			callbacks.push(registerClipboard);
		} else {
			registerClipboard();
		}

		return linkCopy;

		function registerClipboard() {
			var clip = new ClipboardJS(linkCopy, {
				'text': function () {
					return env.code;
				}
			});

			clip.on('success', function() {
				linkCopy.textContent = 'Copied!';

				resetText();
			});
			clip.on('error', function () {
				linkCopy.textContent = 'Press Ctrl+C to copy';

				resetText();
			});
		}

		function resetText() {
			setTimeout(function () {
				linkCopy.textContent = 'Copy';
			}, 5000);
		}
            });";
        }
        $js .= 'Prism.highlightAll();';
        return $js;
    }

}

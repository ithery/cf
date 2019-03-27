<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 27, 2019, 12:44:24 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_ElFinder extends CElement_Component {

    public function __construct($id = "", $tag = "div") {
        parent::__construct($id, $tag);

        $this->tag = 'div';
    }

    public function js($indent = 0) {
        $js = "define('elFinderConfig', {
                // elFinder options (REQUIRED)
                // Documentation for client options:
                // https://github.com/Studio-42/elFinder/wiki/Client-configuration-options
                defaultOpts : {
                    url : 'php/connector.minimal.php' // connector URL (REQUIRED)
                    // bootCalback calls at before elFinder boot up 
                    ,bootCallback : function(fm, extraObj) {
                        /* any bind functions etc. */
                        fm.bind('init', function() {
                                // any your code
                        });
                        // for example set document.title dynamically.
                        var title = document.title;
                        fm.bind('open', function() {
                            var path = '',
                                cwd  = fm.cwd();
                            if (cwd) {
                                path = fm.path(cwd.hash) || null;
                            }
                            document.title = path? path + ':' + title : title;
                        }).bind('destroy', function() {
                            document.title = title;
                        });
                    }
                },
                managers : {
                    // 'DOM Element ID': { /* elFinder options of this DOM Element */ }
                    'elfinder': {}
                }
        });";
        return $js;
    }

}

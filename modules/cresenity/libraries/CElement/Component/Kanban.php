<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 3, 2019, 1:43:39 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_Kanban extends CElement_Component {

    public function __construct($id) {
        parent::__construct($id);
        $this->addClass('form-row')->addClass('kanban');
        CManager::registerModule('dragula');
    }

    public function addList($id = "") {
        $wrapperList = $this->addDiv()->addClass('col-md');
        $list = CElement_Factory::createComponent('Kanban_List', $id);
        $list->addClass('mb-3');
        $wrapperList->add($list);
        return $list;
    }

    public function build() {
        
    }

    public function js($indent = 0) {
        
        
        
        $js = "
            $(function() {

                // Drag&Drop

                var drake = dragula(
                    Array.prototype.slice.call(document.querySelectorAll('.kanban-box'))
                );
                drake.on('drop',function(e){
                
                });
            });
        ";
        return $js;
    }

}

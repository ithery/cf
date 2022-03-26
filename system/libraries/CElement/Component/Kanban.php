<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 3, 2019, 1:43:39 AM
 */
class CElement_Component_Kanban extends CElement_Component {
    protected $saveCallback;

    protected $saveCallbackRequire;

    public function __construct($id) {
        parent::__construct($id);
        $this->addClass('form-row')->addClass('kanban');
        CManager::registerModule('dragula');
    }

    public function addList($id = '') {
        $wrapperList = $this->addDiv()->addClass('col-md');
        $list = CElement_Factory::createComponent('Kanban_List', $id);
        $list->addClass('mb-3');
        $wrapperList->add($list);

        return $list;
    }

    public function setSaveCallback($callback, $require = '') {
        $this->saveCallback = c::toSerializableClosure($callback);
        $this->saveCallbackRequire = $require;

        return $this;
    }

    public function build() {
    }

    public function js($indent = 0) {
        $saveUrl = '';
        if ($this->saveCallback != null) {
            $wrapperCallback = function () {
                $args = func_get_args();
                $errCode = 0;
                $errMessage = '';

                try {
                    $result = CFunction::factory(carr::get($args, 2))->setRequire(carr::get($args, 1))->setArgs($args)->execute();
                } catch (Exception $ex) {
                    $errCode++;
                    $errMessage = $ex->getMessage();
                }
                if (!$result) {
                    //return error with no message for just cancel dracula event
                    $errCode++;
                }

                return json_encode([
                    'errCode' => $errCode,
                    'errMessage' => $errMessage,
                ]);
            };
            $ajaxMethod = CAjax::createMethod();
            $ajaxMethod->setType('Callback');
            $ajaxMethod->setData('callable', serialize(c::toSerializableClosure($wrapperCallback)));
            $ajaxMethod->setData('requires', $this->saveCallbackRequire);
            $ajaxMethod->setData('saveCallback', serialize($this->saveCallback));
            $ajaxMethod->setData('method', 'post');
            $ajaxMethod->setData('parameter', ['itemId', 'fromContainerId', 'toContainerId', 'dataItemFromOrder', 'dataItemToOrder']);

            $saveUrl = $ajaxMethod->makeUrl();
        }

        $saveJs = '';
        if ($saveUrl != null) {
            $saveJs = "
                drake.on('drop',function(el, target, source, sibling){
                    var itemId = $(el).attr('id');
                    var toContainerId = $(target).closest('.widget-box').attr('id');
                    var fromContainerId = $(source).closest('.widget-box').attr('id');

                    var dataItemToOrder = [];
                    $(target).find('.list-group-item').each(function () {
                        dataItemToOrder.push($(this).attr('id'));
                    });
                    var dataItemFromOrder = [];
                    $(source).find('.list-group-item').each(function () {
                        dataItemFromOrder.push($(this).attr('id'));
                    });

                    var dataPost={};
                    dataPost.itemId=itemId;
                    dataPost.toContainerId=toContainerId;
                    dataPost.fromContainerId=fromContainerId;
                    dataPost.dataItemToOrder=dataItemToOrder;
                    dataPost.dataItemFromOrder=dataItemFromOrder;

                    var container = $(target).closest('.kanban');
                    (function(container){
                        cresenity.blockElement(container);
                        $.ajax({
                            method:'post',
                            data:dataPost,
                            dataType:'json',
                            url:'" . $saveUrl . "',
                            complete:function() {
                                cresenity.unblockElement(container);
                            },
                            success:function(data) {
                                console.log(data);

                                if(data.errCode>0) {
                                    drake.cancel();
                                    if(data.errMessage.length>0) {
                                        cresenity.message('error',data.errMessage);
                                    }
                                }
                            },
                            error:function() {
                                console.log('kanban save error');
                                drake.cancel(true);
                            }
                        });
                    })(container);


                });
                ";
        }

        $js = "
            $(function() {

                // Drag&Drop

                var drake = dragula(
                    Array.prototype.slice.call(document.querySelectorAll('.kanban-box')),{
                        invalid: function (el, handle) {
                            //return el.tagName === 'A';
                            return false;
                        }
                    }
                );
                (function(drake) {
                " . $saveJs . '
                })(drake)
            });
        ';
        $js .= parent::jsChild($indent);

        return $js;
    }
}

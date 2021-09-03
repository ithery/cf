<?php

class CElement_Tree extends CElement {
    protected $url;
    protected $target;
    protected $data = [];
    protected $callback;
    protected $requires;
    protected $custom_field_data = [];

    public function __construct($id = '') {
        parent::__construct($id);

        $this->url = null;
        $this->callback = null;
        $this->requires = [];
        CClientModules::instance()->register_module('jstree');
    }

    public static function factory($id = '') {
        return new CElement_Tree($id);
    }

    public function set_data($data) {
        $this->data = $data;
        return $this;
    }

    public function set_custom_field_data($custom_field_data) {
        $this->custom_field_data = $custom_field_data;
        return $this;
    }

    public function set_target($target) {
        $this->target = $target;
        return $this;
    }

    public function set_url($url) {
        $this->url = $url;
        return $this;
    }

    public function set_callback(callable $callback, $require = null) {
        $this->callback = $callback;
        if ($require != null) {
            if (!is_array($require)) {
                $require = [$require];
            }
            foreach ($require as $req) {
                $this->requires[] = $req;
            }
        }
        return $this;
    }

    public function render($data) {
        $html = new CStringBuilder();

        if (count($data) > 0) {
            foreach ($data as $data_k => $data_v) {
                $node = carr::get($data_v, 'node');
                $label = carr::get($data_v, 'label');
                $html->appendln('<ul>');
                $html->append('<li>');
                $html->append($label);
                if ($node == 'root') {
                    $child = carr::get($data_v, 'child');
                    $html->appendln($this->render($child));
                }
                $html->append('</li>');
                $html->appendln('</ul>');
            }
        }
        return $html->text();
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();

        $html->appendln('<div id="' . $this->id . '" class="row">');
        $html->appendln('<div class="tree col-md-4"></div>');
        $html->appendln('
            <div id="data" class="col-md-8">
                <div class="content code" style="display:none;"><textarea class="col-md-12" rows="30" id="code" readonly="readonly"></textarea></div>
                <div class="content folder" style="display:none;"></div>
                <div class="content image" style="display:none; position:relative;"><img src="" alt="" style="display:block; position:absolute; left:50%; top:50%; padding:0; max-height:90%; max-width:90%;" /></div>
                <div class="content default" style="text-align:center;">Select a node from the tree.</div>
            </div>');
//            $html->appendln($this->render($this->data));
//                    '<ul>
//                          <li>Root node 1
//                            <ul>
//                              <li>Child node 1</li>
//                              <li><a href="#">Child node 2</a></li>
//                            </ul>
//                          </li>
//                        </ul>
        $html->appendln('</div>');

        $html->appendln('
            <style>
                .tree {
                    overflow-x: scroll;
                    background-color: rgba(204, 204, 204, 0.20);
                    box-shadow: inset 7px -5px 10px 0 #000;
                    -moz-box-shadow: inset 7px -5px 10px 0 #000;
                    -webkit-box-shadow: inset 7px -5px 10px 0 #000;
                }
                .jstree-default .jstree-anchor {
                    height: 18px !important;
                    line-height: 18px !important;
                }
                .jstree-default .jstree-themeicon-custom {
                    background-color: yellow;
                    height: 18px !important;
                    text-align: center !important;
                    width: 18px !important;
                }
//                    #' . $this->id . ' .tree { float:left; min-width:319px; border-right:1px solid silver; overflow:auto; padding:0px 0; }
                #' . $this->id . " .tree .folder {
                        background:url('../media/img/file_sprite.png') right bottom no-repeat;
                    }
                #" . $this->id . " .tree .file {
                        background:url('../media/img/file_sprite.png') 0 0 no-repeat;
                    }
                #" . $this->id . ' .tree .file-pdf { background-position: -32px 0 }
                #' . $this->id . ' .tree .file-as { background-position: -36px 0 }
                #' . $this->id . ' .tree .file-c { background-position: -72px -0px }
                #' . $this->id . ' .tree .file-iso { background-position: -108px -0px }
                #' . $this->id . ' .tree .file-htm, #' . $this->id . ' .file-html, #' . $this->id . ' .file-xml, #' . $this->id . ' .file-xsl { background-position: -126px -0px }
                #' . $this->id . ' .tree .file-cf { background-position: -162px -0px }
                #' . $this->id . ' .tree .file-cpp { background-position: -216px -0px }
                #' . $this->id . ' .tree .file-cs { background-position: -236px -0px }
                #' . $this->id . ' .tree .file-sql { background-position: -272px -0px }
                #' . $this->id . ' .tree .file-xls, #' . $this->id . ' .file-xlsx { background-position: -362px -0px }
                #' . $this->id . ' .tree .file-h { background-position: -488px -0px }
                #' . $this->id . ' .tree .file-crt, #' . $this->id . ' .file-pem, #' . $this->id . ' .file-cer { background-position: -452px -18px }
                #' . $this->id . ' .tree .file-php { background-position: -108px -18px }
                #' . $this->id . ' .tree .file-jpg, #' . $this->id . ' .file-jpeg, #' . $this->id . ' .file-png, #' . $this->id . ' .file-gif, #' . $this->id . ' .file-bmp { background-position: -126px -18px }
                #' . $this->id . ' .tree .file-ppt, #' . $this->id . ' .file-pptx { background-position: -144px -18px }
                #' . $this->id . ' .tree .file-rb { background-position: -180px -18px }
                #' . $this->id . ' .tree .file-text, #' . $this->id . ' .file-txt, #' . $this->id . ' .file-md, #' . $this->id . ' .file-log, #' . $this->id . ' .file-htaccess { background-position: -254px -18px }
                #' . $this->id . ' .tree .file-doc, #' . $this->id . ' .file-docx { background-position: -362px -18px }
                #' . $this->id . ' .tree .file-zip, #' . $this->id . ' .file-gz, #' . $this->id . ' .file-tar, #' . $this->id . ' .file-rar { background-position: -416px -18px }
                #' . $this->id . ' .tree .file-js { background-position: -434px -18px }
                #' . $this->id . ' .tree .file-css { background-position: -144px -0px }
                #' . $this->id . ' .tree .file-fla { background-position: -398px -0px }
            </style>
            ');

        return $html->text();
    }

    public static function ajax($data) {
        $operation = $data->operation;
        $requires = $data->requires;
        $custom_field_data = $data->custom_field_data;

        foreach ($requires as $req) {
            if (file_exists($req)) {
                require_once $req;
            }
        }
        $args = [
            'operation' => $operation,
            'custom_field_data' => $custom_field_data
        ];
        call_user_func($data->tree_callback, $args);
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();

        $callback_node_url = CAjaxMethod::factory()
                ->set_type('callback')
                ->set_data('callable', ['CElement_Tree', 'ajax'])
                ->set_data('tree_callback', $this->callback)
                ->set_data('requires', $this->requires)
                ->set_data('operation', 'get_node')
                ->set_data('custom_field_data', $this->custom_field_data)
                ->makeurl();
        $callback_content_url = CAjaxMethod::factory()
                ->set_type('callback')
                ->set_data('callable', ['CElement_Tree', 'ajax'])
                ->set_data('tree_callback', $this->callback)
                ->set_data('requires', $this->requires)
                ->set_data('operation', 'get_content')
                ->set_data('custom_field_data', $this->custom_field_data)
                ->makeurl();
        //die($callback_node_url);
        $js->appendln("jQuery('#" . $this->id . " .tree').jstree({
                    'core' : {
                        'data' : {
                            'url' : '" . $callback_node_url . "',
                            'dataType': 'json',
                            'data' : function (node) {
                                return {'id': node.id, 'parent': node.parent, 'parents': node.parents};
                            }
                        },
                        'check_callback' : true,
                        'multiple': false,
                        'themes' : {
                            'responsive' : false
                        }
                    },
                    'types' : {
                        'default' : { 'icon' : 'folder' },
                        'file' : { 'valid_children' : [], 'icon' : 'file' }
                    },
//'checkbox' : {
//    'keep_selected_style' : false
//  },
//                        'plugins' : ['state','dnd','contextmenu','wholerow', 'checkbox']
                })
            .on('changed.jstree', function (e, data) {
                if(data && data.selected && data.selected.length) {
//                        var data = {
//                            'id': data.selected.join(':'),
//                            'selected_id': data.selected,
//                            'parent': data.node.parent,
//                            'parents': data.node.parents
//                        }
//                        $.cresenity.reload('" . $this->target . "','" . $callback_content_url . "','post',data);
                    $.ajax({
                        type: 'get',
                        dataType: 'json',
                        url: '" . $callback_content_url . "?operation=get_content&id=' + data.selected.join(':'),
                        success: function(d) {
                            if(d && typeof d.type !== 'undefined') {
                                $('#" . $this->id . " #data .content').hide();

                                switch(d.type) {
                                    case 'text':
                                    case 'txt':
                                    case 'md':
                                    case 'htaccess':
                                    case 'log':
                                    case 'sql':
                                    case 'php':
                                    case 'log':
                                    case 'js':
                                    case 'json':
                                    case 'css':
                                    case 'html':
                                        $('#" . $this->id . " #data .code').show();
                                        $('#" . $this->id . " #code').val(d.content);
                                        break;
                                    case 'png':
                                    case 'jpg':
                                    case 'jpeg':
                                    case 'bmp':
                                    case 'gif':
                                        $('#" . $this->id . " #data .image img').one('load', function () { $(this).css({'marginTop':'-' + $(this).height()/2 + 'px','marginLeft':'-' + $(this).width()/2 + 'px'}); }).attr('src',d.content);
                                        $('#" . $this->id . " #data .image').show();
                                        break;
                                    default:
                                        $('#" . $this->id . " #data .default').html(d.content).show();
                                        break;
                                }
                            }
                        },
                    });
                }
                else {
                    $('#" . $this->id . " #data .content').hide();
                    $('#" . $this->id . " #data .default').html('Select a file from the tree.').show();
//                        $('#" . $this->target . "').html('');
//                        $('#" . $this->target . "').html('Select a file from the tree.').show();
//                        $('#" . $this->target . "').html('Select a file from the tree.').show();
                }
            })
            ;");

        return $js->text();
    }
}

<?php

class Controller_Demo_Elements_Treeview extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Tree View');

        $widget = $app->addWidget()->setTitle('Tree View Demo');
        $widget->addDiv()->add('Contoh CElement_Component_TreeView dengan data node tetap (tanpa ajax).');
        $widget->addBr();

        $widget->addTreeView('demoTreeViewStatic')->setNodes(static::demoData());

        $widgetAjax = $app->addWidget()->setTitle('Tree View Demo (Ajax)');
        $widgetAjax->addDiv()->add(
            'Contoh CElement_Component_TreeView yang mengambil node via ajax dari model '
            . '\Cresenity\Demo\Model\NestedCategory -- tiap kali sebuah node di-expand, '
            . 'ajax dipanggil lagi untuk mengambil children dari node tersebut saja.'
        );
        $widgetAjax->addBr();

        $widgetAjax->addTreeView('demoTreeViewAjax')->setNodes(
            function ($parentId, CElement_Component_TreeView_Node $node) {
                $parentId = in_array($parentId, ['#', '', null], true) ? null : $parentId;

                $categories = \Cresenity\Demo\Model\NestedCategory::where('parent_id', $parentId)->orderBy('lft')->get();
                foreach ($categories as $category) {
                    $hasChildren = \Cresenity\Demo\Model\NestedCategory::where('parent_id', $category->nested_category_id)->exists();
                    $node->addChild([
                        'id' => $category->nested_category_id,
                        'text' => $category->name,
                        'icon' => $hasChildren ? 'jstree-folder' : 'jstree-file',
                        'hasChildren' => $hasChildren,
                    ]);
                }
            }
        );

        return $app;
    }

    /**
     * Sample folder/file structure for this demo.
     *
     * @return array
     */
    protected static function demoData() {
        return [
            [
                'text' => 'src',
                'icon' => 'jstree-folder',
                'children' => [
                    ['text' => 'index.php', 'icon' => 'jstree-file'],
                    ['text' => 'style.css', 'icon' => 'jstree-file'],
                ],
            ],
            ['text' => 'README.md', 'icon' => 'jstree-file'],
        ];
    }
}

<?php

class Controller_Demo_Elements_Tree extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Tree');

        $widget = $app->addWidget()->setTitle('Tree Demo');
        $widget->addDiv()->add('Klik salah satu file pada tree di bawah untuk menampilkan isinya.');
        $widget->addBr();

        $tree = $widget->addTree('demoTree');
        $tree->setCallback([static::class, 'onTreeCallback']);

        return $app;
    }

    /**
     * Ajax callback wired via CElement_Tree::setCallback(), invoked by CAjax_Engine_Tree
     * for two operations: 'get_node' (jstree asking for the node list) and 'get_content'
     * (user selected a node, asking for its preview content). The returned array is
     * auto-encoded to JSON by the response layer.
     *
     * @param array $args
     *
     * @return array
     */
    public static function onTreeCallback($args) {
        $operation = carr::get($args, 'operation');

        if ($operation == 'get_content') {
            $id = carr::get($_GET, 'id');
            $node = static::findNode(static::demoTreeData(), $id);

            return [
                'type' => carr::get($node, 'type', 'text'),
                'content' => carr::get($node, 'content', ''),
            ];
        }

        return static::toJsTreeNodes(static::demoTreeData());
    }

    /**
     * Sample folder/file structure for this demo, shaped like the data CElement_Tree::render()
     * itself expects (id/label/node=root+child for folders, id/label/type/content for files).
     *
     * @return array
     */
    protected static function demoTreeData() {
        return [
            [
                'id' => 'src',
                'label' => 'src',
                'node' => 'root',
                'child' => [
                    [
                        'id' => 'index.php',
                        'label' => 'index.php',
                        'type' => 'php',
                        'content' => "<?php\n\necho 'Hello World';\n",
                    ],
                    [
                        'id' => 'style.css',
                        'label' => 'style.css',
                        'type' => 'css',
                        'content' => "body {\n    margin: 0;\n}\n",
                    ],
                ],
            ],
            [
                'id' => 'readme.md',
                'label' => 'README.md',
                'type' => 'md',
                'content' => "# Demo Tree\n\nContoh penggunaan CElement_Tree.",
            ],
        ];
    }

    /**
     * Convert demoTreeData()'s shape into the nested node format jstree expects.
     *
     * @param array $nodes
     *
     * @return array
     */
    protected static function toJsTreeNodes(array $nodes) {
        $result = [];
        foreach ($nodes as $node) {
            $isFolder = carr::get($node, 'node') == 'root';
            $jsNode = [
                'id' => carr::get($node, 'id'),
                'text' => carr::get($node, 'label'),
                'icon' => $isFolder ? 'jstree-folder' : 'jstree-file',
            ];
            if ($isFolder) {
                $jsNode['children'] = static::toJsTreeNodes(carr::get($node, 'child', []));
            }
            $result[] = $jsNode;
        }

        return $result;
    }

    /**
     * @param array  $nodes
     * @param string $id
     *
     * @return array|null
     */
    protected static function findNode(array $nodes, $id) {
        foreach ($nodes as $node) {
            if (carr::get($node, 'id') == $id) {
                return $node;
            }
            if (carr::get($node, 'node') == 'root') {
                $found = static::findNode(carr::get($node, 'child', []), $id);
                if ($found != null) {
                    return $found;
                }
            }
        }

        return null;
    }
}

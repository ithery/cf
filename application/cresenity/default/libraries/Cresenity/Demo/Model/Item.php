<?php

namespace Cresenity\Demo\Model;

class Item extends \CModel {
    use \CModel_ArrayDriver_ArrayDriverTrait;

    protected $rows = [
        ['items_id' => 1, 'category_id' => 1, 'name' => 'Item 1'],
        ['items_id' => 2, 'category_id' => 1, 'name' => 'Item 2'],
        ['items_id' => 3, 'category_id' => 1, 'name' => 'Item 3'],
        ['items_id' => 4, 'category_id' => 2, 'name' => 'Item 4'],
        ['items_id' => 5, 'category_id' => 2, 'name' => 'Item 5'],
        ['items_id' => 6, 'category_id' => 2, 'name' => 'Item 6'],
    ];

    public function category() {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }
}

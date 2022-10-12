<?php

namespace Cresenity\Demo\Model;

class Category extends \CModel {
    use \CModel_ArrayDriver_ArrayDriverTrait;

    protected $rows = [
        ['category_id' => 1, 'name' => 'Category 1'],
        ['category_id' => 2, 'name' => 'Category 2'],
        ['category_id' => 3, 'name' => 'Category 3'],
        ['category_id' => 4, 'name' => 'Category 4'],
        ['category_id' => 5, 'name' => 'Category 5']
    ];

    public function item() {
        return $this->hasMany(Item::class, 'category_id', 'category_id');
    }
}

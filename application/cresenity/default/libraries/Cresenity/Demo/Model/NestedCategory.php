<?php

namespace Cresenity\Demo\Model;

/**
 * Hierarchical (nested-set) demo data, used to showcase CElement_Component_Nestable::setDataFromModel().
 *
 * @property-read int         $nested_category_id
 * @property-read int|null    $parent_id
 * @property-read int         $lft
 * @property-read int         $rgt
 * @property-read int         $depth
 * @property-read string      $name
 * @property-read int         $status
 */
class NestedCategory extends \CModel {
    use \CModel_ArrayDriver_ArrayDriverTrait;
    use \CModel_Nested_NestedTrait;

    protected $table = 'nested_category';

    protected $rows = [
        ['nested_category_id' => 1, 'parent_id' => null, 'lft' => 1, 'rgt' => 14, 'depth' => 0, 'name' => 'Electronics', 'status' => 1],
        ['nested_category_id' => 2, 'parent_id' => 1, 'lft' => 2, 'rgt' => 7, 'depth' => 1, 'name' => 'Computers', 'status' => 1],
        ['nested_category_id' => 3, 'parent_id' => 2, 'lft' => 3, 'rgt' => 4, 'depth' => 2, 'name' => 'Laptops', 'status' => 1],
        ['nested_category_id' => 4, 'parent_id' => 2, 'lft' => 5, 'rgt' => 6, 'depth' => 2, 'name' => 'Desktops', 'status' => 1],
        ['nested_category_id' => 5, 'parent_id' => 1, 'lft' => 8, 'rgt' => 13, 'depth' => 1, 'name' => 'Mobile Phones', 'status' => 1],
        ['nested_category_id' => 6, 'parent_id' => 5, 'lft' => 9, 'rgt' => 10, 'depth' => 2, 'name' => 'Smartphones', 'status' => 1],
        ['nested_category_id' => 7, 'parent_id' => 5, 'lft' => 11, 'rgt' => 12, 'depth' => 2, 'name' => 'Feature Phones', 'status' => 1],
        ['nested_category_id' => 8, 'parent_id' => null, 'lft' => 15, 'rgt' => 22, 'depth' => 0, 'name' => 'Furniture', 'status' => 1],
        ['nested_category_id' => 9, 'parent_id' => 8, 'lft' => 16, 'rgt' => 19, 'depth' => 1, 'name' => 'Living Room', 'status' => 1],
        ['nested_category_id' => 10, 'parent_id' => 9, 'lft' => 17, 'rgt' => 18, 'depth' => 2, 'name' => 'Sofas', 'status' => 1],
        ['nested_category_id' => 11, 'parent_id' => 8, 'lft' => 20, 'rgt' => 21, 'depth' => 1, 'name' => 'Bedroom', 'status' => 1],
    ];
}

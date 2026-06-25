<?php

namespace Cresenity\Demo\Model;

/**
 * Demo product model with 50 dummy records.
 *
 * @property-read int    $product_id
 * @property-read string $sku
 * @property-read string $name
 * @property-read string $category
 * @property-read int    $price
 * @property-read int    $stock
 * @property-read string $unit
 */
class Product extends \CModel {
    use \CModel_ArrayDriver_ArrayDriverTrait;

    /**
     * @var array
     */
    protected $rows = [
        ['product_id' => 1, 'sku' => 'SKU-0001', 'name' => 'Premium Widget 1', 'category' => 'Electronics', 'price' => 206000, 'stock' => 168, 'unit' => 'unit'],
        ['product_id' => 2, 'sku' => 'SKU-0002', 'name' => 'Basic Gadget 1', 'category' => 'Clothing', 'price' => 176000, 'stock' => 119, 'unit' => 'unit'],
        ['product_id' => 3, 'sku' => 'SKU-0003', 'name' => 'Deluxe Tool 1', 'category' => 'Food & Beverage', 'price' => 260000, 'stock' => 37, 'unit' => 'pcs'],
        ['product_id' => 4, 'sku' => 'SKU-0004', 'name' => 'Standard Device 1', 'category' => 'Furniture', 'price' => 18000, 'stock' => 200, 'unit' => 'unit'],
        ['product_id' => 5, 'sku' => 'SKU-0005', 'name' => 'Pro Kit 1', 'category' => 'Stationery', 'price' => 83000, 'stock' => 39, 'unit' => 'unit'],
        ['product_id' => 6, 'sku' => 'SKU-0006', 'name' => 'Ultra Pack 1', 'category' => 'Electronics', 'price' => 439000, 'stock' => 93, 'unit' => 'unit'],
        ['product_id' => 7, 'sku' => 'SKU-0007', 'name' => 'Classic Set 1', 'category' => 'Clothing', 'price' => 43000, 'stock' => 25, 'unit' => 'unit'],
        ['product_id' => 8, 'sku' => 'SKU-0008', 'name' => 'Modern Bundle 1', 'category' => 'Food & Beverage', 'price' => 84000, 'stock' => 197, 'unit' => 'pcs'],
        ['product_id' => 9, 'sku' => 'SKU-0009', 'name' => 'Eco Box 1', 'category' => 'Furniture', 'price' => 375000, 'stock' => 105, 'unit' => 'unit'],
        ['product_id' => 10, 'sku' => 'SKU-0010', 'name' => 'Smart Case 1', 'category' => 'Stationery', 'price' => 199000, 'stock' => 21, 'unit' => 'unit'],
        ['product_id' => 11, 'sku' => 'SKU-0011', 'name' => 'Premium Widget 2', 'category' => 'Electronics', 'price' => 457000, 'stock' => 94, 'unit' => 'unit'],
        ['product_id' => 12, 'sku' => 'SKU-0012', 'name' => 'Basic Gadget 2', 'category' => 'Clothing', 'price' => 155000, 'stock' => 116, 'unit' => 'unit'],
        ['product_id' => 13, 'sku' => 'SKU-0013', 'name' => 'Deluxe Tool 2', 'category' => 'Food & Beverage', 'price' => 216000, 'stock' => 157, 'unit' => 'pcs'],
        ['product_id' => 14, 'sku' => 'SKU-0014', 'name' => 'Standard Device 2', 'category' => 'Furniture', 'price' => 448000, 'stock' => 121, 'unit' => 'unit'],
        ['product_id' => 15, 'sku' => 'SKU-0015', 'name' => 'Pro Kit 2', 'category' => 'Stationery', 'price' => 6000, 'stock' => 111, 'unit' => 'unit'],
        ['product_id' => 16, 'sku' => 'SKU-0016', 'name' => 'Ultra Pack 2', 'category' => 'Electronics', 'price' => 45000, 'stock' => 187, 'unit' => 'unit'],
        ['product_id' => 17, 'sku' => 'SKU-0017', 'name' => 'Classic Set 2', 'category' => 'Clothing', 'price' => 105000, 'stock' => 71, 'unit' => 'unit'],
        ['product_id' => 18, 'sku' => 'SKU-0018', 'name' => 'Modern Bundle 2', 'category' => 'Food & Beverage', 'price' => 152000, 'stock' => 76, 'unit' => 'pcs'],
        ['product_id' => 19, 'sku' => 'SKU-0019', 'name' => 'Eco Box 2', 'category' => 'Furniture', 'price' => 51000, 'stock' => 119, 'unit' => 'unit'],
        ['product_id' => 20, 'sku' => 'SKU-0020', 'name' => 'Smart Case 2', 'category' => 'Stationery', 'price' => 235000, 'stock' => 85, 'unit' => 'unit'],
        ['product_id' => 21, 'sku' => 'SKU-0021', 'name' => 'Premium Widget 3', 'category' => 'Electronics', 'price' => 497000, 'stock' => 198, 'unit' => 'unit'],
        ['product_id' => 22, 'sku' => 'SKU-0022', 'name' => 'Basic Gadget 3', 'category' => 'Clothing', 'price' => 195000, 'stock' => 35, 'unit' => 'unit'],
        ['product_id' => 23, 'sku' => 'SKU-0023', 'name' => 'Deluxe Tool 3', 'category' => 'Food & Beverage', 'price' => 370000, 'stock' => 147, 'unit' => 'pcs'],
        ['product_id' => 24, 'sku' => 'SKU-0024', 'name' => 'Standard Device 3', 'category' => 'Furniture', 'price' => 376000, 'stock' => 13, 'unit' => 'unit'],
        ['product_id' => 25, 'sku' => 'SKU-0025', 'name' => 'Pro Kit 3', 'category' => 'Stationery', 'price' => 204000, 'stock' => 188, 'unit' => 'unit'],
        ['product_id' => 26, 'sku' => 'SKU-0026', 'name' => 'Ultra Pack 3', 'category' => 'Electronics', 'price' => 54000, 'stock' => 60, 'unit' => 'unit'],
        ['product_id' => 27, 'sku' => 'SKU-0027', 'name' => 'Classic Set 3', 'category' => 'Clothing', 'price' => 154000, 'stock' => 149, 'unit' => 'unit'],
        ['product_id' => 28, 'sku' => 'SKU-0028', 'name' => 'Modern Bundle 3', 'category' => 'Food & Beverage', 'price' => 78000, 'stock' => 121, 'unit' => 'pcs'],
        ['product_id' => 29, 'sku' => 'SKU-0029', 'name' => 'Eco Box 3', 'category' => 'Furniture', 'price' => 138000, 'stock' => 62, 'unit' => 'unit'],
        ['product_id' => 30, 'sku' => 'SKU-0030', 'name' => 'Smart Case 3', 'category' => 'Stationery', 'price' => 115000, 'stock' => 15, 'unit' => 'unit'],
        ['product_id' => 31, 'sku' => 'SKU-0031', 'name' => 'Premium Widget 4', 'category' => 'Electronics', 'price' => 286000, 'stock' => 131, 'unit' => 'unit'],
        ['product_id' => 32, 'sku' => 'SKU-0032', 'name' => 'Basic Gadget 4', 'category' => 'Clothing', 'price' => 486000, 'stock' => 153, 'unit' => 'unit'],
        ['product_id' => 33, 'sku' => 'SKU-0033', 'name' => 'Deluxe Tool 4', 'category' => 'Food & Beverage', 'price' => 194000, 'stock' => 64, 'unit' => 'pcs'],
        ['product_id' => 34, 'sku' => 'SKU-0034', 'name' => 'Standard Device 4', 'category' => 'Furniture', 'price' => 210000, 'stock' => 93, 'unit' => 'unit'],
        ['product_id' => 35, 'sku' => 'SKU-0035', 'name' => 'Pro Kit 4', 'category' => 'Stationery', 'price' => 500000, 'stock' => 12, 'unit' => 'unit'],
        ['product_id' => 36, 'sku' => 'SKU-0036', 'name' => 'Ultra Pack 4', 'category' => 'Electronics', 'price' => 492000, 'stock' => 177, 'unit' => 'unit'],
        ['product_id' => 37, 'sku' => 'SKU-0037', 'name' => 'Classic Set 4', 'category' => 'Clothing', 'price' => 219000, 'stock' => 155, 'unit' => 'unit'],
        ['product_id' => 38, 'sku' => 'SKU-0038', 'name' => 'Modern Bundle 4', 'category' => 'Food & Beverage', 'price' => 253000, 'stock' => 109, 'unit' => 'pcs'],
        ['product_id' => 39, 'sku' => 'SKU-0039', 'name' => 'Eco Box 4', 'category' => 'Furniture', 'price' => 28000, 'stock' => 72, 'unit' => 'unit'],
        ['product_id' => 40, 'sku' => 'SKU-0040', 'name' => 'Smart Case 4', 'category' => 'Stationery', 'price' => 292000, 'stock' => 103, 'unit' => 'unit'],
        ['product_id' => 41, 'sku' => 'SKU-0041', 'name' => 'Premium Widget 5', 'category' => 'Electronics', 'price' => 390000, 'stock' => 107, 'unit' => 'unit'],
        ['product_id' => 42, 'sku' => 'SKU-0042', 'name' => 'Basic Gadget 5', 'category' => 'Clothing', 'price' => 345000, 'stock' => 183, 'unit' => 'unit'],
        ['product_id' => 43, 'sku' => 'SKU-0043', 'name' => 'Deluxe Tool 5', 'category' => 'Food & Beverage', 'price' => 174000, 'stock' => 20, 'unit' => 'pcs'],
        ['product_id' => 44, 'sku' => 'SKU-0044', 'name' => 'Standard Device 5', 'category' => 'Furniture', 'price' => 224000, 'stock' => 146, 'unit' => 'unit'],
        ['product_id' => 45, 'sku' => 'SKU-0045', 'name' => 'Pro Kit 5', 'category' => 'Stationery', 'price' => 224000, 'stock' => 119, 'unit' => 'unit'],
        ['product_id' => 46, 'sku' => 'SKU-0046', 'name' => 'Ultra Pack 5', 'category' => 'Electronics', 'price' => 109000, 'stock' => 49, 'unit' => 'unit'],
        ['product_id' => 47, 'sku' => 'SKU-0047', 'name' => 'Classic Set 5', 'category' => 'Clothing', 'price' => 371000, 'stock' => 85, 'unit' => 'unit'],
        ['product_id' => 48, 'sku' => 'SKU-0048', 'name' => 'Modern Bundle 5', 'category' => 'Food & Beverage', 'price' => 277000, 'stock' => 130, 'unit' => 'pcs'],
        ['product_id' => 49, 'sku' => 'SKU-0049', 'name' => 'Eco Box 5', 'category' => 'Furniture', 'price' => 251000, 'stock' => 55, 'unit' => 'unit'],
        ['product_id' => 50, 'sku' => 'SKU-0050', 'name' => 'Smart Case 5', 'category' => 'Stationery', 'price' => 156000, 'stock' => 174, 'unit' => 'unit'],
    ];
}

<?php

class Controller_Demo_Module_Validation_Database extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $sku = '';
        $categoryName = '';
        $post = c::request()->post();

        // Database-level rule: the value must not already exist on another row.
        // Product/Category are demo models, so we look them up with Model::where()->first();
        // against real tables you'd write CValidation::rule()->unique('products', 'sku')
        // instead, which runs the same check straight against the database.
        $skuUniqueRule = CValidation::rule()->closure(function ($attribute, $value, Closure $fail) {
            $existing = \Cresenity\Demo\Model\Product::where('sku', '=', $value)->first();

            if ($existing != null) {
                $fail(c::e("{$attribute} {$value} sudah digunakan, silakan pakai kode lain."));
            }
        });
        $categoryNameUniqueRule = CValidation::rule()->closure(function ($attribute, $value, Closure $fail) {
            $existing = \Cresenity\Demo\Model\Category::where('name', '=', $value)->first();

            if ($existing != null) {
                $fail(c::e("{$attribute} {$value} sudah digunakan, silakan pakai nama lain."));
            }
        });

        if ($post) {
            c::msg('success', 'Form Submitted with data:<br/><pre>' . json_encode($post, JSON_PRETTY_PRINT) . '</pre>');
            $validator = c::validator($post, [
                'sku' => ['required', $skuUniqueRule],
                'category_name' => ['required', $categoryNameUniqueRule],
            ]);

            if (!$validator->check()) {
                c::msg('error', $validator->errors()->first());
            }
            $sku = carr::get($post, 'sku');
            $categoryName = carr::get($post, 'category_name');
        }

        $app->setTitle('Validation - Database Demo');

        $existingSkus = \Cresenity\Demo\Model\Product::limit(5)->get()->pluck('sku')->implode(', ');
        $existingCategoryNames = \Cresenity\Demo\Model\Category::limit(5)->get()->pluck('name')->implode(', ');

        $widget = $app->addWidget()->setTitle('Database-level Validation Demo');
        $widget->addDiv()->addClass('mb-3 text-muted')
            ->add('Both fields below are validated with a control-level closure rule (addControl()->addValidation()) that queries an existing demo model, so a value already in use is rejected.');
        $form = $widget->addForm();
        $form->addField()->setLabel('Product Code')
            ->addControl('sku', 'text')
            ->setPlaceholder('e.g. SKU-0051')
            ->setValue($sku)
            ->addValidation('required')
            ->addValidation($skuUniqueRule);
        $form->addDiv()->addClass('form-text text-muted mb-3')->add('Existing codes (try one to see it fail): ' . $existingSkus);
        $form->addField()->setLabel('Category Name')
            ->addControl('category_name', 'text')
            ->setPlaceholder('e.g. Category 6')
            ->setValue($categoryName)
            ->addValidation('required')
            ->addValidation($categoryNameUniqueRule);
        $form->addDiv()->addClass('form-text text-muted mb-3')->add('Existing names (try one to see it fail): ' . $existingCategoryNames);
        $form->addActionList()->addAction()->setSubmit()->setLabel('Submit');

        return $app;
    }
}

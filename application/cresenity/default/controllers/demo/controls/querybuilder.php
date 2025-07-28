<?php

class Controller_Demo_Controls_Querybuilder extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $query = c::request()->post('query');
        $app->add(<<<HTML
        <style>
        .query-builder.form-inline {
            display: block!important;
        }

        </style>
        HTML);
        c::db()->enableBenchmark();

        if ($query) {
            $modelQuery = CElement_FormInput_QueryBuilder::parseToModelQuery($query, \Cresenity\Demo\Model\Country::class);
            /** @var CModel_Query $modelQuery */
            $sql = $modelQuery->toCompiledSql();
            $alertInfo = $app->addAlert()->setType('info');
            $alertInfo->addH2()->add('Post');
            $alertInfo->addPre()->add($query);
            $alertInfo->addH2()->add('Compiled SQL To Country Model');
            $alertInfo->addPre()->add($sql);
        }

        $form = $app->addForm();
        c::manager()->registerModule('jquery-query-builder');
        $queryBuilder = $form->addQueryBuilderControl('query');
        /** @var CElement_FormInput_QueryBuilder $queryBuilder */
        $queryBuilder->withFilterBuilder(function (CElement_FormInput_QueryBuilder_FilterBuilder $builder) {
            $builder->addFilter()->setId('name')->setLabel('Name')->setTypeString();

            $builder->withFilter(function (CElement_FormInput_QueryBuilder_Filter $filter) {
                $filter->setId('tag')->setLabel('Tag')->setTypeInteger()->setInputSelect([
                    '1' => 'Books',
                    '2' => 'Movies',
                    '3' => 'Music',
                    '4' => 'Tools',
                    '5' => 'Goodies',
                    '6' => 'Clothes',

                ])->setOperators(['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']);
            })->withFilter(function (CElement_FormInput_QueryBuilder_Filter $filter) {
                $filter->setId('in_stock')->setLabel('In Stock')->setTypeInteger()->setInputRadio([
                    '1' => 'Yes',
                    '0' => 'No'
                ]);
            })->withFilter(function (CElement_FormInput_QueryBuilder_Filter $filter) {
                $filter->setId('price')->setLabel('Price')->setTypeDouble()->setValidation([
                    'min' => 0,
                    'step' => '0.01'
                ]);
            })->withFilter(function (CElement_FormInput_QueryBuilder_Filter $filter) {
                $filter->setId('id')->setLabel('Identifier')->setTypeString()->setValidation([
                    'format' => '/^.{4}-.{4}-.{4}$/',
                    'messages' => [
                        'format' => 'The provided identifier is not valid',
                    ],
                ])->setPlaceholder('____-____-____');
            });
        });

        $form->addActionList()->addAction()->setLabel('Submit')->setSubmit();

        return $app;
    }
}

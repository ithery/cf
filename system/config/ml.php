<?php
return [
    'csv_path_output' => DOCROOT. 'temp/ml/' . CF::appCode() . '/csv/output/',
    'csv_path_input' => DOCROOT. 'temp/ml/' . CF::appCode() . '/csv/input/',
    'ai_model_path_output' => DOCROOT. 'temp/ml/' . CF::appCode() . '/model/',
    'RubixMainClass' => RubixService::class,
];

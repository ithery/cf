<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Default Driver
    |--------------------------------------------------------------------------
    |
    | This value determines which of the following driver to use.
    | You can switch to a different driver at runtime.
    |
    */
    'default' => 'jenssegers',

    //except save request or route names
    'except' =>  ['login', 'register'],


    //name of the table which visit records should save in
    'table_name' => 'visit',
    'model' => [
        'visit' => CApp_Model_Visit::class,
    ],
    /*
    |--------------------------------------------------------------------------
    | List of Drivers
    |--------------------------------------------------------------------------
    |
    | This is the array of Classes that maps to Drivers above.
    | You can create your own driver if you like and add the
    | config in the drivers array and the class to use for
    | here with the same name. You will have to implement
    | Shetabit\Visitor\Contracts\UserAgentParser in your driver.
    |
    */
    'drivers' => [
        'jenssegers' => \CApp_Visitor_Driver_JenssegersAgentDriver::class,
        'UAParser' => \CApp_Visitor_Driver_UAParserDriver::class,
    ]
];

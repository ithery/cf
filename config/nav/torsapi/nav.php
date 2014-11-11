<?php

return array(
    array(
        "name" => "dashboard",
        "label" => "Dashboard",
        "controller" => "home",
        "method" => "index",
        "icon" => "home",
    ),
	 array(
        "name" => "data_menu",
        "label" => "Data",
        "icon" => "table",
        "subnav" => array(
            array(
                "name" => "blacklist_keyword_menu",
                "label" => "Blacklist Keyword",
                "subnav" => array(
                    array(
                        "name" => "blacklist_keyword",
                        "label" => "Blacklist Keyword",
                        "controller" => "blacklist_keyword",
                        "method" => "index",
                        'action' => array(
                            array(
                                'name' => 'add_blacklist_keyword',
                                'label' => 'Add',
                                'controller' => 'blacklist_keyword',
                                'method' => 'add',
                            ),
                            array(
                                'name' => 'edit_blacklist_keyword',
                                'label' => 'Edit',
                                'controller' => 'blacklist_keyword',
                                'method' => 'edit',
                            ),
                            array(
                                'name' => 'delete_blacklist_keyword',
                                'label' => 'Delete',
                                'controller' => 'blacklist_keyword',
                                'method' => 'delete',
                            ),
                        ), //end action 
                    ),
                    array(
                        "name" => "blacklist_keyword_test",
                        "label" => "Blacklist Keyword Test",
                        "controller" => "blacklist_keyword_test",
                        "method" => "index",
                        'action' => array(
                        ), //end action 
                    ),
                ),
            ),
		),
    ),
    
    array(
        "name" => "setting_list",
        "label" => "Setting",
        "icon" => "cog",
        "subnav" => array(
            array(
                "name" => "access",
                "label" => "Access",
                "subnav" => array(
                    array(
                        "name" => "roles",
                        "label" => "Roles",
                        "controller" => "roles",
                        "method" => "index",
                        'action' => array(
                            array(
                                'name' => 'add_roles',
                                'label' => 'Add',
                                'controller' => 'roles',
                                'method' => 'add',
                            ),
                            array(
                                'name' => 'edit_roles',
                                'label' => 'Edit',
                                'controller' => 'roles',
                                'method' => 'edit',
                            ),
                            array(
                                'name' => 'delete_roles',
                                'label' => 'Delete',
                                'controller' => 'roles',
                                'method' => 'delete',
                            ),
                        ), //end action roles
                    ),
                    array(
                        "name" => "users",
                        "label" => "Users",
                        "controller" => "users",
                        "method" => "index",
                        'action' => array(
                            array(
                                'name' => 'add_users',
                                'label' => 'Add',
                                'controller' => 'users',
                                'method' => 'add',
                            ),
                            array(
                                'name' => 'edit_users',
                                'label' => 'Edit',
                                'controller' => 'users',
                                'method' => 'edit',
                            ),
                            array(
                                'name' => 'delete_users',
                                'label' => 'Delete',
                                'controller' => 'users',
                                'method' => 'delete',
                            ),
                        ), //end action users
                    ),
                    array(
                        "name" => "user_permission",
                        "label" => "Users Permission",
                        "controller" => "user_permission",
                        "method" => "index",
                    ),
                ), //end subnav access
            ),
            
        ),
    ),
    array(
        "name" => "log_menu",
        "label" => "Log",
        "icon" => "file",
        "subnav" => array(
            array(
                "name" => "api_log_request",
                "label" => "API Log Request",
                "controller" => "api_log_request",
                "method" => "index",
                'action' => array(
                ), //end action 
            ),
            array(
                "name" => "api_airlines_log_request",
                "label" => "API Airlines Log Request",
                "controller" => "api_airlines_log_request",
                "method" => "index",
                'action' => array(
                ), //end action 
            ),
        ),
    ),
    array(
        "name" => "developer_menu",
        "label" => "Developer",
        "icon" => "cog",
        "subnav" => array(
            array(
                "name" => "api_specification",
                "label" => "API Specification",
                "controller" => "api_specification",
                "method" => "index",
                'action' => array(
                ), //end action 
            ),
            array(
                "name" => "api_error_specification",
                "label" => "API Error Specification",
                "controller" => "api_error_specification",
                "method" => "index",
                'action' => array(
                ), //end action 
            ),
        ),
    ),
);



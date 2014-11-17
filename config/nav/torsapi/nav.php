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
            "name" => "data_transaction",
            "label" => "Transaction",
            "icon" => "table",
            "subnav" => array(
                array(
                    "name" => "transaction",
                    "label" => "Transaction",
                    "controller" => "transaction",
                    "method" => "index",
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
            "name" => "developer_menu",
            "label" => "Developer",
            "icon" => "cog",
            "subnav" => array(
                array(
                    "name" => "airlines_simulation",
                    "label" => "Airlines Simulation",
                    "controller" => "airlines_simulation",
                    "method" => "index",
                    'action' => array(
                    ), //end action 
                ),
            ),
        ),
    );
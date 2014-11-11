<?php

return array(
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
                    array(
                        'name' => 'order_roles',
                        'label' => 'Change Order',
                        'controller' => 'roles',
                        'method' => 'ordering',
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
                        'name' => 'detail_users',
                        'label' => 'Detail',
                        'controller' => 'users',
                        'method' => 'detail',
                    ),
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
);

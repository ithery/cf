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
        "name" => "master_data_menu",
        "label" => "Master Data",
        "icon" => "list",
        "subnav" => array(
            array(
                "name" => "employee_menu",
                "label" => "Employee",
                "subnav" => array(
                    array(
                        "name" => "employee_group",
                        "label" => "Employee Group",
                        "controller" => "employee_group",
                        "method" => "index",
                        'action' => array(
                            array(
                                'name' => 'add_employee_group',
                                'label' => 'Add',
                                'controller' => 'employee_group',
                                'method' => 'add',
                            ),
                            array(
                                'name' => 'edit_employee_group',
                                'label' => 'Edit',
                                'controller' => 'employee_group',
                                'method' => 'edit',
                            ),
                            array(
                                'name' => 'delete_employee_group',
                                'label' => 'Delete',
                                'controller' => 'employee_group',
                                'method' => 'delete',
                            ),
                        ),
                    ),
                    array(
                        "name" => "employee",
                        "label" => "Employee",
                        "controller" => "employee",
                        "method" => "index",
                        'action' => array(
                            array(
                                'name' => 'add_employee',
                                'label' => 'Add',
                                'controller' => 'employee',
                                'method' => 'add',
                            ),
                            array(
                                'name' => 'edit_employee',
                                'label' => 'Edit',
                                'controller' => 'employee',
                                'method' => 'edit',
                            ),
                            array(
                                'name' => 'delete_employee',
                                'label' => 'Delete',
                                'controller' => 'employee',
                                'method' => 'delete',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                "name" => "insurance_menu",
                "label" => "Insurance",
                "subnav" => array(
                    array(
                        "name" => "insurance_group",
                        "label" => "Insurance Group",
                        "controller" => "insurance_group",
                        "method" => "index",
                        'action' => array(
                            array(
                                'name' => 'add_insurance_group',
                                'label' => 'Add',
                                'controller' => 'insurance_group',
                                'method' => 'add',
                            ),
                            array(
                                'name' => 'edit_insurance_group',
                                'label' => 'Edit',
                                'controller' => 'insurance_group',
                                'method' => 'edit',
                            ),
                            array(
                                'name' => 'delete_insurance_group',
                                'label' => 'Delete',
                                'controller' => 'insurance_group',
                                'method' => 'delete',
                            ),
                        ),
                    ),
                    array(
                        "name" => "insurance",
                        "label" => "Insurance",
                        "controller" => "insurance",
                        "method" => "index",
                        'action' => array(
                            array(
                                'name' => 'add_insurance',
                                'label' => 'Add',
                                'controller' => 'insurance',
                                'method' => 'add',
                            ),
                            array(
                                'name' => 'edit_insurance',
                                'label' => 'Edit',
                                'controller' => 'insurance',
                                'method' => 'edit',
                            ),
                            array(
                                'name' => 'delete_insurance',
                                'label' => 'Delete',
                                'controller' => 'insurance',
                                'method' => 'delete',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                "name" => "subbroker_menu",
                "label" => "Subbroker",
                "subnav" => array(
                    array(
                        "name" => "subbroker_group",
                        "label" => "Subbroker Group",
                        "controller" => "subbroker_group",
                        "method" => "index",
                        'action' => array(
                            array(
                                'name' => 'add_subbroker_group',
                                'label' => 'Add',
                                'controller' => 'subbroker_group',
                                'method' => 'add',
                            ),
                            array(
                                'name' => 'edit_subbroker_group',
                                'label' => 'Edit',
                                'controller' => 'subbroker_group',
                                'method' => 'edit',
                            ),
                            array(
                                'name' => 'delete_subbroker_group',
                                'label' => 'Delete',
                                'controller' => 'subbroker_group',
                                'method' => 'delete',
                            ),
                        ),
                    ),
                    array(
                        "name" => "subbroker",
                        "label" => "Subbroker",
                        "controller" => "subbroker",
                        "method" => "index",
                        'action' => array(
                            array(
                                'name' => 'add_subbroker',
                                'label' => 'Add',
                                'controller' => 'subbroker',
                                'method' => 'add',
                            ),
                            array(
                                'name' => 'edit_subbroker',
                                'label' => 'Edit',
                                'controller' => 'subbroker',
                                'method' => 'edit',
                            ),
                            array(
                                'name' => 'delete_subbroker',
                                'label' => 'Delete',
                                'controller' => 'subbroker',
                                'method' => 'delete',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                "name" => "client_menu",
                "label" => "Client",
                "subnav" => array(
                    array(
                        "name" => "client_group",
                        "label" => "Client Group",
                        "controller" => "client_group",
                        "method" => "index",
                        'action' => array(
                            array(
                                'name' => 'add_client_group',
                                'label' => 'Add',
                                'controller' => 'client_group',
                                'method' => 'add',
                            ),
                            array(
                                'name' => 'edit_client_group',
                                'label' => 'Edit',
                                'controller' => 'client_group',
                                'method' => 'edit',
                            ),
                            array(
                                'name' => 'delete_client_group',
                                'label' => 'Delete',
                                'controller' => 'client_group',
                                'method' => 'delete',
                            ),
                        ),
                    ),
                    array(
                        "name" => "client",
                        "label" => "Client",
                        "controller" => "client",
                        "method" => "index",
                        'action' => array(
                            array(
                                'name' => 'add_client',
                                'label' => 'Add',
                                'controller' => 'client',
                                'method' => 'add',
                            ),
                            array(
                                'name' => 'edit_client',
                                'label' => 'Edit',
                                'controller' => 'client',
                                'method' => 'edit',
                            ),
                            array(
                                'name' => 'delete_client',
                                'label' => 'Delete',
                                'controller' => 'client',
                                'method' => 'delete',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                "name" => "insurance_type",
                "label" => "Insurance Type",
                "controller" => "insurance_type",
                "method" => "index",
                'action' => array(
                    array(
                        'name' => 'add_insurance_type',
                        'label' => 'Add',
                        'controller' => 'insurance_type',
                        'method' => 'add',
                    ),
                    array(
                        'name' => 'edit_insurance_type',
                        'label' => 'Edit',
                        'controller' => 'insurance_type',
                        'method' => 'edit',
                    ),
                    array(
                        'name' => 'delete_insurance_type',
                        'label' => 'Delete',
                        'controller' => 'insurance_type',
                        'method' => 'delete',
                    ),
                    array(
                        'name' => 'view_insurance_type',
                        'label' => 'View',
                        'controller' => 'insurance_type',
                        'method' => 'view',
                    ),
                    array(
                        'name' => 'insurance_type_quotation_printer',
                        'label' => 'Manage Quotation Printer',
                        'controller' => 'insurance_type',
                        'method' => 'quotation_printer',
                    ),
                    array(
                        'name' => 'insurance_type_covernote_printer',
                        'label' => 'Manage Covernote Printer',
                        'controller' => 'insurance_type',
                        'method' => 'covernote_printer',
                    ),
                    array(
                        'name' => 'insurance_type_clause',
                        'label' => 'Manage Clause',
                        'controller' => 'insurance_type',
                        'method' => 'clause',
                    ),
                    array(
                        'name' => 'insurance_type_deductible',
                        'label' => 'Manage Deductible',
                        'controller' => 'insurance_type',
                        'method' => 'deductible',
                    ),
                    array(
                        'name' => 'insurance_type_section',
                        'label' => 'Manage Section',
                        'controller' => 'insurance_type',
                        'method' => 'section',
                    ),
                    array(
                        'name' => 'insurance_type_sum_insured_breakdown',
                        'label' => 'Manage Sum Insured Breakdown',
                        'controller' => 'insurance_type',
                        'method' => 'sum_insured_breakdown',
                    ),
                    array(
                        'name' => 'insurance_type_custom_input',
                        'label' => 'Manage Custom Input',
                        'controller' => 'insurance_type',
                        'method' => 'insurance_type_input',
                    ),
                    array(
                        'name' => 'insurance_type_custom_item_input',
                        'label' => 'Manage Custom Item Input',
                        'controller' => 'insurance_type',
                        'method' => 'insurance_type_item_input',
                    ),
                ),
            ),
        ),
    ),
    array(
        "name" => "document_menu",
        "label" => "Document",
        "icon" => "book",
        "subnav" => array(
            array(
                "name" => "quotation",
                "label" => "Quotation",
                "controller" => "quotation",
                "method" => "index",
                'action' => array(
                    array(
                        'name' => 'add_quotation',
                        'label' => 'Add',
                        'controller' => 'quotation',
                        'method' => 'add',
                    ),
                    array(
                        'name' => 'edit_quotation',
                        'label' => 'Edit',
                        'controller' => 'quotation',
                        'method' => 'edit',
                    ),
                    array(
                        'name' => 'download_quotation',
                        'label' => 'Download',
                        'controller' => 'quotation',
                        'method' => 'edit',
                    ),
                    array(
                        'name' => 'delete_quotation',
                        'label' => 'Delete',
                        'controller' => 'quotation',
                        'method' => 'delete',
                    ),
                ),
            ),
            array(
                "name" => "covernote_candidate",
                "label" => "Covernote Candidate",
                "controller" => "covernote_candidate",
                "method" => "index",
            ),
            array(
                "name" => "covernote",
                "label" => "Covernote",
                "controller" => "covernote",
                "method" => "index",
                'action' => array(
                    array(
                        'name' => 'add_covernote',
                        'label' => 'Add',
                        'controller' => 'covernote',
                        'method' => 'add',
                    ),
                    array(
                        'name' => 'edit_covernote',
                        'label' => 'Edit',
                        'controller' => 'covernote',
                        'method' => 'edit',
                    ),
                    array(
                        'name' => 'download_covernote',
                        'label' => 'Download',
                        'controller' => 'covernote',
                        'method' => 'edit',
                    ),
                    array(
                        'name' => 'delete_covernote',
                        'label' => 'Delete',
                        'controller' => 'covernote',
                        'method' => 'delete',
                    ),
                ),
            ),
            array(
                "name" => "endoresement_menu",
                "label" => "Endorsement",
                "controller" => "endorsement",
                "method" => "index",
                'action' => array(
                    array(
                        'name' => 'add_endorsement',
                        'label' => 'Create',
                        'controller' => 'endorsement',
                        'method' => 'add',
                    ),
                    array(
                        'name' => 'edit_endorsement',
                        'label' => 'Edit',
                        'controller' => 'endorsement',
                        'method' => 'edit',
                    ),
                    array(
                        'name' => 'download_endorsement',
                        'label' => 'Download',
                        'controller' => 'endorsement',
                        'method' => 'edit',
                    ),
                    array(
                        'name' => 'delete_endorsement',
                        'label' => 'Delete',
                        'controller' => 'endorsement',
                        'method' => 'delete',
                    ),
                ),
            ),
            array(
                "name" => "renewal_menu",
                "label" => "Renewal",
                "controller" => "renewal",
                "method" => "index",
                'action' => array(
                    array(
                        'name' => 'add_renewal',
                        'label' => 'Create',
                        'controller' => 'renewal',
                        'method' => 'add',
                    ),
                    array(
                        'name' => 'edit_renewal',
                        'label' => 'Edit',
                        'controller' => 'renewal',
                        'method' => 'edit',
                    ),
                    array(
                        'name' => 'download_renewal',
                        'label' => 'Download',
                        'controller' => 'renewal',
                        'method' => 'edit',
                    ),
                    array(
                        'name' => 'delete_renewal',
                        'label' => 'Delete',
                        'controller' => 'renewal',
                        'method' => 'delete',
                    ),
                ),
            ),
        ),
    ),
    array(
        "name" => "report_menu",
        "label" => "Report",
        "icon" => "file",
        "subnav" => include dirname(__FILE__) . "/subnav/report.php",
    ),
    array(
        "name" => "report_analyze",
        "label" => "Analyze",
        "icon" => "bar-chart",
        "subnav" => include dirname(__FILE__) . "/subnav/analyze.php",
    ),
    array(
        "name" => "log_list",
        "label" => "Log",
        "icon" => "file-text",
        "subnav" => include dirname(__FILE__) . "/subnav/log.php",
    ), //end log_list
    array(
        "name" => "setting_menu",
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
            array(
                "name" => "currency",
                "label" => "Currency",
                "controller" => "currency",
                "method" => "index",
                'action' => array(
                    array(
                        'name' => 'add_currency',
                        'label' => 'Add',
                        'controller' => 'currency',
                        'method' => 'add',
                    ),
                    array(
                        'name' => 'edit_currency',
                        'label' => 'Edit',
                        'controller' => 'currency',
                        'method' => 'edit',
                    ),
                    array(
                        'name' => 'delete_currency',
                        'label' => 'Delete',
                        'controller' => 'currency',
                        'method' => 'delete',
                    ),
                ),
            ),
        ),
    ),
);

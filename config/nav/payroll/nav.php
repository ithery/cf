<?php



return array(
    array(
        "name"=>"dashboard",
        "label"=>"Dashboard",
        "controller"=>"home",
        "method"=>"index",
        "icon"=>"home",
    ),
    array(
        "name"=>"test",
        "label"=>"Test",
        "icon"=>"smile",
        "subnav"=>array(
            array(
                "name"=>"siswa",
                "label"=>"Siswa",
                "controller"=>"siswa",
                "method"=>"index",
                "action"=>array(
                        array(
                                'name'=>'add_siswa',
                                'label'=>'Add',
                                'controller'=>'siswa',
                                'method'=>'add',
                        ),
                        array(
                                'name'=>'edit_siswa',
                                'label'=>'Edit',
                                'controller'=>'siswa',
                                'method'=>'edit',
                        ),
						array(
                                'name'=>'delete_siswa',
                                'label'=>'Delete',
                                'controller'=>'siswa',
                                'method'=>'delete',
                        ),
                ),
            ),

        ),
    ),
   
);



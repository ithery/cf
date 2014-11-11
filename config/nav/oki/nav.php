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
        "name" => "data",
        "label" => "Data",
        "icon" => "user",
        "subnav" => array(
            array(
                "name" => "jabatan",
                "label" => "Jabatan",
                "controller" => "jabatan",
                "method" => "index",
                ),
            array(
                "name" => "pegawai",
                "label" => "Pegawai",
                "controller" => "pegawai",
                "method" => "index",
            ),
        ),
    ),
);

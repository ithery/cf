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
        "name" => "akademik",
        "label" => "Akademik",
        "icon" => "list",
        "subnav" => array(
            array(
                "name" => "tahun_ajaran",
                "label" => "Tahun Ajaran",
                "controller" => "tahun_ajaran",
                "method" => "index",
            ),
            array(
                "name" => "kelas",
                "label" => "Kelas",
                "controller" => "kelas",
                "method" => "index",
            ),
            array(
                "name" => "siswa",
                "label" => "Siswa",
                "controller" => "siswa",
                "method" => "index",
            ),
            array(
                "name" => "guru",
                "label" => "Guru",
                "controller" => "guru",
                "method" => "index",
            ),
            array(
                "name" => "wali_kelas",
                "label" => "Wali Kelas",
                "controller" => "wali_kelas",
                "method" => "index",
            ),
            array(
                "name" => "mata_pelajaran",
                "label" => "Mata Pelajaran",
                "controller" => "mata_pelajaran",
                "method" => "index",
            ),
        ),
    ),
    array(
        "name" => "Sekolah",
        "label" => "Sekolah",
        "icon" => "building ",
        "subnav" => array(
            array(
                "name" => "Jadwal",
                "label" => "Jadwal",
                "subnav" => array(
                    array(
                        "name" => "Jadwal Pelajaran",
                        "label" => "Jadwal Pelajaran",
                        "controller" => "jadwal_pelajaran",
                        "method" => "index",
                    ),
                    array(
                        "name" => "Jadwal Mengajar",
                        "label" => "Jadwal Mengajar",
                        "controller" => "jadwal_mengajar",
                        "method" => "index",
                    ),
                ),
            ),
            array(
                "name" => "nilai",
                "label" => "Nilai",
                "subnav" => array(
                    array(
                        "name" => "Nilai Siswa",
                        "label" => "Nilai Siswa",
                        "controller" => "nilai",
                        "method" => "index",
                    ),
                    array(
                        "name" => "Raport",
                        "label" => "Raport",
                        "controller" => "raport",
                        "method" => "index",
                    ),
                ),
            ),
        ),
    ),
);

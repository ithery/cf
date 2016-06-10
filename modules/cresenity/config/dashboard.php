<?php

    return array(
        "table" => array(
            "type" => 'table',
            "class" => 'CDashboard_Table',
            "options" => array(
                "title" => array(
                    "name" => "title",
                    "label" => "Title",
                    "type" => "text",
                ),
                "query" => array(
                    "name" => "query",
                    "label" => "Query",
                    "type" => "textarea",
                ),
                "columns" => array(
                    "name" => "columns",
                    "label" => "Column",
                    "type" => "text",
                ),
            ),
        ),
        "last_user_online" => array(
            "type" => 'last_user_online',
            "class" => 'CDashboard_LastUserOnline',
            "options" => array(
                "total_user" => array(
                    "name" => "total_user",
                    "label" => "Total User",
                    "type" => "select",
                    "list" => array("3" => "3", "5" => "5", "8" => "8", "10" => "10")
                ),
            ),
        ),
        "calendar" => array(
            "type" => 'calendar',
            "class" => 'CDashboard_Calendar',
            "options" => array(
            ),
        ),
        "listview" => array(
            "type" => 'listview',
            "class" => 'CDashboard_ListView',
            "options" => array(
            ),
        ),
    );
    
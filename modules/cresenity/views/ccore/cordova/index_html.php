<?php

?>
<!DOCTYPE html>
<!--
    Copyright (c) 2012-2016 Adobe Systems Incorporated. All rights reserved.

    Licensed to the Apache Software Foundation (ASF) under one
    or more contributor license agreements.  See the NOTICE file
    distributed with this work for additional information
    regarding copyright ownership.  The ASF licenses this file
    to you under the Apache License, Version 2.0 (the
    "License"); you may not use this file except in compliance
    with the License.  You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing,
    software distributed under the License is distributed on an
    "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
     KIND, either express or implied.  See the License for the
    specific language governing permissions and limitations
    under the License.
-->
<html>

    <head>
        <meta name="format-detection" content="telephone=no">
        <meta name="msapplication-tap-highlight" content="no">
        <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width">
        <link rel="stylesheet" type="text/css" href="css/index.css">
        <title>WELCOME</title>
        <style>
            body {
                background-color: #F60000;
            }
            #splash_screen {
                display: block;
                height: 100%;
                left: 0;
                position: absolute;
                width: 100%;
                top: 0;
                z-index: 100;
            }
            .hide {
                display: none !important;
            }
        </style>
    </head>

    <body id="main" class="main">
        <div id="app" class="app">
            <div id="deviceready" class="blink">
                <p class="event listening" id="message_manifest">WELCOME</p>
            </div>
        </div>
        <script type="text/javascript" src="cordova.js"></script>
        <script type="text/javascript" src="js/painlessfs.js"></script>
        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script type="text/javascript" src="js/SocialSharing.js"></script>
        <script type="text/javascript" src="js/index.js"></script>
    </body>

</html>
    
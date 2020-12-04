<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (CF::isDevSuite()) {

    CApp_Administrator::addNav([
        "name" => "administrator.cresenity",
        "label" => "Cresenity",
        "icon" => " lnr lnr-moon",
        "subnav" => [
            [
                "name" => "administrator.cresenity.documentation",
                "label" => "Documentation",
                "controller" => 'administrator/cresenity/documentation',
                "method" => 'index',
            ],
        ],
    ]);
}

CApp::component()->registerComponent('counter', \Cresenity\Component\Counter::class);

CApp::component()->registerComponent('member-table', \Cresenity\Testing\MemberTableComponent::class);
CApp::component()->registerComponent('test-validate', \Cresenity\Testing\ValidateTestComponent::class);
CApp::component()->registerComponent('test-upload', \Cresenity\Testing\UploadTestComponent::class);

/*
  CRouting::router()->get('posts/{post}/comments/{comment}', function ($postId, $commentId) {
  return $postId.'|'.$commentId;
  });
 * 
 */

CRouting::router()->post('posts', function () {
    return 'this is post from router';
});

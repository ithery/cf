<?php

if (CF::isDevSuite()) {
    CApp_Administrator::addNav(
        [
            'name' => 'administrator.cresenity',
            'label' => 'Cresenity',
            'icon' => ' lnr lnr-moon',
            'subnav' => [
                [
                    'name' => 'administrator.cresenity.documentation',
                    'label' => 'Documentation',
                    'controller' => 'administrator/cresenity/documentation',
                    'method' => 'index',
                ],
            ],
        ]
    );
}

CApp::component()->registerComponent('counter', \Cresenity\Component\Counter::class);

CApp::component()->registerComponent('melon', Melon::class);

CApp::component()->registerComponent('member-table', \Cresenity\Testing\MemberTableComponent::class);
CApp::component()->registerComponent('test-validate', \Cresenity\Testing\ValidateTestComponent::class);
CApp::component()->registerComponent('test-upload', \Cresenity\Testing\UploadTestComponent::class);

<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 2, 2019, 10:13:30 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class Controller_Administrator_Cloud_Info_Project extends CApp_Administrator_Controller_User {

    public function tab() {
        $app = CApp::instance();

        $app->addH5()->add('Project Information');
        $errCode = 0;
        $errMessage = '';
        $cloudData = array();
        try {
            $cloudData = CApp_Cloud::instance()->api('Development/GetInfo');
        } catch (Exception $ex) {
            $errCode++;
            $errMessage = $ex->getMessage();
        }
        $divCardColumns = $app->addDiv()->addClass('card-columns');

        $widgetTeams = $divCardColumns->addWidget()->setTitle('Teams')->addClass('card mb-3')->setNoPadding();
        $listGroupTeams = $widgetTeams->addListGroup();
        $listGroupTeams->setDataFromArray(carr::get($cloudData, 'project.teams'));
        $listGroupTeams->setItemCallback(function($item, $data) {
            $divMedia = $item->addDiv()->addClass('media pb-1 mb-3');
            $divMedia->addImg()->setSrc(carr::get($data, 'photoUrl'))->addClass('d-block ui-w-40 rounded-circle');
            $divMediaBody = $divMedia->addDiv()->addClass('media-body flex-truncate ml-3');
            $divMediaBody->addSpan()->addClass('badge badge-outline-success')->add(carr::get($data, 'username'));
        }, __FILE__);

        $widgetApplications = $divCardColumns->addWidget()->setTitle('App Available')->addClass('card mb-3')->setNoPadding();
        $listGroupApplications = $widgetApplications->addListGroup();
        $listGroupApplications->setDataFromArray(carr::get($cloudData, 'project.applications'));
        $listGroupApplications->setItemCallback(function($item, $data) {
            $divMedia = $item->addDiv()->addClass('media pb-1 mb-3');
            $divMedia->addImg()->setSrc(carr::get($data, 'imageUrl'))->addClass('d-block ui-w-40 rounded-circle');
            $divMediaBody = $divMedia->addDiv()->addClass('media-body flex-truncate ml-3');
            $divMediaBody->addSpan()->addClass('badge badge-outline-success')->add(carr::get($data, 'appName'));
        }, __FILE__);

        $widgetMilestone = $divCardColumns->addWidget()->setTitle('Milestone')->addClass('card mb-3')->setNoPadding();
        $listGroupMilestone = $widgetMilestone->addListGroup();
        $listGroupMilestone->setDataFromArray(carr::get($cloudData, 'project.milestones'));
        $listGroupMilestone->setItemCallback(function($item, $data) {
            $divMedia = $item->addDiv()->addClass('media pb-1 mb-3');
            $divMedia->addImg()->setSrc(carr::get($data, 'imageUrl'))->addClass('d-block ui-w-40 rounded-circle');
            $divMediaBody = $divMedia->addDiv()->addClass('media-body flex-truncate ml-3');
            $divMediaBody->addSpan()->addClass('badge badge-outline-success')->add(carr::get($data, 'name'));
        }, __FILE__);


        $widgetEnvironment = $divCardColumns->addWidget()->setTitle('Environment')->addClass('card mb-3')->setNoPadding();
        $listGroupEnvironment = $widgetEnvironment->addListGroup();
        $listGroupEnvironment->setDataFromArray(carr::get($cloudData, 'project.environments'));
        $listGroupEnvironment->setItemCallback(function($item, $data) {
            $divMedia = $item->addDiv()->addClass('media pb-1 mb-3');
            $divMedia->addImg()->setSrc(carr::get($data, 'imageUrl'))->addClass('d-block ui-w-40 rounded-circle');
            $divMediaBody = $divMedia->addDiv()->addClass('media-body flex-truncate ml-3');
            $divMediaBody->addSpan()->addClass('badge badge-outline-success')->add(carr::get($data, 'name'));
        }, __FILE__);
        echo $app->render();
    }

}

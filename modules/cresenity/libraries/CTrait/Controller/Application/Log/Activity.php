<?php

trait CTrait_Controller_Application_Log_Activity {
    public function activity() {
        $app = c::app();
        $logActivityClass = '';

        try {
            $logActivityClass = $this->logActivityModel;
        } catch (Exception $ex) {
            cmsg::add('error', $ex->getMessage());

            return $app;
        }
        $logActivityModel = new $logActivityClass();
        if (!$logActivityModel instanceof CModel) {
            cmsg::add('error', '"logActivityModel" must be instance of CModel');

            return $app;
        }
        $app->add($this->logActivityModel);

        return $app;
    }
}

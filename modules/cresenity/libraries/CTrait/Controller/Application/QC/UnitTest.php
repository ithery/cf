<?php

/**
 * Description of UnitTest
 *
 * @author Hery
 */
trait CTrait_Controller_Application_QC_UnitTest {

    protected function getTitle() {
        return 'Unit Test';
    }

    public function index() {
        $app = CApp::instance();
        $db = CDatabase::instance();

        $app->title($this->getTitle());
        $actionContainer = $app->addDiv()->addClass('action-container mb-3');

        $reloadAction = $actionContainer->addAction()->setLabel('Reload')->addClass('btn-primary')->setIcon('fas fa-sync');

        $tableUnitTestDiv = $app->addDiv('tableUnitTest');

        $handlerActionClick = $reloadAction->addListener('click')->addReloadHandler();
        $handlerActionClick->setTarget('tableUnitTest');
        $handlerActionClick->setUrl($this->controllerUrl() . 'reloadTabUnitTest');
        $handlerActionClick->setBlockerType('shimmer');



        $reloadOptions = array();
        static::reloadTabUnitTest($tableUnitTestDiv, $reloadOptions);

        echo $app->render();
    }

    public static function reloadTabUnitTest($container = null, $options = array()) {
        $app = $container;
        if ($container == null) {
            $app = CApp::instance();
        }
        $qcManager = CQC_Manager::instance();
        $request = $options;
        if ($request == null) {
            $request = CApp_Base::getRequest();
        }
        $db = CDatabase::instance();
        $listUnitTest = $qcManager->unitTests();
        $dataUnitTest = array();
        $groupTab = carr::get($_GET, 'group');
        if ($qcManager->haveUnitTestGroup()) {
            $tabList = $app->addTabList()->setAjax(false);
            $groupKeys = $qcManager->getUnitTestGroupsKey();
            $notGrouped = $qcManager->unitTests(false);
            if (count($notGrouped) > 0) {
                $tab = $tabList->addTab()->setLabel('Not Grouped');
                static::reloadTableUnitTest($tab, ['group' => false]);
            }
            foreach ($groupKeys as $groupName) {
                $tab = $tabList->addTab()->setLabel($groupName);
                if ($groupTab == $groupName) {
                    $tab->setActive();
                }

                static::reloadTableUnitTest($tab, ['group' => $groupName]);
            }
        } else {
            $div = $app->addDiv();
            static::reloadTableUnitTest($div);
        }

        if ($container == null) {
            echo $app->render();
        }
    }

    public static function reloadTableUnitTest($container = null, $options = array()) {
        $app = $container;
        if ($container == null) {
            $app = CApp::instance();
        }
        $qcManager = CQC_Manager::instance();
        $request = $options;
        if ($request == null) {
            $request = CApp_Base::getRequest();
        }
        $db = CDatabase::instance();
        $group = carr::get($request, 'group');
        $listUnitTest = $qcManager->unitTests($group);
        $dataUnitTest = array();
        foreach ($listUnitTest as $kUnitTest => $vUnitTest) {
            $dUnitTest = array();
            $dUnitTest['unit_test_class'] = $kUnitTest;
            $dUnitTest['unit_test_name'] = $vUnitTest;
            $dataUnitTest[] = $dUnitTest;
        }
        $table = $app->addTable();
        $table->setDataFromArray($dataUnitTest);
        $table->addColumn('unit_test_name')->setLabel('Name');
        $table->setTitle('UnitTest List');
        $table->setApplyDataTable(false);
        $table->cellCallbackFunc(array(__CLASS__, 'cellCallback'), __FILE__);

        $table->setRowActionStyle('btn-dropdown');


        $groupQueryString = '';
        if (strlen($group) > 0) {
            $groupQueryString = '?group=' . $group;
        }

        $actMonitor = $table->addRowAction();
        $actMonitor->setIcon("fas fa-file")->setLabel('Detail');
        $actMonitor->setLink(static::controllerUrl() . 'detail/{unit_test_class}' . $groupQueryString);
        $actStart = $table->addRowAction();
        $actStart->setIcon("fas fa-play")->setLabel('Run');
        $actStart->setLink(static::controllerUrl() . 'run/{unit_test_class}' . $groupQueryString)->setConfirm();

        if ($container == null) {
            echo $app->render();
        }
    }

    public static function cellCallback($table, $col, $row, $val) {
        return $val;
    }

    public function detail($class = null) {
        $app = CApp::instance();
        if ($class == null) {
            curl::redirect($this->controllerUrl());
        }
        $name = carr::last(explode("_", $class));

        $app->title('Test Case of ' . $name);
        $app->addBreadcrumb($this->getTitle(), $this->controllerUrl());

        
        $actionContainer = $app->addDiv()->addClass('action-container mb-3');
        $backAction = $actionContainer->addAction()->setLabel('Back')->addClass('btn-primary')->setIcon('fas fa-arrow-left')->setLink(static::controllerUrl() . static::groupQueryString());
        $rotateAction = $actionContainer->addAction()->setLabel('Run All')->addClass('btn-primary')->setIcon('fas fa-play')
                ->setConfirm()
                ->addListener('click')->addHandler('custom')
                ->setJs("
                    $('.btn-run-method').trigger('click');
                    ");
                

        
        $runner = CQC::createUnitTestRunner($class);
        $methods = $runner->getTestMethods();

        foreach ($methods as $method) {


            $template = $app->addTemplate()->setTemplate('CApp/QC/UnitTest/Method');
            $template->setVar('method', $method);
            $template->setVar('name', $name);
            $template->setVar('className', $class);
            $template->setVar('controllerUrl', static::controllerUrl());
        }

        echo $app->render();
    }

    public function check($className, $method = null) {
        $runner = CQC::createUnitTestRunner($className);

        $errCode = 0;
        $errMessage = '';
        $data = [];
        $output = '';
        try {
            if ($method != null) {
                $output = $runner->runMethod($method);
            } else {
                $output = $runner->run();
            }
        } catch (Exception $ex) {
            $errCode++;
            $errMessage = $ex->getMessage();
        }
        $data['output'] = $output;

        echo CApp_Base::jsonResponse($errCode, $errMessage, $data);
    }

    
    /**
     * 
     * @return string
     */
    private static function groupQueryString() {
        $group = carr::get($_GET, 'group');
        $groupQueryString = '';
        if (strlen($group) > 0) {
            $groupQueryString = '?group=' . $group;
        }
        return $groupQueryString;
    }
}

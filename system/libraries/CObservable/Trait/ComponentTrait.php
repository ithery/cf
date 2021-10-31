<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 14, 2018, 8:18:54 PM
 */
trait CObservable_Trait_ComponentTrait {
    /**
     * @param string $id
     *
     * @return CElement_Component_DataTable
     */
    public function addTable($id = '') {
        $table = CElement_Factory::createComponent('DataTable', $id);
        $this->add($table);
        return $table;
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_ListGroup
     */
    public function addListGroup($id = '') {
        $listGroup = CElement_Factory::createComponent('ListGroup', $id);
        $this->add($listGroup);
        return $listGroup;
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_DataGridList
     */
    public function addDataGridList($id = '') {
        $dataGridList = CElement_Factory::createComponent('DataGridList', $id);
        $this->add($dataGridList);
        return $dataGridList;
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_Nestable
     */
    public function addNestable($id = '') {
        $nestable = CElement_Factory::createComponent('Nestable', $id);
        $this->add($nestable);
        return $nestable;
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_Terminal
     */
    public function addTerminal($id = '') {
        $terminal = CElement_Factory::createComponent('Terminal', $id);
        $this->add($terminal);
        return $terminal;
    }

    /**
     * @param string $type
     * @param string $id
     *
     * @return CElement_Component_Chart
     */
    public function addChart($type = 'Chart', $id = '') {
        $chart = CElement_Component_Chart::factory($type, $id);
        $this->add($chart);
        return $chart;
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_ElFinder
     */
    public function addElFinder($id = '') {
        $elFinder = CElement_Component_ElFinder::factory($id);
        $this->add($elFinder);
        return $elFinder;
    }

    public function addFileManager($id = '') {
        $fileManager = CElement_Component_FileManager::factory($id);
        $this->add($fileManager);
        return $fileManager;
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_Widget
     */
    public function addWidget($id = '') {
        $widget = CElement_Factory::createComponent('Widget', $id);
        $this->add($widget);
        return $widget;
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_Form
     */
    public function addForm($id = '') {
        $form = CElement_Factory::createComponent('Form', $id);
        $this->add($form);
        return $form;
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_Form
     */
    public function addKanban($id = '') {
        $kanban = CElement_Factory::createComponent('Kanban', $id);
        $this->add($kanban);
        return $kanban;
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_PdfViewer
     */
    public function addPdfViewer($id = '') {
        $pdfViewer = CElement_Factory::createComponent('PdfViewer', $id);
        $this->add($pdfViewer);
        return $pdfViewer;
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_TreeView
     */
    public function addTreeView($id = '') {
        $treeView = CElement_Factory::createComponent('TreeView', $id);
        $this->add($treeView);
        return $treeView;
    }
}

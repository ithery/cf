<?php
use CVendor_LiteSpeed_Info as Info;
use CVendor_LiteSpeed_Node as Node;

class CVendor_LiteSpeed_Page {
    private $_id;

    private $_label;

    private $_tblmap;

    private $_printdone;

    private $_disp_tid;

    private $_disp_ref;

    private $_extended;

    private $_linked_tbls;

    public function __construct($id, $label, $tblmap) {
        $this->_id = $id;
        $this->_label = $label;
        $this->_tblmap = $tblmap;
    }

    public function GetID() {
        return $this->_id;
    }

    public function GetLabel() {
        return $this->_label;
    }

    public function GetTblMap() {
        return $this->_tblmap;
    }

    public function PrintHtml($disp) {
        $this->_disp_tid = $disp->Get(Info::FLD_TID);
        $this->_disp_ref = $disp->Get(Info::FLD_REF);

        $this->_linked_tbls = null;
        $this->_extended = true;
        if ($this->_disp_tid == '') {
            $this->_extended = false;
        } elseif (($last = strrpos($this->_disp_tid, '`')) > 0) {
            $this->_disp_tid = substr($this->_disp_tid, $last + 1);
        }

        if (($topmesg = $disp->Get(Info::FLD_TOP_MSG)) != null) {
            foreach ($topmesg as $tm) {
                echo UIBase::message('', $tm, 'error');
            }
        }

        $root = $disp->Get(Info::FLD_PG_DATA);
        if ($root == null) {
            return;
        }

        if ($root->Get(Node::FLD_KEY) == Node::K_EXTRACTED) {
            $this->print_tbl($this->_disp_tid, $root, $disp);
        } else {
            $this->_printdone = false;
            $this->printMap($this->_tblmap, $root, $disp);
        }

        if ($disp->IsViewAction() && $this->_linked_tbls != null) {
            $this->_extended = true;
            $disp->SetPrintingLinked(true);
            foreach ($this->_linked_tbls as $lti) {
                $this->_disp_tid = $lti;
                $this->_disp_ref = $disp->Get(Info::FLD_REF);
                $this->_printdone = false;
                $this->printMap($this->_tblmap, $root, $disp);
            }
            $disp->SetPrintingLinked(false);
        }
    }

    private function printMap($tblmap, $node, $disp) {
        $dlayer = ($node == null) ? null : $node->LocateLayer($tblmap->GetLoc());
        $maps = $tblmap->GetMaps($this->_extended);
        foreach ($maps as $m) {
            if (is_a($m, 'DTblMap')) {
                if (is_array($dlayer)) {
                    $ref = $this->_disp_ref;
                    if (($first = strpos($ref, '`')) > 0) {
                        $this->_disp_ref = substr($ref, $first + 1);
                        $ref = substr($ref, 0, $first);
                    } else {
                        $this->_disp_ref = '';
                    }
                    $dlayer = $dlayer[$ref];
                }
                $this->printMap($m, $dlayer, $disp);
                if ($this->_printdone) {
                    break;
                }
            } else {
                if ($m != null && ($this->_disp_tid == '' || $this->_disp_tid == $m)) {
                    $this->print_tbl($m, $dlayer, $disp);
                    if ($this->_disp_tid == $m) {
                        $this->_printdone = true;

                        break;
                    }
                }
            }
        }
    }

    private function print_tbl($tid, $dlayer, $disp) {
        $tbl = DTblDef::getInstance()->GetTblDef($tid);
        $tbl->PrintHtml($dlayer, $disp);

        if (($linked = $tbl->Get(DTbl::FLD_LINKEDTBL)) != null) {
            if ($this->_linked_tbls == null) {
                $this->_linked_tbls = $linked;
            } else {
                $this->_linked_tbls = array_merge($this->_linked_tbls, $linked);
            }
        }
    }
}

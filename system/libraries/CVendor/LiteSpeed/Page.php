<?php
use CVendor_LiteSpeed_Info as Info;
use CVendor_LiteSpeed_Node as Node;
use CVendor_LiteSpeed_UIBase as UIBase;

class CVendor_LiteSpeed_Page {
    private $id;

    private $label;

    private $tblmap;

    private $printdone;

    private $dispTid;

    private $dispRef;

    private $extended;

    private $linkedTbls;

    public function __construct($id, $label, $tblmap) {
        $this->id = $id;
        $this->label = $label;
        $this->tblmap = $tblmap;
    }

    public function getID() {
        return $this->id;
    }

    public function getLabel() {
        return $this->label;
    }

    public function getTblMap() {
        return $this->tblmap;
    }

    public function printHtml($disp) {
        $this->dispTid = $disp->get(Info::FLD_TID);
        $this->dispRef = $disp->get(Info::FLD_REF);

        $this->linkedTbls = null;
        $this->extended = true;
        if ($this->dispTid == '') {
            $this->extended = false;
        } elseif (($last = strrpos($this->dispTid, '`')) > 0) {
            $this->dispTid = substr($this->dispTid, $last + 1);
        }

        if (($topmesg = $disp->get(Info::FLD_TOP_MSG)) != null) {
            foreach ($topmesg as $tm) {
                echo UIBase::message('', $tm, 'error');
            }
        }

        $root = $disp->get(Info::FLD_PG_DATA);
        if ($root == null) {
            return;
        }

        if ($root->get(Node::FLD_KEY) == Node::K_EXTRACTED) {
            $this->print_tbl($this->dispTid, $root, $disp);
        } else {
            $this->printdone = false;
            $this->printMap($this->tblmap, $root, $disp);
        }

        if ($disp->IsViewAction() && $this->linkedTbls != null) {
            $this->extended = true;
            $disp->SetPrintingLinked(true);
            foreach ($this->linkedTbls as $lti) {
                $this->dispTid = $lti;
                $this->dispRef = $disp->get(Info::FLD_REF);
                $this->printdone = false;
                $this->printMap($this->tblmap, $root, $disp);
            }
            $disp->SetPrintingLinked(false);
        }
    }

    private function printMap($tblmap, $node, $disp) {
        $dlayer = ($node == null) ? null : $node->LocateLayer($tblmap->GetLoc());
        $maps = $tblmap->GetMaps($this->extended);
        foreach ($maps as $m) {
            if (is_a($m, 'DTblMap')) {
                if (is_array($dlayer)) {
                    $ref = $this->dispRef;
                    if (($first = strpos($ref, '`')) > 0) {
                        $this->dispRef = substr($ref, $first + 1);
                        $ref = substr($ref, 0, $first);
                    } else {
                        $this->dispRef = '';
                    }
                    $dlayer = $dlayer[$ref];
                }
                $this->printMap($m, $dlayer, $disp);
                if ($this->printdone) {
                    break;
                }
            } else {
                if ($m != null && ($this->dispTid == '' || $this->dispTid == $m)) {
                    $this->print_tbl($m, $dlayer, $disp);
                    if ($this->dispTid == $m) {
                        $this->printdone = true;

                        break;
                    }
                }
            }
        }
    }

    private function print_tbl($tid, $dlayer, $disp) {
        $tbl = CVendor_LiteSpeed_OWS_TblDef::getInstance()->getTblDef($tid);
        $tbl->PrintHtml($dlayer, $disp);

        if (($linked = $tbl->get(CVendor_LiteSpeed_Tbl::FLD_LINKEDTBL)) != null) {
            if ($this->linkedTbls == null) {
                $this->linkedTbls = $linked;
            } else {
                $this->linkedTbls = array_merge($this->linkedTbls, $linked);
            }
        }
    }
}

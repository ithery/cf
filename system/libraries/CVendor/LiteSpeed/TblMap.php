<?php
use CVendor_LiteSpeed_Tbl as Tbl;
use CVendor_LiteSpeed_Node as Node;
use CVendor_LiteSpeed_OWS_Attr as Attr;

use CVendor_LiteSpeed_OWS_TblDef as TblDef;

class CVendor_LiteSpeed_TblMap {
    private $_layer;

    private $_map;  // array of  string: tid or submap

    private $_extended_map;

    public function __construct($layer, $map, $extended_map = null) {
        $this->_layer = $layer;
        $this->_map = $map;
        $this->_extended_map = $extended_map;
    }

    public function GetLoc($index = 0) {
        return is_array($this->_layer) ? $this->_layer[$index] : $this->_layer;
    }

    public function GetMaps($extended) {
        $maps = is_array($this->_map) ? $this->_map : [$this->_map];

        if ($extended && $this->_extended_map != null) {
            if (is_array($this->_extended_map)) {
                $maps = array_merge($maps, $this->_extended_map);
            } else {
                $maps[] = $this->_extended_map;
            }
        }

        return $maps;
    }

    public function FindTblLoc($tid) {
        $location = $this->_layer; // page data, layer is not array
        $maps = $this->GetMaps(true);

        foreach ($maps as $m) {
            if ($m instanceof CVendor_LiteSpeed_TblMap) {
                $nextloc = $m->FindTblLoc($tid);
                if ($nextloc != null) {
                    return ($location == '') ? $nextloc : "{$location}:${nextloc}";
                }
            } elseif ($tid == $m) {
                return $location;
            }
        }

        return null;
    }

    public function Convert($srcloc_index, $srcnode, $dstloc_index, $dstnode) {
        $srcloc = $this->GetLoc($srcloc_index);
        $dstloc = $this->GetLoc($dstloc_index);

        $srclayer = $srcnode->LocateLayer($srcloc);
        if ($srclayer == null) {
            return;
        }

        $tonode = $dstnode->AllocateLayerNode($dstloc);

        $is_multi = (strpos($dstloc, '*') !== false);
        $map = $this->GetMaps(false);

        if ($is_multi) {
            // get last layer
            $k0 = strrpos($dstloc, ':');
            if ($k0 === false) {
                $k0 = 0;
            } else {
                $k0++;
            }
            $type = Node::BM_BLK | Node::BM_MULTI;
            $k1 = strrpos($dstloc, '$');
            if ($k1 === false) {
                $key = ($k0 > 0) ? substr($dstloc, $k0) : $dstloc;
            } else {
                $key = substr($dstloc, $k0, $k1 - $k0);
                $type |= Node::BM_VAL;
            }
            $key = ltrim($key, '*');

            if (!is_array($srclayer)) {
                $srclayer = [$srclayer];
            }

            foreach ($srclayer as $fromnode) {
                $child = new Node($key, null, $type); // value will be set later
                $this->convert_map($map, $srcloc_index, $fromnode, $dstloc_index, $child);
                $tonode->AddChild($child);
            }
        } else {
            $this->convert_map($map, $srcloc_index, $srclayer, $dstloc_index, $tonode);
        }
    }

    private function convert_map($map, $srcloc_index, $srcnode, $dstloc_index, $dstnode) {
        foreach ($map as $m) {
            if ($m instanceof CVendor_LiteSpeed_TblMap) {
                $m->Convert($srcloc_index, $srcnode, $dstloc_index, $dstnode);
            } else {
                $this->convert_tbl($m, $srcnode, $dstnode);
            }
        }
    }

    private function convert_tbl($tid, $srcnode, $dstnode) {
        $tbl = TblDef::GetInstance()->GetTblDef($tid);
        $attrs = $tbl->Get(Tbl::FLD_DATTRS);
        $index = $tbl->Get(Tbl::FLD_INDEX);

        foreach ($attrs as $attr) {
            if ($attr == null || $attr->_type == 'action' || $attr->IsFlagOn(Attr::BM_NOFILE)) {
                continue;
            }

            $key = $attr->GetKey();
            $layerpos = strpos($key, ':');
            if ($layerpos > 0) {
                $layer = substr($key, 0, $layerpos);
                $key = substr($key, $layerpos + 1);
                $snode = $srcnode->LocateLayer($layer);
                if ($snode == null) {
                    //echo "attr layer loc $layer return null\n";
                    continue;
                }
                $dnode = $dstnode->AllocateLayerNode($layer);
            } else {
                $snode = $srcnode;
                $dnode = $dstnode;
            }

            $from = $snode->GetChildren($key);
            if ($from == null) {
                $val = ($key == $index) ? $snode->Get(Node::FLD_VAL) : '';
                if ($val == '' && !$attr->IsFlagOn(Attr::BM_NOTNULL)) {
                    continue;
                }
                $from = new Node($key, $val);
            } else {
                $snode->RemoveChild($key);
            }

            if (is_array($from)) {
                foreach ($from as $fnode) {
                    $fnode->Set(Node::FLD_PRINTKEY, $key);
                    $dnode->AddChild($fnode);
                }
            } else {
                $from->Set(Node::FLD_PRINTKEY, $key);
                $dnode->AddChild($from);
                if ($key == $index) {
                    $from->AddFlag(Node::BM_IDX);
                    $dnode->SetVal($from->Get(Node::FLD_VAL));
                }
                if ($attr->IsFlagOn(Attr::BM_RAWDATA)) {
                    $from->AddFlag(Node::BM_RAW);
                }
            }
        }

        if (($subtid = $tbl->GetSubTid($dstnode)) != null) {
            $this->convert_tbl($subtid, $srcnode, $dstnode);
        }

        if (!$srcnode->HasDirectChildren()) {
            $srcnode->RemoveFromParent();
        }
    }
}

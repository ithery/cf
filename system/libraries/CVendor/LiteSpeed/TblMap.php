<?php
use CVendor_LiteSpeed_Tbl as Tbl;
use CVendor_LiteSpeed_Node as Node;
use CVendor_LiteSpeed_OWS_Attr as Attr;

use CVendor_LiteSpeed_OWS_TblDef as TblDef;

class CVendor_LiteSpeed_TblMap {
    private $layer;

    private $map;  // array of  string: tid or submap

    private $extendedMap;

    public function __construct($layer, $map, $extendedMap = null) {
        $this->layer = $layer;
        $this->map = $map;
        $this->extendedMap = $extendedMap;
    }

    public function getLoc($index = 0) {
        return is_array($this->layer) ? $this->layer[$index] : $this->layer;
    }

    public function getMaps($extended) {
        $maps = is_array($this->map) ? $this->map : [$this->map];

        if ($extended && $this->extendedMap != null) {
            if (is_array($this->extendedMap)) {
                $maps = array_merge($maps, $this->extendedMap);
            } else {
                $maps[] = $this->extendedMap;
            }
        }

        return $maps;
    }

    public function findTblLoc($tid) {
        $location = $this->layer; // page data, layer is not array
        $maps = $this->getMaps(true);

        foreach ($maps as $m) {
            if ($m instanceof CVendor_LiteSpeed_TblMap) {
                $nextloc = $m->findTblLoc($tid);
                if ($nextloc != null) {
                    return ($location == '') ? $nextloc : "{$location}:${nextloc}";
                }
            } elseif ($tid == $m) {
                return $location;
            }
        }

        return null;
    }

    public function convert($srcloc_index, Node $srcnode, $dstloc_index, Node $dstnode) {
        $srcloc = $this->getLoc($srcloc_index);
        $dstloc = $this->getLoc($dstloc_index);

        $srclayer = $srcnode->locateLayer($srcloc);
        if ($srclayer == null) {
            return;
        }

        $tonode = $dstnode->allocateLayerNode($dstloc);

        $is_multi = (strpos($dstloc, '*') !== false);
        $map = $this->getMaps(false);

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
                $this->convertMap($map, $srcloc_index, $fromnode, $dstloc_index, $child);
                $tonode->addChild($child);
            }
        } else {
            $this->convertMap($map, $srcloc_index, $srclayer, $dstloc_index, $tonode);
        }
    }

    private function convertMap($map, $srcloc_index, $srcnode, $dstloc_index, $dstnode) {
        foreach ($map as $m) {
            if ($m instanceof CVendor_LiteSpeed_TblMap) {
                $m->convert($srcloc_index, $srcnode, $dstloc_index, $dstnode);
            } else {
                $this->convertTbl($m, $srcnode, $dstnode);
            }
        }
    }

    private function convertTbl($tid, $srcnode, $dstnode) {
        $tbl = TblDef::getInstance()->getTblDef($tid);
        $attrs = $tbl->get(Tbl::FLD_DATTRS);
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
                    $fnode->set(Node::FLD_PRINTKEY, $key);
                    $dnode->AddChild($fnode);
                }
            } else {
                $from->set(Node::FLD_PRINTKEY, $key);
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

        if (($subtid = $tbl->getSubTid($dstnode)) != null) {
            $this->convertTbl($subtid, $srcnode, $dstnode);
        }

        if (!$srcnode->HasDirectChildren()) {
            $srcnode->RemoveFromParent();
        }
    }
}

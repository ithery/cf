<?php
use CVendor_LiteSpeed_Info as Info;
use CVendor_LiteSpeed_Node as Node;
use CVendor_LiteSpeed_PathTool as PathTool;
use CVendor_LiteSpeed_OWS_PageDef as PageDef;

class CVendor_LiteSpeed_Data {
    private $type; //{'serv','admin','vh','tp','special'}

    private $id;

    /**
     * Root Node.
     *
     * @var Node
     */
    private $root;

    private $path;

    private $xmlpath;

    private $conferr;

    public function __construct($configType, $path, $id = null) {
        $this->type = $configType;
        $this->id = $id;
        $isnew = ($id != null && $id[0] == '`');

        if ($configType == Info::CT_EX) {
            $this->path = $path;
            $this->initSpecial();
        } else {
            $pos = strpos($path, '.xml');
            if ($pos > 0) {
                $this->xmlpath = $path;
                $this->path = substr($path, 0, $pos) . '.conf';
            } else {
                $pos = strpos($path, '.conf');
                if ($pos > 0) {
                    $this->path = $path;
                    $this->xmlpath = substr($path, 0, $pos) . '.xml';
                } else {
                    // assume xml format
                    $this->xmlpath = $path . '.xml'; // forced
                    $this->path = $path . '.conf';
                }
            }
            $this->init($isnew);
        }
    }

    /**
     * @return Node
     */
    public function getRootNode() {
        return $this->root;
    }

    public function getId() {
        return $this->id;
    }

    public function getType() {
        return $this->type;
    }

    public function getConfErr() {
        return $this->conferr;
    }

    public function getChildrenValues($location, $ref = '') {
        $vals = [];
        $layer = $this->root->getChildrenByLoc($location, $ref);
        if ($layer != null) {
            if (is_array($layer)) {
                $vals = array_map('strval', array_keys($layer));
            } else {
                $vals[] = $layer->get(Node::FLD_VAL);
            }
        }

        return $vals;
    }

    public function getChildVal($location, $ref = '') {
        $layer = $this->root->getChildrenByLoc($location, $ref);
        if ($layer != null && $layer instanceof Node) {
            return $layer->get(Node::FLD_VAL);
        } else {
            return null;
        }
    }

    public function getChildNodeById($key, $id) {
        return $this->root->getChildNodeById($key, $id);
    }

    public function setRootNode($nd) {
        $this->root = $nd;
        $this->root->setVal($this->path);
        $this->root->set(Node::FLD_TYPE, Node::T_ROOT);
    }

    public function savePost($extractData, $disp) {
        $tid = $disp->getLast(Info::FLD_TID);
        if ($this->type == Info::CT_EX) {
            $ref = $disp->getLast(Info::FLD_REF);
        } else {
            $ref = $disp->get(Info::FLD_REF);
        }
        $tblmap = PageDef::getPage($disp)->getTblMap();
        $location = $tblmap->FindTblLoc($tid);
        $this->root->updateChildren($location, $ref, $extractData);

        if (($newref = $extractData->get(Node::FLD_VAL)) != null) {
            $this->checkIntegrity($tid, $newref, $disp);
        }

        $this->saveFile();
    }

    public function changeContextSeq($seq) {
        $loc = ($this->type == Info::CT_VH) ? 'context' : 'virtualHostConfig:context';
        if (($ctxs = $this->root->getChildren($loc)) == null) {
            return false;
        }

        if (!is_array($ctxs) || $seq == -1 || $seq == count($ctxs)) {
            return false;
        }

        if ($seq > 0) {
            $index = $seq - 1;
            $switched = $seq;
        } else {
            $index = -$seq - 1;
            $switched = $index - 1;
        }

        $parent = null;
        $uris = array_keys($ctxs);
        $temp = $uris[$switched];
        $uris[$switched] = $uris[$index];
        $uris[$index] = $temp;

        foreach ($uris as $uri) {
            $ctx = $ctxs[$uri];
            if ($parent == null) {
                $parent = $ctx->get(Node::FLD_PARENT);
                $parent->removeChild('context');
            }
            $parent->addChild($ctx);
        }
        $this->saveFile();

        return true;
    }

    public function deleteEntry($disp) {
        $tid = $disp->getLast(Info::FLD_TID);
        if ($this->type == Info::CT_EX) {
            $ref = $disp->getLast(Info::FLD_REF);
        } else {
            $ref = $disp->get(Info::FLD_REF);
        }
        $tblmap = PageDef::getPage($disp)->getTblMap();
        $location = $tblmap->fFindTblLoc($tid);

        $layer = $this->root->getChildrenByLoc($location, $ref);
        if ($layer != null) {
            $layer->removeFromParent();
            $this->checkIntegrity($tid, null, $disp);
            $this->saveFile();
        } else {
            error_log("cannot find delete entry\n");
        }
    }

    public function saveFile() {
        if ($this->type == Info::CT_EX) {
            return $this->saveSpecial();
        }

        $filemap = PageDef::instance()->getFileMap($this->type);   // serv, vh, tp, admin
        $root = $this->saveConfFile($this->root, $filemap, $this->path);

        if (defined('SAVE_XML')) {
            $this->saveXmlFile($root, $filemap, $this->xmlpath);
        }
    }

    private function checkIntegrity($tid, $newref, $disp) {
        if (($ref = $disp->getLast(Info::FLD_REF)) == null || $newref == $ref) {
            return;
        }

        if (in_array($tid, ['ADM_L_GENERAL', 'T_TOPD', 'V_TOPD', 'V_BASE', 'L_GENERAL'])) {
            $disp->Set(Info::FLD_VIEW_NAME, $newref);
        }

        $root = $disp->get(Info::FLD_CONF_DATA)->getRootNode();

        if (($tid == 'V_BASE' || $tid == 'V_TOPD') && ($dlayer = $root->getChildren('listener')) != null) {
            if (!is_array($dlayer)) {
                $dlayer = [$dlayer];
            }

            foreach ($dlayer as $listener) {
                if (($maplayer = $listener->getChildren('vhmap')) != null) {
                    if (!is_array($maplayer)) {
                        $maplayer = [$maplayer];
                    }
                    foreach ($maplayer as $map) {
                        if ($map->get(Node::FLD_VAL) == $ref) {
                            if ($newref == null) {
                                $map->removeFromParent();  // handle delete
                            } else {
                                $map->setVal($newref);
                                if ($map->getChildren('vhost') != null) {
                                    $map->setChildVal('vhost', $newref);
                                }
                            }

                            break;
                        }
                    }
                }
            }
        }

        if ($newref == null) {  // for delete condition, do not auto delete, let user handle
            return;
        }

        if ($tid == 'L_GENERAL' && ($dlayer = $root->getChildren('vhTemplate')) != null) {
            if (!is_array($dlayer)) {
                $dlayer = [$dlayer];
            }

            foreach ($dlayer as $templ) {
                if (($listeners = $templ->getChildVal('listeners')) != null) {
                    $changed = false;
                    $lns = preg_split('/, /', $listeners, -1, PREG_SPLIT_NO_EMPTY);
                    foreach ($lns as $i => $ln) {
                        if ($ln == $ref) {
                            $lns[$i] = $newref;
                            $changed = true;

                            break;
                        }
                    }
                    if ($changed) {
                        $listeners = implode(', ', $lns);
                        $templ->setChildVal('listeners', $listeners);
                    }
                }
            }
        } elseif (strncmp($tid, 'A_EXT_', 6) == 0) {
            $disp_view = $disp->get(Info::FLD_VIEW);
            $loc = ($disp_view == Info::CT_TP) ? 'virtualHostConfig:scripthandler:addsuffix' : 'scripthandler:addsuffix';
            if (($dlayer = $root->getChildren($loc)) != null) {
                if (!is_array($dlayer)) {
                    $dlayer = [$dlayer];
                }

                foreach ($dlayer as $sh) {
                    if ($sh->getChildVal('handler') == $ref) {
                        $sh->setChildVal('handler', $newref);
                    }
                }
            }

            if ($disp_view != Info::CT_SERV) {
                $loc = ($disp_view == Info::CT_TP) ? 'virtualHostConfig:context' : 'context';
                if (($dlayer = $root->getChildren($loc)) != null) {
                    if (!is_array($dlayer)) {
                        $dlayer = [$dlayer];
                    }

                    foreach ($dlayer as $ctx) {
                        if ($ctx->getChildVal('authorizer') == $ref) {
                            $ctx->SetChildVal('authorizer', $newref);
                        }
                        if ($ctx->getChildVal('handler') == $ref) {
                            $ctx->SetChildVal('handler', $newref);
                        }
                    }
                }
            }
        } elseif (strpos($tid, '_REALM_')) { //'T_REALM_FILE','V_REALM_FILE','VT_REALM_LDAP'
            $loc = ($disp->get(Info::FLD_VIEW) == Info::CT_TP) ? 'virtualHostConfig:context' : 'context';
            if (($dlayer = $root->getChildren($loc)) != null) {
                if (!is_array($dlayer)) {
                    $dlayer = [$dlayer];
                }

                foreach ($dlayer as $ctx) {
                    if ($ctx->getChildVal('realm') == $ref) {
                        $ctx->setChildVal('realm', $newref);
                    }
                }
            }
        }
    }

    public function getContent() {
        $root = $this->root;
        $convertedroot = $root->dupHolder();
        $filemap = PageDef::instance()->getFileMap($this->type);
        $filemap->convert(1, $root, 1, $convertedroot);

        $confbuf = '';

        $this->beforeWriteConf($convertedroot);

        $convertedroot->printBuf($confbuf);

        return $confbuf;
    }

    private function saveConfFile($root, $filemap, $filepath) {
        $convertedroot = $root->dupHolder();
        $filemap->convert(1, $root, 1, $convertedroot);

        $confbuf = '';
        $this->beforeWriteConf($convertedroot);
        $convertedroot->printBuf($confbuf);
        if (!defined('_CONF_READONLY_')) {
            $this->writeFile($filepath, $confbuf);
        }

        return $convertedroot;
    }

    private function saveXmlFile($root, $filemap, $filepath) {
        $this->beforeWriteXml($root);
        $xmlroot = $root->dupHolder();
        $filemap->convert(1, $root, 0, $xmlroot);

        $xmlbuf = '';
        $xmlroot->printXmlBuf($xmlbuf);
        $this->writeFile($filepath, $xmlbuf);

        return $xmlroot;
    }

    private function beforeWriteConf(Node $root) {
        if ($this->type == Info::CT_SERV && ($listeners = $root->getChildren('listener')) != null) {
            if (!is_array($listeners)) {
                $listeners = [$listeners];
            }
            foreach ($listeners as $l) {
                if (($maps = $l->getChildren('vhmap')) != null) {
                    if (!is_array($maps)) {
                        $maps = [$maps];
                    }
                    foreach ($maps as $map) {
                        $vn = $map->get(Node::FLD_VAL);
                        $domain = $map->getChildVal('domain');
                        $l->addChild(new Node('map', "${vn} ${domain}"));
                    }
                    $l->removeChild('vhmap');
                }
            }
        }

        if ($this->type == Info::CT_SERV && ($mods = $root->getChildren('module')) != null) {
            if (!is_array($mods)) {
                $mods = [$mods];
            }
            foreach ($mods as $mod) {
                if ($mod->getChildVal('internal') != 1) {
                    $mod->removeChild('internal'); // if not internal, omit this line
                }
            }
        }

        $loc = ($this->type == Info::CT_TP) ? 'virtualHostConfig:scripthandler' : 'scripthandler';
        if (($sh = $root->getChildren($loc)) != null) {
            if (($shc = $sh->getChildren('addsuffix')) != null) {
                if (!is_array($shc)) {
                    $shc = [$shc];
                }
                foreach ($shc as $shcv) {
                    $suffix = $shcv->get(Node::FLD_VAL);
                    $type = $shcv->getChildVal('type');
                    $handler = $shcv->getChildVal('handler');
                    $sh->addChild(new Node('add', "${type}:${handler} ${suffix}"));
                }
                $sh->removeChild('addsuffix');
            }
        }

        if ($this->type == Info::CT_VH || $this->type == Info::CT_TP) {
            $loc = ($this->type == Info::CT_VH) ? 'context' : 'virtualHostConfig:context';
            if (($ctxs = $root->getChildren($loc)) != null) {
                if (!is_array($ctxs)) {
                    $ctxs = [$ctxs];
                }
                $order = 1;
                foreach ($ctxs as $ctx) {
                    if ($ctx->getChildVal('type') === 'null') {
                        $ctx->removeChild('type'); // default is static (null), do not write to file
                    }
                }
            }
        }

        if ($this->type == Info::CT_TP) {
            $vhconf = $root->getChildVal('configFile');
            if (($pos = strpos($vhconf, '.xml')) > 0) {
                $vhconf = substr($vhconf, 0, $pos) . '.conf';
                $root->SetChildVal('configFile', $vhconf);
            }
        }
    }

    private function beforeWriteXml(Node $root) {
        if ($this->type == Info::CT_SERV) {
            if (($listeners = $root->getChildren('listener')) != null) {
                if (!is_array($listeners)) {
                    $listeners = [$listeners];
                }
                foreach ($listeners as $l) {
                    if (($maps = $l->getChildren('map')) != null) {
                        if (!is_array($maps)) {
                            $maps = [$maps];
                        }
                        foreach ($maps as $map) {
                            $mapval = $map->get(Node::FLD_VAL);
                            if (($pos = strpos($mapval, ' ')) > 0) {
                                $vn = substr($mapval, 0, $pos);
                                $domain = trim(substr($mapval, $pos + 1));
                                $anode = new Node('vhmap', $vn);
                                $anode->addChild(new Node('vhost', $vn));
                                $anode->addChild(new Node('domain', $domain));
                                $l->addChild($anode);
                            }
                        }
                        $l->removeChild('map');
                    }
                }
            }

            if (($vhosts = $root->getChildren('virtualhost')) != null) {
                if (!is_array($vhosts)) {
                    $vhosts = [$vhosts];
                }
                foreach ($vhosts as $vh) {
                    $vhconf = $vh->getChildVal('configFile');
                    if (($pos = strpos($vhconf, '.conf')) > 0) {
                        $vhconf = substr($vhconf, 0, $pos) . '.xml';
                        $vh->SetChildVal('configFile', $vhconf);
                    }
                }
            }

            // migrate all tp.xml
            if (($tps = $root->getChildren('vhTemplate')) != null) {
                if (!is_array($tps)) {
                    $tps = [$tps];
                }
                foreach ($tps as $tp) {
                    $tpconf = $tp->getChildVal('templateFile');
                    if (($pos = strpos($tpconf, '.conf')) > 0) {
                        $tpconf = substr($tpconf, 0, $pos) . '.xml';
                        $tp->SetChildVal('templateFile', $tpconf);
                    }
                }
            }
        }

        $loc = ($this->type == Info::CT_TP) ? 'virtualHostConfig:scripthandler' : 'scripthandler';
        if (($sh = $root->getChildren($loc)) != null) {
            if (($shc = $sh->getChildren('add')) != null) {
                if (!is_array($shc)) {
                    $shc = [$shc];
                }
                foreach ($shc as $shcv) {
                    $typeval = $shcv->get(Node::FLD_VAL);
                    if (preg_match("/^(\w+):(\S+)\s+(.+)$/", $typeval, $m)) {
                        $anode = new Node('addsuffix', $m[3]);
                        $anode->addChild(new Node('suffix', $m[3]));
                        $anode->addChild(new Node('type', $m[1]));
                        $anode->addChild(new Node('handler', $m[2]));
                        $sh->addChild($anode);
                    }
                }
                $sh->removeChild('add');
            }
        }

        if ($this->type == Info::CT_VH || $this->type == Info::CT_TP) {
            $loc = ($this->type == Info::CT_VH) ? 'context' : 'virtualHostConfig:context';
            if (($ctxs = $root->getChildren($loc)) != null) {
                if (!is_array($ctxs)) {
                    $ctxs = [$ctxs];
                }
                $order = 1;
                foreach ($ctxs as $ctx) {
                    if ($ctx->getChildVal('type') === 'null') {
                        $ctx->removeChild('type'); // default is static (null), do not write to file
                    }
                }
            }
        }

        if ($this->type == Info::CT_TP) {
            $vhconf = $root->getChildVal('configFile');
            if (($pos = strpos($vhconf, '.conf')) > 0) {
                $vhconf = substr($vhconf, 0, $pos) . '.xml';
                $root->setChildVal('configFile', $vhconf);
            }
        }
    }

    private function afterRead() {
        if ($this->type == Info::CT_SERV) {
            $serverName = $this->root->getChildVal('serverName');
            if ($serverName == '$HOSTNAME' || $serverName == '') {
                $serverName = php_uname('n');
            }
            $this->id = $serverName;

            $runningAs = 'user(' . $this->root->getChildVal('user')
                    . ') : group(' . $this->root->getChildVal('group') . ')';
            $this->root->addChild(new Node('runningAs', $runningAs));

            $mods = $this->root->getChildren('module');
            if ($mods != null) {
                if (!is_array($mods)) {
                    $mods = [$mods];
                }
                foreach ($mods as $mod) {
                    if ($mod->getChildVal('internal') === null) {
                        if ($mod->get(Node::FLD_VAL) == 'cache') {
                            $mod->addChild(new Node('internal', '1'));
                        } else {
                            $mod->addChild(new Node('internal', '0'));
                        }
                    }
                }
            }
        }

        if ($this->type == Info::CT_SERV || $this->type == Info::CT_ADMIN) {
            if (($listeners = $this->root->getChildren('listener')) != null) {
                if (!is_array($listeners)) {
                    $listeners = [$listeners];
                }
                foreach ($listeners as $l) {
                    $addr = $l->getChildVal('address');
                    if ($pos = strrpos($addr, ':')) {
                        $ip = substr($addr, 0, $pos);
                        if ($ip == '*') {
                            $ip = 'ANY';
                        }
                        $l->addChild(new Node('ip', $ip));
                        $l->addChild(new Node('port', substr($addr, $pos + 1)));
                    }
                    if (($maps = $l->getChildren('map')) != null) {
                        if (!is_array($maps)) {
                            $maps = [$maps];
                        }
                        foreach ($maps as $map) {
                            $mapval = $map->get(Node::FLD_VAL);
                            if (($pos = strpos($mapval, ' ')) > 0) {
                                $vn = substr($mapval, 0, $pos);
                                $domain = trim(substr($mapval, $pos + 1));
                                $anode = new Node('vhmap', $vn);
                                $anode->addChild(new Node('vhost', $vn));
                                $anode->addChild(new Node('domain', $domain));
                                $l->addChild($anode);
                            }
                        }
                        $l->removeChild('map');
                    }
                }
            }
        }

        if ($this->type == Info::CT_VH || $this->type == Info::CT_TP) {
            $loc = ($this->type == Info::CT_VH) ? 'context' : 'virtualHostConfig:context';
            if (($ctxs = $this->root->getChildren($loc)) != null) {
                if (!is_array($ctxs)) {
                    $ctxs = [$ctxs];
                }
                $order = 1;
                foreach ($ctxs as $ctx) {
                    if ($ctx->getChildren('type') == null) {
                        $ctx->addChild(new Node('type', 'null')); // default is static (null)
                    }
                    $ctx->addChild(new Node('order', $order++));
                }
            }
        }

        $loc = ($this->type == Info::CT_TP) ? 'virtualHostConfig:scripthandler' : 'scripthandler';
        if (($sh = $this->root->getChildren($loc)) != null) {
            if (($shc = $sh->getChildren('add')) != null) {
                if (!is_array($shc)) {
                    $shc = [$shc];
                }
                foreach ($shc as $shcv) {
                    $typeval = $shcv->get(Node::FLD_VAL);
                    if (preg_match("/^(\w+):(\S+)\s+(.+)$/", $typeval, $m)) {
                        $anode = new Node('addsuffix', $m[3]);
                        $anode->addChild(new Node('suffix', $m[3]));
                        $anode->addChild(new Node('type', $m[1]));
                        $anode->addChild(new Node('handler', $m[2]));
                        $sh->addChild($anode);
                    }
                }
                $sh->removeChild('add');
            }
        }
    }

    private function init($isnew) {
        if ($isnew) {
            if (!file_exists($this->path) && !PathTool::createFile($this->path, $err)) {
                $this->conferr = 'Failed to create config file at ' . $this->path;

                return false;
            } else {
                $this->root = new Node(Node::K_ROOT, $this->path, Node::T_ROOT);

                return true;
            }
        }

        if (!file_exists($this->path) || filesize($this->path) < 10) {
            if ($this->type == Info::CT_SERV) {
                if (file_exists($this->xmlpath) && !$this->migrateAllXml2Conf()) {
                    return false;
                } else {
                    $this->conferr = 'Failed to find config file at ' . $this->path;

                    return false;
                }
            } else {
                if (file_exists($this->xmlpath)) {
                    if (!$this->migrate_xml2conf()) {
                        return false;
                    }
                } else {// treat as new vh or tp
                    $this->root = new Node(Node::K_ROOT, $this->path, Node::T_ROOT);

                    return true;
                }
            }
        }

        $parser = new CVendor_LiteSpeed_Parser_PlainConfParser();
        $this->root = $parser->parse($this->path);
        if ($this->root->hasFatalErr()) {
            $this->conferr = $this->root->getErr();
            error_log('fatel err ' . $this->root->getErr());

            return false;
        }
        if ($parser->hasInclude()) {
            // readonly
            if (!defined('_CONF_READONLY_')) {
                define('_CONF_READONLY_', true);
            }
        }

        $this->afterRead();

        return true;
    }

    private function initSpecial() {
        $lines = file($this->path);
        if ($lines === false) {
            return false;
        }

        $this->root = new Node(Node::K_ROOT, $this->id, Node::T_ROOT);
        $items = [];

        if ($this->id == 'MIME') {
            foreach ($lines as $line) {
                if (($c = strpos($line, '=')) > 0) {
                    $suffix = trim(substr($line, 0, $c));
                    $type = trim(substr($line, $c + 1));
                    $m = new Node('index', $suffix);
                    $m->addChild(new Node('suffix', $suffix));
                    $m->addChild(new Node('type', $type));
                    $items[$suffix] = $m;
                }
            }
        } elseif ($this->id == 'ADMUSR' || $this->id == 'V_UDB') {
            foreach ($lines as $line) {
                $parsed = explode(':', trim($line));
                $size = count($parsed);
                if ($size == 2 || $size == 3) {
                    $name = trim($parsed[0]);
                    $pass = trim($parsed[1]);
                    if ($name != '' && $pass != '') {
                        $u = new Node('index', $name);
                        $u->addChild(new Node('name', $name));
                        $u->addChild(new Node('passwd', $pass));
                        if ($size == 3 && (($group = trim($parsed[2])) != '')) {
                            $u->addChild(new Node('group', $group));
                        }
                        $items[$name] = $u;
                    }
                }
            }
        } elseif ($this->id == 'V_GDB') {
            foreach ($lines as $line) {
                $parsed = explode(':', trim($line));
                if (count($parsed) == 2) {
                    $group = trim($parsed[0]);
                    $users = trim($parsed[1]);
                    if ($group != '') {
                        $g = new Node('index', $group);
                        $g->addChild(new Node('name', $group));
                        $g->addChild(new Node('users', $users));
                        $items[$group] = $g;
                    }
                }
            }
        }

        ksort($items, SORT_STRING);
        reset($items);
        foreach ($items as $item) {
            $this->root->addChild($item);
        }

        return true;
    }

    private function saveSpecial() {
        $fd = fopen($this->path, 'w');
        if (!$fd) {
            return false;
        }

        $items = $this->root->getChildren('index');

        if ($items != null) {
            if (is_array($items)) {
                ksort($items, SORT_STRING);
                reset($items);
            } else {
                $items = [$items];
            }

            foreach ($items as $key => $item) {
                $line = '';
                if ($this->id == 'MIME') {
                    $line = str_pad($key, 8) . ' = ' . $item->getChildVal('type') . "\n";
                } elseif ($this->id == 'ADMUSR' || $this->id == 'V_UDB') {
                    $line = $item->getChildVal('name') . ':' . $item->getChildVal('passwd');
                    $group = $item->getChildVal('group');
                    if ($group != null) {
                        $line .= ':' . $group;
                    }
                    $line .= "\n";
                } elseif ($this->id == 'V_GDB') {
                    $line = $item->getChildVal('name') . ':' . $item->getChildVal('users') . "\n";
                }
                fputs($fd, $line);
            }
        }
        fclose($fd);

        return true;
    }

    private function migrate_xml2conf() {
        error_log("Migrating {$this->xmlpath} \n");
        $xmlparser = new XmlParser();
        $xmlroot = $xmlparser->Parse($this->xmlpath);
        if ($xmlroot->HasFatalErr()) {
            $this->conferr = $xmlroot->get(Node::FLD_ERR);

            return false;
        }

        $root = $xmlroot->DupHolder();
        $filemap = DPageDef::GetInstance()->getFileMap($this->type);   // serv, vh, tp, admin
        $filemap->convert(0, $xmlroot, 1, $root);

        $buf = '';
        $this->beforeWriteConf($root);
        $root->PrintBuf($buf);
        touch($this->path);

        $this->writeFile($this->path, $buf);
        $this->copyPermission($this->xmlpath, $this->path);

        $migrated = $this->xmlpath . '.migrated.' . time();
        if (defined('SAVE_XML')) {
            copy($this->xmlpath, $migrated);
        } else {
            rename($this->xmlpath, $migrated);
        }

        if (defined('RECOVER_SCRIPT')) {
            file_put_contents(RECOVER_SCRIPT, "mv ${migrated} {$this->xmlpath}\n", FILE_APPEND);
        }
        error_log("  converted {$this->xmlpath} to {$this->path}\n\n");

        return true;
    }

    private function copyPermission($fromfile, $tofile) {
        $owner = fileowner($fromfile);
        if (fileowner($tofile) != $owner) {
            chown($tofile, $owner);
        }
        $perm = fileperms($fromfile);
        if (fileperms($tofile) != $perm) {
            chmod($tofile, $perm);
        }
    }

    private function migrateAllXml2Conf() {
        error_log("Migrating all config from server xml config {$this->xmlpath} \n");
        $xmlparser = new XmlParser();
        $xmlroot = $xmlparser->parse($this->xmlpath);
        if ($xmlroot->HasFatalErr()) {
            $this->conferr = $xmlroot->get(Node::FLD_ERR);

            return false;
        }

        $root = $xmlroot->DupHolder();
        $filemap = DPageDef::GetInstance()->getFileMap(Info::CT_SERV);   // serv, vh, tp, admin
        $filemap->Convert(0, $xmlroot, 1, $root);

        // migrate all vh.xml
        if (($vhosts = $root->getChildren('virtualhost')) != null) {
            if (!is_array($vhosts)) {
                $vhosts = [$vhosts];
            }
            foreach ($vhosts as $vh) {
                $vhname = $vh->get(Node::FLD_VAL);
                $vhroot = $vh->getChildVal('vhRoot');
                $vhconf = $vh->getChildVal('configFile');
                $conffile = PathTool::GetAbsFile($vhconf, 'VR', $vhname, $vhroot);
                $vhdata = new CData(Info::CT_VH, $conffile);
                if (($pos = strpos($vhconf, '.xml')) > 0) {
                    $vhconf = substr($vhconf, 0, $pos) . '.conf';
                    $vh->SetChildVal('configFile', $vhconf);
                }
            }
        }

        // migrate all tp.xml
        if (($tps = $root->getChildren('vhTemplate')) != null) {
            if (!is_array($tps)) {
                $tps = [$tps];
            }
            foreach ($tps as $tp) {
                $tpconf = $tp->getChildVal('templateFile');
                $conffile = PathTool::GetAbsFile($tpconf, 'SR');
                $tpdata = new CData(Info::CT_TP, $conffile);
                if (($pos = strpos($tpconf, '.xml')) > 0) {
                    $tpconf = substr($tpconf, 0, $pos) . '.conf';
                    $tp->SetChildVal('templateFile', $tpconf);
                }
            }
        }

        $buf = '';
        $this->before_write_conf($root);
        $root->PrintBuf($buf);
        touch($this->path);

        $this->write_file($this->path, $buf);
        $this->copy_permission($this->xmlpath, $this->path);

        $migrated = $this->xmlpath . '.migrated.' . time();

        if (defined('SAVE_XML')) {
            copy($this->xmlpath, $migrated);
        } else {
            rename($this->xmlpath, $migrated);
        }
        if (defined('RECOVER_SCRIPT')) {
            file_put_contents(RECOVER_SCRIPT, "mv ${migrated} {$this->xmlpath}\n", FILE_APPEND);
        }

        error_log("  converted {$this->xmlpath} to {$this->path}\n\n");
    }

    private function migrate_allconf2xml() {
        if (($vhosts = $this->root->getChildren('virtualhost')) != null) {
            if (!is_array($vhosts)) {
                $vhosts = [$vhosts];
            }
            $filemap = DPageDef::GetInstance()->getFileMap(Info::CT_VH);
            foreach ($vhosts as $vh) {
                $vhname = $vh->get(Node::FLD_VAL);
                $vhroot = $vh->getChildVal('vhRoot');
                $vhconf = $vh->getChildVal('configFile');
                $conffile = PathTool::GetAbsFile($vhconf, 'VR', $vhname, $vhroot);
                $vhdata = new CData(Info::CT_VH, $conffile);
                $this->save_xml_file($vhdata->root, $filemap, $vhdata->xmlpath);
                $this->copy_permission($vhdata->path, $vhdata->xmlpath);
                error_log("  converted {$vhdata->path} to {$vhdata->xmlpath}\n");
            }
        }

        if (($tps = $this->root->getChildren('vhTemplate')) != null) {
            if (!is_array($tps)) {
                $tps = [$tps];
            }
            $filemap = PageDef::getInstance()->getFileMap(Info::CT_TP);
            foreach ($tps as $tp) {
                $tpconf = $tp->getChildVal('templateFile');
                $conffile = PathTool::GetAbsFile($tpconf, 'SR');
                $tpdata = new CData(Info::CT_TP, $conffile);
                $this->save_xml_file($tpdata->root, $filemap, $tpdata->xmlpath);
                $this->copy_permission($tpdata->path, $tpdata->xmlpath);
                error_log("  converted {$tpdata->path} to {$tpdata->xmlpath}\n");
            }
        }

        $filemap = PageDef::getInstance()->getFileMap(Info::CT_SERV);
        $this->save_xml_file($this->root, $filemap, $this->xmlpath);
        $this->copy_permission($this->path, $this->xmlpath);
        error_log("  converted {$this->path} to {$this->xmlpath}\n");
    }

    private function writeFile($filepath, $buf) {
        if (!file_exists($filepath)) {
            // new file, check path exists
            if (!PathTool::createFile("{$filepath}.new", $err)) {
                error_log("failed to create file ${filepath} : ${err} \n");

                return false;
            }
        }

        $fd = fopen("{$filepath}.new", 'w');
        if (!$fd) {
            error_log("failed to open in write mode for {$filepath}.new");

            return false;
        }

        if (fwrite($fd, $buf) === false) {
            error_log("failed to write temp config for {$filepath}.new");

            return false;
        }
        fclose($fd);

        @unlink("{$filepath}.bak");
        if (file_exists($filepath) && !rename($filepath, "{$filepath}.bak")) {
            error_log("failed to rename {$filepath} to {$filepath}.bak");

            return false;
        }

        if (!rename("{$filepath}.new", $filepath)) {
            error_log("failed to rename {$filepath}.new to {$filepath}");

            return false;
        }

        return true;
    }
}

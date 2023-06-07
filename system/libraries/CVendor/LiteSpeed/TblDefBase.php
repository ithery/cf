<?php
use CVendor_LiteSpeed_Msg as Msg;
use CVendor_LiteSpeed_Tbl as Tbl;
use CVendor_LiteSpeed_Node as Node;
use CVendor_LiteSpeed_OWS_Attr as Attr;

class CVendor_LiteSpeed_TblDefBase {
    protected $tblDef = [];

    protected $options = [];

    protected $_attrs;

    protected $_specials = [];

    /**
     * @param mixed $tblId
     *
     * @return CVendor_LiteSpeed_Tbl
     */
    public function getTblDef($tblId) {
        if (!isset($this->tblDef[$tblId])) {
            $funcname = 'add_' . $tblId;
            if (!method_exists($this, $funcname)) {
                die("invalid tid ${tblId}\n");
            }
            $this->$funcname($tblId);
        }

        return $this->tblDef[$tblId];
    }

    protected function loadSpecials() {
        // define special block contains raw data
        $this->addSpecial('rewrite', ['enable', 'logLevel', 'map', 'inherit', 'base'], 'rules');
        $this->addSpecial('virtualHostConfig:rewrite', ['enable', 'logLevel', 'map', 'inherit', 'base'], 'rules'); // for template
        $this->addSpecial('botWhiteList', [], 'list');
    }

    protected function addSpecial($key, $attrList, $catchAllTag) {
        $key = strtolower($key);
        $this->_specials[$key] = []; // allow later ones override previous one
        foreach ($attrList as $attr) {
            $this->_specials[$key][] = strtolower($attr);
        }
        $this->_specials[$key]['*'] = $catchAllTag;
    }

    public function markSpecialBlock(Node $node) {
        $key = strtolower($node->get(Node::FLD_KEY));
        if (isset($this->_specials[$key])) {
            $tag = $this->_specials[$key]['*']; // cache all key
            $node->AddRawTag($tag);

            return true;
        }

        return false;
    }

    public function isSpecialBlockRawContent($node, $testKey) {
        $key = strtolower($node->get(Node::FLD_KEY));
        if (isset($this->_specials[$key])) {
            if (!in_array(strtolower($testKey), $this->_specials[$key])) {
                return true;
            }
        }

        return false;
    }

    protected function dupTblDef($tblId, $newId, $newTitle = null) {
        $tbl = $this->getTblDef($tblId);
        $newtbl = $tbl->Dup($newId, $newTitle);

        return $newtbl;
    }

    protected static function newIntAttr($key, $label, $allowNull = true, $min = null, $max = null, $helpKey = null) {
        return new Attr($key, 'uint', $label, 'text', $allowNull, $min, $max, null, 0, $helpKey);
    }

    protected static function newBoolAttr($key, $label, $allowNull = true, $helpKey = null) {
        return new Attr($key, 'bool', $label, 'radio', $allowNull, null, null, null, 0, $helpKey);
    }

    protected static function NewSelAttr($key, $label, $options, $allowNull = true, $helpKey = null, $inputAttr = null, $multiInd = 0) {
        if (is_array($options)) { // fixed options
            return new Attr($key, 'sel', $label, 'select', $allowNull, null, $options, $inputAttr, 0, $helpKey);
        }

        // derived options
        if ($multiInd == 0) {
            return new Attr($key, 'sel1', $label, 'select', $allowNull, $options, null, $inputAttr, 0, $helpKey);
        } else { //sel2 is derived and multi and using text
            return new Attr($key, 'sel2', $label, 'text', $allowNull, $options, null, $inputAttr, 1, $helpKey);
        }
    }

    protected static function newCheckBoxAttr($key, $label, $options, $allowNull = true, $helpKey = null, $default = null) {
        return new Attr($key, 'checkboxOr', $label, 'checkboxgroup', $allowNull, $default, $options, null, 0, $helpKey);
    }

    protected static function newTextAttr($key, $label, $type, $allowNull = true, $helpKey = null, $multiInd = 0, $inputAttr = null) {
        return new Attr($key, $type, $label, 'text', $allowNull, null, null, $inputAttr, $multiInd, $helpKey);
    }

    protected static function newParseTextAttr($key, $label, $parseformat, $parsehelp, $allowNull = true, $helpKey = null, $multiInd = 0) {
        return new Attr($key, 'parse', $label, 'text', $allowNull, $parseformat, $parsehelp, null, $multiInd, $helpKey);
    }

    protected static function NewParseTextAreaAttr($key, $label, $parseformat, $parsehelp, $allowNull = true, $row, $helpKey = null, $viewtextarea = 1, $wrapoff = 0, $multiInd = 0) {
        $inputAttr = 'rows="' . $row . '"';
        if ($wrapoff == 1) {
            $inputAttr .= ' wrap=off';
        }

        $type = ($viewtextarea == 1) ? 'textarea1' : 'textarea';

        return new Attr($key, 'parse', $label, $type, $allowNull, $parseformat, $parsehelp, $inputAttr, $multiInd, $helpKey);
    }

    protected static function NewTextAreaAttr($key, $label, $type, $allowNull = true, $row, $helpKey = null, $viewtextarea = 1, $wrapoff = 0, $multiInd = 0) {
        $inputAttr = 'rows="' . $row . '"';
        if ($wrapoff == 1) {
            $inputAttr .= ' wrap="off"';
        }

        $inputtype = ($viewtextarea == 1) ? 'textarea1' : 'textarea';

        return new Attr($key, $type, $label, $inputtype, $allowNull, null, null, $inputAttr, $multiInd, $helpKey);
    }

    protected static function newPathAttr($key, $label, $type, $reflevel, $rwc = '', $allowNull = true, $helpKey = null, $multiInd = 0) {
        return new Attr($key, $type, $label, 'text', $allowNull, $reflevel, $rwc, null, $multiInd, $helpKey);
    }

    protected static function newCustFlagAttr($key, $label, $flag = 0, $allowNull = true, $type = 'cust', $inputtype = 'text', $helpKey = null, $multiInd = 0) {
        $attr = new Attr($key, $type, $label, $inputtype, $allowNull, null, null, null, $multiInd, $helpKey);
        if ($flag != 0) {
            $attr->SetFlag($flag);
        }

        return $attr;
    }

    protected static function NewPassAttr($key, $label, $allowNull = true, $helpKey = null) {
        return new Attr($key, 'cust', $label, 'password', $allowNull, null, null, null, 0, $helpKey);
    }

    protected static function newViewAttr($key, $label, $helpKey = null) { // for view only
        return new Attr($key, 'cust', $label, null, null, null, null, null, 0, $helpKey);
    }

    protected static function NewActionAttr($linkTbl, $act, $allowNull = true) {
        return new Attr('action', 'action', Msg::aLbl('l_action'), null, $allowNull, $linkTbl, $act);
    }

    protected function loadCommonOptions() {
        $this->options['tp_vname'] = ['/\$VH_NAME/', Msg::aLbl('parse_tpname')];

        $this->options['symbolLink'] = ['1' => Msg::aLbl('o_yes'), '2' => Msg::aLbl('o_ifownermatch'), '0' => Msg::aLbl('o_no')];

        $this->options['extType'] = [
            'fcgi' => Msg::aLbl('l_fcgiapp'), 'fcgiauth' => Msg::aLbl('l_extfcgiauth'),
            'lsapi' => Msg::aLbl('l_extlsapi'),
            'servlet' => Msg::aLbl('l_extservlet'), 'proxy' => Msg::aLbl('l_extproxy'),
            'logger' => Msg::aLbl('l_extlogger'),
            'loadbalancer' => Msg::aLbl('l_extlb')];

        $this->options['extTbl'] = [
            0 => 'type', 1 => 'A_EXT_FCGI',
            'fcgi' => 'A_EXT_FCGI', 'fcgiauth' => 'A_EXT_FCGIAUTH',
            'lsapi' => 'A_EXT_LSAPI',
            'servlet' => 'A_EXT_SERVLET', 'proxy' => 'A_EXT_PROXY',
            'logger' => 'A_EXT_LOGGER',
            'loadbalancer' => 'A_EXT_LOADBALANCER'];

        $this->options['tp_extTbl'] = [
            0 => 'type', 1 => 'T_EXT_FCGI',
            'fcgi' => 'T_EXT_FCGI', 'fcgiauth' => 'T_EXT_FCGIAUTH',
            'lsapi' => 'T_EXT_LSAPI',
            'servlet' => 'T_EXT_SERVLET', 'proxy' => 'T_EXT_PROXY',
            'logger' => 'T_EXT_LOGGER',
            'loadbalancer' => 'T_EXT_LOADBALANCER'];

        $this->options['logLevel'] = ['ERROR' => 'ERROR', 'WARN' => 'WARNING',
            'NOTICE' => 'NOTICE', 'INFO' => 'INFO', 'DEBUG' => 'DEBUG'];

        $this->options['aclogctrl'] = [
            0 => Msg::aLbl('o_ownlogfile'),
            1 => Msg::aLbl('o_serverslogfile'),
            2 => Msg::aLbl('o_disabled')];

        $this->options['lsrecaptcha'] = [
            '0' => Msg::aLbl('o_notset'),
            '1' => Msg::aLbl('o_checkbox'),
            '2' => Msg::aLbl('o_invisible')];

        // for shared parse format
        $this->options['parseFormat'] = [
            'filePermission4' => '/^0?[0-7]{3,4}$/',
            'filePermission3' => '/^0?[0-7]{3}$/'
        ];

        $ipv6str = isset($_SERVER['LSWS_IPV6_ADDRS']) ? $_SERVER['LSWS_IPV6_ADDRS'] : '';
        $ipv6 = [];
        if ($ipv6str != '') {
            $ipv6['[ANY]'] = '[ANY] IPv6';
            $ips = explode(',', $ipv6str);
            foreach ($ips as $ip) {
                if ($pos = strpos($ip, ':')) {
                    $aip = substr($ip, $pos + 1);
                    $ipv6[$aip] = $aip;
                }
            }
        }
        $ipo = [];
        $ipo['ANY'] = 'ANY IPv4';
        $ipstr = isset($_SERVER['LSWS_IPV4_ADDRS']) ? $_SERVER['LSWS_IPV4_ADDRS'] : '';
        if ($ipstr != '') {
            $ips = explode(',', $ipstr);
            foreach ($ips as $ip) {
                if ($pos = strpos($ip, ':')) {
                    $aip = substr($ip, $pos + 1);
                    $ipo[$aip] = $aip;
                    if ($aip != '127.0.0.1') {
                        $ipv6["[::FFFF:${aip}]"] = "[::FFFF:${aip}]";
                    }
                }
            }
        }
        if ($ipv6str != '') {
            $this->options['ip'] = $ipo + $ipv6;
        } else {
            $this->options['ip'] = $ipo;
        }
    }

    protected function loadCommonAttrs() {
        $ctxOrder = self::newViewAttr('order', Msg::aLbl('l_order'));
        $ctxOrder->SetFlag(Attr::BM_NOFILE | Attr::BM_HIDE | Attr::BM_NOEDIT);

        $attrs = [
            'priority' => self::newIntAttr('priority', Msg::aLbl('l_priority'), true, -20, 20),
            'indexFiles' => self::NewTextAreaAttr('indexFiles', Msg::aLbl('l_indexfiles'), 'fname', true, 2, null, 0, 0, 1),
            'autoIndex' => self::newBoolAttr('autoIndex', Msg::aLbl('l_autoindex')),
            'adminEmails' => self::NewTextAreaAttr('adminEmails', Msg::aLbl('l_adminemails'), 'email', true, 3, null, 0, 0, 1),
            'suffix' => self::newParseTextAttr('suffix', Msg::aLbl('l_suffix'), "/^[A-z0-9_\-]+$/", Msg::aLbl('parse_suffix'), false, null, 1),
            'fileName2' => self::newPathAttr('fileName', Msg::aLbl('l_filename'), 'file0', 2, 'r', false),
            'fileName3' => self::newPathAttr('fileName', Msg::aLbl('l_filename'), 'file0', 3, 'r', true),
            'rollingSize' => self::newIntAttr('rollingSize', Msg::aLbl('l_rollingsize'), true, null, null, 'log_rollingSize'),
            'keepDays' => self::newIntAttr('keepDays', Msg::aLbl('l_keepdays'), true, 0, null, 'log_keepDays'),
            'logFormat' => self::newTextAttr('logFormat', Msg::aLbl('l_logformat'), 'cust', true, 'accessLog_logFormat'),
            'logHeaders' => self::newCheckBoxAttr('logHeaders', Msg::aLbl('l_logheaders'), ['1' => 'Referrer', '2' => 'UserAgent', '4' => 'Host', '0' => Msg::aLbl('o_none')], true, 'accessLog_logHeader'),
            'compressArchive' => self::newBoolAttr('compressArchive', Msg::aLbl('l_compressarchive'), true, 'accessLog_compressArchive'),
            'extraHeaders' => self::NewTextAreaAttr('extraHeaders', Msg::aLbl('l_extraHeaders'), 'cust', true, 5, null, 1, 1),
            'scriptHandler_type' => self::NewSelAttr('type', Msg::aLbl('l_handlertype'), $this->options['scriptHandler'], false, 'shType', 'onChange="lst_conf(\'c\')"'),
            'scriptHandler' => self::NewSelAttr('handler', Msg::aLbl('l_handlername'), 'extprocessor:$$type', false, 'shHandlerName'),
            'ext_type' => self::NewSelAttr('type', Msg::aLbl('l_type'), $this->options['extType'], false, 'extAppType'),
            'name' => self::newTextAttr('name', Msg::aLbl('l_name'), 'name', false),
            'ext_name' => self::newTextAttr('name', Msg::aLbl('l_name'), 'name', false, 'extAppName'),
            'ext_address' => self::newTextAttr('address', Msg::aLbl('l_address'), 'addr', false, 'extAppAddress'),
            'ext_maxConns' => self::newIntAttr('maxConns', Msg::aLbl('l_maxconns'), false, 1, 2000),
            'pcKeepAliveTimeout' => self::newIntAttr('pcKeepAliveTimeout', Msg::aLbl('l_pckeepalivetimeout'), true, -1, 10000),
            'ext_env' => self::NewParseTextAreaAttr('env', Msg::aLbl('l_env'), "/\S+=\S+/", Msg::aLbl('parse_env'), true, 5, null, 0, 1, 2),
            'ext_initTimeout' => self::newIntAttr('initTimeout', Msg::aLbl('l_inittimeout'), false, 1),
            'ext_retryTimeout' => self::newIntAttr('retryTimeout', Msg::aLbl('l_retrytimeout'), false, 0),
            'ext_respBuffer' => self::NewSelAttr('respBuffer', Msg::aLbl('l_respbuffer'), ['0' => Msg::aLbl('o_no'), '1' => Msg::aLbl('o_yes'), '2' => Msg::aLbl('o_nofornph')], false),
            'ext_persistConn' => self::newBoolAttr('persistConn', Msg::aLbl('l_persistconn')),
            'ext_autoStart' => self::NewSelAttr('autoStart', Msg::aLbl('l_autostart'), ['2' => Msg::aLbl('o_thrucgidaemon'), '0' => Msg::aLbl('o_no')], false),
            'ext_path' => self::newPathAttr('path', Msg::aLbl('l_command'), 'file1', 3, 'x', true, 'extAppPath'),
            'ext_backlog' => self::newIntAttr('backlog', Msg::aLbl('l_backlog'), true, 1, 100),
            'ext_instances' => self::newIntAttr('instances', Msg::aLbl('l_instances'), true, 0, 1000),
            'ext_runOnStartUp' => self::NewSelAttr('runOnStartUp', Msg::aLbl('l_runonstartup'), ['' => '', '1' => Msg::aLbl('o_yes'), '3' => Msg::aLbl('o_yesdetachmode'), '2' => Msg::aLbl('o_yesdaemonmode'), '0' => Msg::aLbl('o_no'), ]),
            'ext_user' => self::newTextAttr('extUser', Msg::aLbl('l_suexecuser'), 'cust'),
            'ext_group' => self::newTextAttr('extGroup', Msg::aLbl('l_suexecgrp'), 'cust'),
            'cgiUmask' => self::newParseTextAttr('umask', Msg::aLbl('l_umask'), $this->options['parseFormat']['filePermission3'], Msg::aLbl('parse_umask')),
            'memSoftLimit' => self::newIntAttr('memSoftLimit', Msg::aLbl('l_memsoftlimit'), true, 0),
            'memHardLimit' => self::newIntAttr('memHardLimit', Msg::aLbl('l_memhardlimit'), true, 0),
            'procSoftLimit' => self::newIntAttr('procSoftLimit', Msg::aLbl('l_procsoftlimit'), true, 0),
            'procHardLimit' => self::newIntAttr('procHardLimit', Msg::aLbl('l_prochardlimit'), true, 0),
            'ssl_renegProtection' => self::newBoolAttr('renegProtection', Msg::aLbl('l_renegprotection')),
            'sslSessionCache' => self::newBoolAttr('sslSessionCache', Msg::aLbl('l_sslSessionCache')),
            'sslSessionTickets' => self::newBoolAttr('sslSessionTickets', Msg::aLbl('l_sslSessionTickets')),
            'l_vhost' => self::NewSelAttr('vhost', Msg::aLbl('l_vhost'), 'virtualhost', false, 'virtualHostName'),
            'l_domain' => self::newTextAttr('domain', Msg::aLbl('l_domains'), 'domain', false, 'domainName', 1),
            'tp_templateFile' => self::newPathAttr('templateFile', Msg::aLbl('l_templatefile'), 'filetp', 2, 'rwc', false),
            'tp_listeners' => self::NewSelAttr('listeners', Msg::aLbl('l_mappedlisteners'), 'listener', false, 'mappedListeners', null, 1),
            'tp_vhName' => self::newTextAttr('vhName', Msg::aLbl('l_vhname'), 'vhname', false, 'templateVHName'),
            'tp_vhDomain' => self::newTextAttr('vhDomain', Msg::aLbl('l_domain'), 'domain', true, 'templateVHDomain'),
            'tp_vhAliases' => self::newTextAttr('vhAliases', Msg::aLbl('l_vhaliases'), 'domain', true, 'templateVHAliases', 1),
            'tp_vhRoot' => self::newParseTextAttr('vhRoot', Msg::aLbl('l_defaultvhroot'), $this->options['tp_vname'][0], $this->options['tp_vname'][1], false, 'templateVHRoot'),
            'tp_vrFile' => self::newParseTextAttr('fileName', Msg::aLbl('l_filename'), '/(\$VH_NAME)|(\$VH_ROOT)/', Msg::aLbl('parse_tpfile'), false, 'templateFileRef'),
            'tp_name' => self::newParseTextAttr('name', Msg::aLbl('l_name'), $this->options['tp_vname'][0], $this->options['tp_vname'][1], false, 'tpextAppName'),
            'vh_maxKeepAliveReq' => self::newIntAttr('maxKeepAliveReq', Msg::aLbl('l_maxkeepalivereq'), true, 0, 32767, 'vhMaxKeepAliveReq'),
            'vh_enableGzip' => self::newBoolAttr('enableGzip', Msg::aLbl('l_enablecompress'), true, 'vhEnableGzip'),
            'vh_allowSymbolLink' => self::NewSelAttr('allowSymbolLink', Msg::aLbl('l_allowsymbollink'), $this->options['symbolLink']),
            'vh_enableScript' => self::newBoolAttr('enableScript', Msg::aLbl('l_enablescript'), false),
            'vh_restrained' => self::newBoolAttr('restrained', Msg::aLbl('l_restrained'), false),
            'vh_setUIDMode' => self::NewSelAttr('setUIDMode', Msg::aLbl('l_setuidmode'), ['' => '', 0 => 'Server UID', 1 => 'CGI File UID', 2 => 'DocRoot UID'], true, 'setUidMode'),
            'vh_suexec_user' => self::newTextAttr('user', Msg::aLbl('l_suexecuser1'), 'cust', true, 'suexecUser'),
            'vh_suexec_group' => self::newTextAttr('group', Msg::aLbl('l_suexecgrp1'), 'cust', true, 'suexecGroup'),
            'staticReqPerSec' => self::newIntAttr('staticReqPerSec', Msg::aLbl('l_staticreqpersec'), true, 0),
            'dynReqPerSec' => self::newIntAttr('dynReqPerSec', Msg::aLbl('l_dynreqpersec'), true, 0),
            'outBandwidth' => self::newIntAttr('outBandwidth', Msg::aLbl('l_outbandwidth'), true, 0),
            'inBandwidth' => self::newIntAttr('inBandwidth', Msg::aLbl('l_inbandwidth'), true, 0),
            'ctx_order' => $ctxOrder,
            'ctx_type' => self::NewSelAttr('type', Msg::aLbl('l_type'), $this->options['ctxType'], false, 'ctxType'),
            'ctx_uri' => self::newTextAttr('uri', Msg::aLbl('l_uri'), 'expuri', false, 'expuri'),
            'ctx_location' => self::newTextAttr('location', Msg::aLbl('l_location'), 'cust'),
            'ctx_shandler' => self::NewSelAttr('handler', Msg::aLbl('l_servletengine'), 'extprocessor:servlet', false, 'servletEngine'),
            'appserverEnv' => self::NewSelAttr('appserverEnv', Msg::aLbl('l_runtimemode'), ['' => '', '0' => 'Development', '1' => 'Production', '2' => 'Staging']),
            'geoipDBFile' => self::newPathAttr('geoipDBFile', Msg::aLbl('l_geoipdbfile'), 'filep', 2, 'r', false),
            'enableIpGeo' => self::newBoolAttr('enableIpGeo', Msg::aLbl('l_enableipgeo')),
            'note' => self::NewTextAreaAttr('note', Msg::aLbl('l_notes'), 'cust', true, 4, null, 0),
        ];
        $this->_attrs = $attrs;
    }

    //	Attr($key, $type, $label,  $inputType, $allowNull,$min, $max, $inputAttr, $multiInd)
    protected function get_expires_attrs() {
        return [
            self::newBoolAttr('enableExpires', Msg::aLbl('l_enableexpires')),
            self::newParseTextAttr('expiresDefault', Msg::aLbl('l_expiresdefault'), "/^[AaMm]\d+$/", Msg::aLbl('parse_expiresdefault')),
            self::NewParseTextAreaAttr('expiresByType', Msg::aLbl('l_expiresByType'), "/^(\*\/\*)|([A-z0-9_\-\.\+]+\/\*)|([A-z0-9_\-\.\+]+\/[A-z0-9_\-\.\+]+)=[AaMm]\d+$/", Msg::aLbl('parse_expiresByType'), true, 2, null, 0, 0, 1)
        ];
    }

    protected function add_S_INDEX($id) {
        $attrs = [
            $this->_attrs['indexFiles'],
            $this->_attrs['autoIndex'],
            self::newTextAttr('autoIndexURI', Msg::aLbl('l_autoindexuri'), 'uri')
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_indexfiles'), $attrs);
    }

    protected function add_S_LOG($id) {
        $attrs = [
            $this->_attrs['fileName2']->dup(null, null, 'log_fileName'),
            self::NewSelAttr('logLevel', Msg::aLbl('l_loglevel'), $this->options['logLevel'], false, 'log_logLevel'),
            self::NewSelAttr('debugLevel', Msg::aLbl('l_debuglevel'), ['10' => Msg::aLbl('o_high'), '5' => Msg::aLbl('o_medium'), '2' => Msg::aLbl('o_low'), '0' => Msg::aLbl('o_none')], false, 'log_debugLevel'),
            $this->_attrs['rollingSize'],
            $this->_attrs['keepDays'],
            self::newBoolAttr('enableStderrLog', Msg::aLbl('l_enablestderrlog'), true, 'log_enableStderrLog')
        ];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_serverlog'), $attrs, 'fileName');
    }

    protected function add_S_ACLOG_TOP($id) {
        $attrs = [
            $this->_attrs['fileName2']->dup(null, null, 'accessLog_fileName'),
            $this->_attrs['logFormat'],
            $this->_attrs['rollingSize'],
            self::NewActionAttr('S_ACLOG', 'Ed'),
        ];
        $this->tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_accesslog'), $attrs, 'fileName', 'S_ACLOG');
    }

    protected function add_S_ACLOG($id) {
        $attrs = [
            $this->_attrs['fileName2']->dup(null, null, 'accessLog_fileName'),
            self::NewSelAttr('pipedLogger', Msg::aLbl('l_pipedlogger'), 'extprocessor:logger', true, 'accessLog_pipedLogger'),
            $this->_attrs['logFormat'],
            $this->_attrs['logHeaders'],
            $this->_attrs['rollingSize'],
            $this->_attrs['keepDays'],
            $this->_attrs['compressArchive']
        ];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_accesslog'), $attrs, 'fileName');
    }

    protected function add_A_EXPIRES($id) {
        $attrs = $this->get_expires_attrs();
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_expires'), $attrs);
    }

    protected function add_S_GEOIP_TOP($id) {
        $align = ['center', 'center', 'center'];

        $attrs = [
            $this->_attrs['geoipDBFile'],
            self::newViewAttr('geoipDBName', Msg::aLbl('l_dbname')),
            self::NewActionAttr('S_GEOIP', 'Ed')
        ];
        $this->tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_geoipdb'), $attrs, 'geoipDBFile', 'S_GEOIP', $align, 'geolocationDB', 'database', true);
    }

    protected function add_S_GEOIP($id) {
        $attrs = [
            $this->_attrs['geoipDBFile'],
            self::newTextAttr('geoipDBName', Msg::aLbl('l_dbname'), 'dbname', false),
            self::NewParseTextAreaAttr('maxMindDBEnv', Msg::aLbl('l_envvariable'), "/^\S+[ \t]+\S+$/", Msg::aLbl('parse_geodbenv'), true, 5, null, 0, 1, 2),
            $this->_attrs['note'],
        ];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_geoipdb'), $attrs, 'geoipDBFile', 'geolocationDB');
    }

    protected function add_S_IP2LOCATION($id) {
        $attrs = [
            self::newPathAttr('ip2locDBFile', Msg::aLbl('l_ip2locDBFile'), 'filep', 2, 'r'),
            self::NewSelAttr('ip2locDBCache', Msg::aLbl('l_ip2locDBCache'), ['' => '',
                'FileIo' => 'File System',
                'MemoryCache' => 'Memory',
                'SharedMemoryCache' => 'Shared Memory']),
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_ip2locDB'), $attrs);
    }

    protected function add_S_TUNING_CONN($id) {
        $attrs = [
            self::newIntAttr('maxConnections', Msg::aLbl('l_maxconns'), false, 1),
            self::newIntAttr('maxSSLConnections', Msg::aLbl('l_maxsslconns'), false, 0),
            self::newIntAttr('connTimeout', Msg::aLbl('l_conntimeout'), false, 10, 1000000),
            self::newIntAttr('maxKeepAliveReq', Msg::aLbl('l_maxkeepalivereq'), false, 0, 32767),
            self::newIntAttr('keepAliveTimeout', Msg::aLbl('l_keepalivetimeout'), false, 0, 60),
            self::newIntAttr('sndBufSize', Msg::aLbl('l_sndbufsize'), true, 0, '512K'),
            self::newIntAttr('rcvBufSize', Msg::aLbl('l_rcvbufsize'), true, 0, '512K'),
        ];

        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_connection'), $attrs);
    }

    protected function add_S_TUNING_REQ($id) {
        $attrs = [
            self::newIntAttr('maxReqURLLen', Msg::aLbl('l_maxrequrllen'), false, 200, 65530),
            self::newIntAttr('maxReqHeaderSize', Msg::aLbl('l_maxreqheadersize'), false, 1024, 65530),
            self::newIntAttr('maxReqBodySize', Msg::aLbl('l_maxreqbodysize'), false, '1M', null),
            self::newIntAttr('maxDynRespHeaderSize', Msg::aLbl('l_maxdynrespheadersize'), false, 200, '64K'),
            self::newIntAttr('maxDynRespSize', Msg::aLbl('l_maxdynrespsize'), false, '1M', null)
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_reqresp'), $attrs);
    }

    protected function add_S_TUNING_GZIP($id) {
        $parseFormat = "/^(\!)?(\*\/\*)|([A-z0-9_\-\.\+]+\/\*)|([A-z0-9_\-\.\+]+\/[A-z0-9_\-\.\+]+)|default$/";

        $attrs = [
            // general
            self::newBoolAttr('enableGzipCompress', Msg::aLbl('l_enablecompress'), false),
            self::NewParseTextAreaAttr('compressibleTypes', Msg::aLbl('l_compressibletypes'), $parseFormat, Msg::aLbl('parse_compressibletypes'), true, 5, null, 0, 0, 1),
            // dyn
            self::newBoolAttr('enableDynGzipCompress', Msg::aLbl('l_enabledyngzipcompress'), false),
            self::newIntAttr('gzipCompressLevel', Msg::aLbl('l_gzipcompresslevel'), true, 1, 9),
            // self::newIntAttr('enableBrCompress', Msg::aLbl('l_brcompresslevel'), true, 0, 6),
            // static
            self::newBoolAttr('gzipAutoUpdateStatic', Msg::aLbl('l_gzipautoupdatestatic')),
            self::newIntAttr('gzipStaticCompressLevel', Msg::aLbl('l_gzipstaticcompresslevel'), true, 1, 9),
            self::newIntAttr('brStaticCompressLevel', Msg::aLbl('l_brstaticcompresslevel'), true, 1, 11),
            self::newTextAttr('gzipCacheDir', Msg::aLbl('l_gzipcachedir'), 'cust'),
            self::newIntAttr('gzipMaxFileSize', Msg::aLbl('l_gzipmaxfilesize'), true, '1K'),
            self::newIntAttr('gzipMinFileSize', Msg::aLbl('l_gzipminfilesize'), true, 200)
        ];

        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_gzipbr'), $attrs);
    }

    protected function add_S_TUNING_QUIC($id) {
        $congest_options = ['' => 'Default', '1' => 'Cubic', '2' => 'BBR'];
        $attrs = [
            self::newBoolAttr('quicEnable', Msg::aLbl('l_enablequic')),
            self::newTextAttr('quicShmDir', Msg::aLbl('l_quicshmdir'), 'cust'),
            self::newTextAttr('quicVersions', Msg::aLbl('l_quicversions'), 'cust'),
            self::NewSelAttr('quicCongestionCtrl', Msg::aLbl('l_congestionctrl'), $congest_options),
            self::newIntAttr('quicCfcw', Msg::aLbl('l_quiccfcw'), true, '64K', '512M'),
            self::newIntAttr('quicMaxCfcw', Msg::aLbl('l_quicmaxcfcw'), true, '64K', '512M'),
            self::newIntAttr('quicSfcw', Msg::aLbl('l_quicsfcw'), true, '64K', '128M'),
            self::newIntAttr('quicMaxSfcw', Msg::aLbl('l_quicmaxsfcw'), true, '64K', '128M'),
            self::newIntAttr('quicMaxStreams', Msg::aLbl('l_quicmaxstreams'), true, 10, 1000),
            self::newIntAttr('quicHandshakeTimeout', Msg::aLbl('l_quichandshaketimeout'), true, 1, 15),
            self::newIntAttr('quicIdleTimeout', Msg::aLbl('l_quicidletimeout'), true, 10, 30),
            self::newBoolAttr('quicEnableDPLPMTUD', Msg::aLbl('l_quicenabledplpmtud')),
            self::newIntAttr('quicBasePLPMTU', Msg::aLbl('l_quicbaseplpmtu'), true, 0, 65527),
            self::newIntAttr('quicMaxPLPMTU', Msg::aLbl('l_quicmaxplpmtu'), true, 0, 65527),
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_quic'), $attrs);
    }

    protected function add_S_SEC_FILE($id) {
        $parseFormat = $this->options['parseFormat']['filePermission4'];
        $parseHelp = Msg::aLbl('parse_secpermissionmask');

        $flag = (Attr::BM_HIDE | Attr::BM_NOEDIT);
        $a_requiredPermissionMask = self::newParseTextAttr('requiredPermissionMask', Msg::aLbl('l_requiredpermissionmask'), $parseFormat, $parseHelp);
        $a_requiredPermissionMask->SetFlag($flag);
        $a_restrictedPermissionMask = self::newParseTextAttr('restrictedPermissionMask', Msg::aLbl('l_restrictedpermissionmask'), $parseFormat, $parseHelp);
        $a_restrictedPermissionMask->SetFlag($flag);
        $a_restrictedScriptPermissionMask = self::newParseTextAttr('restrictedScriptPermissionMask', Msg::aLbl('l_restrictedscriptpermissionmask'), $parseFormat, $parseHelp);
        $a_restrictedScriptPermissionMask->SetFlag($flag);
        $a_restrictedDirPermissionMask = self::newParseTextAttr('restrictedDirPermissionMask', Msg::aLbl('l_restricteddirpermissionmask'), $parseFormat, $parseHelp);
        $a_restrictedDirPermissionMask->SetFlag($flag);

        $attrs = [
            self::NewSelAttr('followSymbolLink', Msg::aLbl('l_followsymbollink'), $this->options['symbolLink'], false),
            self::newBoolAttr('checkSymbolLink', Msg::aLbl('l_checksymbollink'), false),
            self::newBoolAttr('forceStrictOwnership', Msg::aLbl('l_forcestrictownership'), false),
            $a_requiredPermissionMask,
            $a_restrictedPermissionMask,
            $a_restrictedScriptPermissionMask,
            $a_restrictedDirPermissionMask,
        ];

        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_fileaccess'), $attrs);
    }

    protected function add_S_SEC_CONN($id) {
        $attrs = [
            $this->_attrs['staticReqPerSec'],
            $this->_attrs['dynReqPerSec'],
            $this->_attrs['outBandwidth'],
            $this->_attrs['inBandwidth'],
            self::newIntAttr('softLimit', Msg::aLbl('l_softlimit'), true, 0),
            self::newIntAttr('hardLimit', Msg::aLbl('l_hardlimit'), true, 0),
            self::newBoolAttr('blockBadReq', Msg::aLbl('l_blockbadreq')),
            self::newIntAttr('gracePeriod', Msg::aLbl('l_graceperiod'), true, 1, 3600),
            self::newIntAttr('banPeriod', Msg::aLbl('l_banperiod'), true, 0)
        ];

        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_perclientthrottle'), $attrs, 'perClientConnLimit');
    }

    protected function add_S_SEC_RECAP($id) {
        $parseFormat = '/^[[:alnum:]-_]{20,100}$/';
        $parseHelp = Msg::aLbl('parse_recaptchakey');
        $botlist = self::NewTextAreaAttr('botWhiteList:list', Msg::aLbl('l_botWhiteList'), 'cust', true, 5, 'recaptchaBotWhiteList', 0, 1);
        $botlist->SetFlag(Attr::BM_RAWDATA);

        $attrs = [
            self::newBoolAttr('enabled', Msg::aLbl('l_recapenabled'), true, 'enableRecaptcha'),
            self::newParseTextAttr('siteKey', Msg::aLbl('l_sitekey'), $parseFormat, $parseHelp, true, 'recaptchaSiteKey'),
            self::newParseTextAttr('secretKey', Msg::aLbl('l_secretKey'), $parseFormat, $parseHelp, true, 'recaptchaSecretKey'),
            self::NewSelAttr('type', Msg::aLbl('l_recaptype'), $this->options['lsrecaptcha'], true, 'recaptchaType'),
            self::newIntAttr('maxTries', Msg::aLbl('l_maxTries'), true, 0, 65535, 'recaptchaMaxTries'),
            self::newIntAttr('allowedRobotHits', Msg::aLbl('l_allowedRobotHits'), true, 0, 65535, 'recaptchaAllowedRobotHits'),
            $botlist,
            self::newIntAttr('regConnLimit', Msg::aLbl('l_regConnLimit'), true, 0, null, 'recaptchaRegConnLimit'),
            self::newIntAttr('sslConnLimit', Msg::aLbl('l_sslConnLimit'), true, 0, null, 'recaptchaSslConnLimit'),
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_lsrecaptcha'), $attrs, 'lsrecaptcha');
    }

    protected function add_VT_SEC_RECAP($id) {
        $parseFormat = '/^[[:alnum:]-_]{20,100}$/';
        $parseHelp = Msg::aLbl('parse_recaptchakey');

        $attrs = [
            self::newBoolAttr('enabled', Msg::aLbl('l_recapenabled'), true, 'enableRecaptcha'),
            self::newParseTextAttr('siteKey', Msg::aLbl('l_sitekey'), $parseFormat, $parseHelp, true, 'recaptchaSiteKey'),
            self::newParseTextAttr('secretKey', Msg::aLbl('l_secretKey'), $parseFormat, $parseHelp, true, 'recaptchaSecretKey'),
            self::NewSelAttr('type', Msg::aLbl('l_recaptype'), $this->options['lsrecaptcha'], true, 'recaptchaType'),
            self::newIntAttr('maxTries', Msg::aLbl('l_maxTries'), true, 0, 65535, 'recaptchaMaxTries'),
            self::newIntAttr('regConnLimit', Msg::aLbl('l_concurrentReqLimit'), true, 0, null, 'recaptchaVhReqLimit'),
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_lsrecaptcha'), $attrs, 'lsrecaptcha');
    }

    protected function add_S_SEC_BUBBLEWRAP($id) {
        $attrs = [
            self::NewSelAttr('bubbleWrap', Msg::aLbl('l_bubblewrap'), ['0' => Msg::aLbl('o_disabled'), '1' => Msg::aLbl('o_off'), '2' => Msg::aLbl('o_on')]),
            self::NewTextAreaAttr('bubbleWrapCmd', Msg::aLbl('l_bubblewrapcmd'), 'cust', true, 3, null, 0),
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_bubblewrap'), $attrs);
    }

    protected function add_VT_SEC_BUBBLEWRAP($id) {
        $attrs = [
            self::NewSelAttr('bubbleWrap', Msg::aLbl('l_bubblewrap'), ['' => Msg::aLbl('o_notset'), '1' => Msg::aLbl('o_off'), '2' => Msg::aLbl('o_on')]),
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_bubblewrap'), $attrs);
    }

    protected function add_S_SEC_DENY($id) {
        $attrs = [
            self::NewTextAreaAttr('dir', null, 'cust', true, 15, null, 0, 1, 2)
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_accessdenydir'), $attrs, 'accessDenyDir', 1);
    }

    protected function add_A_SEC_AC($id) {
        $attrs = [
            self::NewTextAreaAttr('allow', Msg::aLbl('l_accessallow'), 'subnet', true, 5, 'accessControl_allow', 0, 0, 1),
            self::NewTextAreaAttr('deny', Msg::aLbl('l_accessdeny'), 'subnet', true, 5, 'accessControl_deny', 0, 0, 1)
        ];

        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_accesscontrol'), $attrs, 'accessControl', 1);
    }

    protected function add_A_EXT_SEL($id) {
        $attrs = [$this->_attrs['ext_type']];
        $this->tblDef[$id] = Tbl::NewSel($id, Msg::aLbl('l_newextapp'), $attrs, $this->options['extTbl']);
    }

    protected function add_T_EXT_SEL($id) {
        $attrs = [$this->_attrs['ext_type']];
        $this->tblDef[$id] = Tbl::NewSel($id, Msg::aLbl('l_newextapp'), $attrs, $this->options['tp_extTbl']);
    }

    protected function add_A_EXT_TOP($id) {
        $align = ['left', 'left', 'left', 'center'];

        $attrs = [
            $this->_attrs['ext_type'],
            self::newViewAttr('name', Msg::aLbl('l_name')),
            self::newViewAttr('address', Msg::aLbl('l_address')),
            self::NewActionAttr($this->options['extTbl'], 'vEd')
        ];
        $this->tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_extapps'), $attrs, 'name', 'A_EXT_SEL', $align, null, 'application', true);
    }

    protected function add_A_EXT_FCGI($id) {
        $attrs = [
            $this->_attrs['ext_name'],
            $this->_attrs['ext_address'],
            $this->_attrs['note'],
            $this->_attrs['ext_maxConns'],
            $this->_attrs['ext_env'],
            $this->_attrs['ext_initTimeout'],
            $this->_attrs['ext_retryTimeout'],
            $this->_attrs['ext_persistConn'],
            $this->_attrs['pcKeepAliveTimeout'],
            $this->_attrs['ext_respBuffer'],
            $this->_attrs['ext_autoStart'],
            $this->_attrs['ext_path'],
            $this->_attrs['ext_backlog'],
            $this->_attrs['ext_instances'],
            $this->_attrs['ext_user'],
            $this->_attrs['ext_group'],
            $this->_attrs['cgiUmask'],
            $this->_attrs['ext_runOnStartUp'],
            self::newIntAttr('extMaxIdleTime', Msg::aLbl('l_maxidletime'), true, -1),
            $this->_attrs['priority']->dup(null, null, 'extAppPriority'),
            $this->_attrs['memSoftLimit'],
            $this->_attrs['memHardLimit'],
            $this->_attrs['procSoftLimit'],
            $this->_attrs['procHardLimit']
        ];
        $defaultExtract = ['type' => 'fcgi'];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_fcgiapp'), $attrs, 'name', null, $defaultExtract);
    }

    protected function add_A_EXT_FCGIAUTH($id) {
        $this->tblDef[$id] = $this->dupTblDef('A_EXT_FCGI', $id, Msg::aLbl('l_extfcgiauth'));
        $this->tblDef[$id]->set(Tbl::FLD_DEFAULTEXTRACT, ['type' => 'fcgiauth']);
    }

    protected function add_A_EXT_LSAPI($id) {
        $this->tblDef[$id] = $this->dupTblDef('A_EXT_FCGI', $id, Msg::aLbl('l_extlsapi'));
        $this->tblDef[$id]->set(Tbl::FLD_DEFAULTEXTRACT, ['type' => 'lsapi']);
    }

    protected function add_A_EXT_LOADBALANCER($id) {
        $parseFormat = '/^(fcgi|fcgiauth|lsapi|servlet|proxy)::.+$/';
        $parseHelp = 'ExtAppType::ExtAppName, allowed types are fcgi, fcgiauth, lsapi, servlet and proxy. e.g. fcgi::myphp, servlet::tomcat';

        $attrs = [$this->_attrs['ext_name'],
            self::NewParseTextAreaAttr('workers', Msg::aLbl('l_workers'), $parseFormat, $parseHelp, true, 3, 'extWorkers', 0, 0, 1),
            $this->_attrs['note'],
        ];
        $defaultExtract = ['type' => 'loadbalancer'];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_extlb'), $attrs, 'name', null, $defaultExtract);
    }

    protected function add_A_EXT_LOGGER($id) {
        $attrs = [$this->_attrs['ext_name'],
            self::newTextAttr('address', Msg::aLbl('l_loggeraddress'), 'addr', true), //optional
            $this->_attrs['note'],
            $this->_attrs['ext_maxConns'],
            $this->_attrs['ext_env'],
            $this->_attrs['ext_path'],
            $this->_attrs['ext_instances'],
            $this->_attrs['ext_user'],
            $this->_attrs['ext_group'],
            $this->_attrs['cgiUmask'],
            $this->_attrs['priority']->dup(null, null, 'extAppPriority')
        ];
        $defaultExtract = ['type' => 'logger'];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_extlogger'), $attrs, 'name', null, $defaultExtract);
    }

    protected function add_A_EXT_SERVLET($id) {
        $attrs = [$this->_attrs['ext_name'],
            $this->_attrs['ext_address'],
            $this->_attrs['note'],
            $this->_attrs['ext_maxConns'],
            $this->_attrs['pcKeepAliveTimeout'],
            $this->_attrs['ext_env'],
            $this->_attrs['ext_initTimeout'],
            $this->_attrs['ext_retryTimeout'],
            $this->_attrs['ext_respBuffer']
        ];
        $defaultExtract = ['type' => 'servlet'];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_extservlet'), $attrs, 'name', null, $defaultExtract);
    }

    protected function add_A_EXT_PROXY($id) {
        $attrs = [$this->_attrs['ext_name'],
            self::newTextAttr('address', Msg::aLbl('l_address'), 'wsaddr', false, 'expWSAddress'),
            $this->_attrs['note'],
            $this->_attrs['ext_maxConns'],
            $this->_attrs['pcKeepAliveTimeout'],
            $this->_attrs['ext_env'],
            $this->_attrs['ext_initTimeout'],
            $this->_attrs['ext_retryTimeout'],
            $this->_attrs['ext_respBuffer']
        ];
        $defaultExtract = ['type' => 'proxy'];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_extproxy'), $attrs, 'name', null, $defaultExtract);
    }

    protected function add_T_EXT_TOP($id) {
        $align = ['center', 'center', 'left', 'center'];

        $attrs = [
            $this->_attrs['ext_type'],
            self::newViewAttr('name', Msg::aLbl('l_name')),
            self::newViewAttr('address', Msg::aLbl('l_address')),
            self::NewActionAttr($this->options['tp_extTbl'], 'vEd')
        ];
        $this->tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_extapps'), $attrs, 'name', 'T_EXT_SEL', $align, null, 'application', true);
    }

    protected function add_T_EXT_FCGI($id) {
        $this->tblDef[$id] = $this->dupTblDef('A_EXT_FCGI', $id);
        $this->tblDef[$id]->resetAttrEntry(0, $this->_attrs['tp_name']);
    }

    protected function add_T_EXT_FCGIAUTH($id) {
        $this->tblDef[$id] = $this->dupTblDef('A_EXT_FCGIAUTH', $id);
        $this->tblDef[$id]->resetAttrEntry(0, $this->_attrs['tp_name']);
    }

    protected function add_T_EXT_LSAPI($id) {
        $this->tblDef[$id] = $this->dupTblDef('A_EXT_LSAPI', $id);
        $this->tblDef[$id]->resetAttrEntry(0, $this->_attrs['tp_name']);
    }

    protected function add_T_EXT_LOADBALANCER($id) {
        $this->tblDef[$id] = $this->dupTblDef('A_EXT_LOADBALANCER', $id);
        $this->tblDef[$id]->resetAttrEntry(0, $this->_attrs['tp_name']);
    }

    protected function add_T_EXT_LOGGER($id) {
        $this->tblDef[$id] = $this->dupTblDef('A_EXT_LOGGER', $id);
        $this->tblDef[$id]->resetAttrEntry(0, $this->_attrs['tp_name']);
    }

    protected function add_T_EXT_SERVLET($id) {
        $this->tblDef[$id] = $this->dupTblDef('A_EXT_SERVLET', $id);
        $this->tblDef[$id]->resetAttrEntry(0, $this->_attrs['tp_name']);
    }

    protected function add_T_EXT_PROXY($id) {
        $this->tblDef[$id] = $this->dupTblDef('A_EXT_PROXY', $id);
        $this->tblDef[$id]->resetAttrEntry(0, $this->_attrs['tp_name']);
    }

    protected function add_A_SCRIPT($id) {
        $attrs = [
            $this->_attrs['suffix'],
            $this->_attrs['scriptHandler_type'],
            $this->_attrs['scriptHandler'],
            $this->_attrs['note'],
        ];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_shdef'), $attrs, 'suffix');
    }

    protected function add_A_SCRIPT_TOP($id) {
        $align = ['center', 'center', 'center', 'center'];

        $attrs = [
            $this->_attrs['suffix'],
            $this->_attrs['scriptHandler_type'],
            $this->_attrs['scriptHandler'],
            self::NewActionAttr('A_SCRIPT', 'Ed')
        ];
        $this->tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_shdef'), $attrs, 'suffix', 'A_SCRIPT', $align, null, 'script');
    }

    protected function add_S_RAILS($id) {
        $attrs = [
            self::newPathAttr('binPath', Msg::aLbl('l_rubybin'), 'file', 1, 'x', true, 'rubyBin'),
            $this->_attrs['appserverEnv'],
            $this->_attrs['ext_maxConns'],
            $this->_attrs['ext_env'],
            $this->_attrs['ext_initTimeout'],
            $this->_attrs['ext_retryTimeout'],
            $this->_attrs['pcKeepAliveTimeout'],
            $this->_attrs['ext_respBuffer'],
            $this->_attrs['ext_backlog'],
            $this->_attrs['ext_runOnStartUp'],
            self::newIntAttr('extMaxIdleTime', Msg::aLbl('l_maxidletime'), true, -1),
            $this->_attrs['priority']->dup(null, null, 'extAppPriority'),
            $this->_attrs['memSoftLimit'],
            $this->_attrs['memHardLimit'],
            $this->_attrs['procSoftLimit'],
            $this->_attrs['procHardLimit']
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_railssettings'), $attrs, 'railsDefaults');
    }

    protected function add_S_WSGI($id) {
        $attrs = [
            self::newPathAttr('binPath', Msg::aLbl('l_wsgibin'), 'file', 1, 'x', true, 'wsgiBin'),
            $this->_attrs['appserverEnv'],
            $this->_attrs['ext_maxConns'],
            $this->_attrs['ext_env'],
            $this->_attrs['ext_initTimeout'],
            $this->_attrs['ext_retryTimeout'],
            $this->_attrs['pcKeepAliveTimeout'],
            $this->_attrs['ext_respBuffer'],
            $this->_attrs['ext_backlog'],
            $this->_attrs['ext_runOnStartUp'],
            self::newIntAttr('extMaxIdleTime', Msg::aLbl('l_maxidletime'), true, -1),
            $this->_attrs['priority']->dup(null, null, 'extAppPriority'),
            $this->_attrs['memSoftLimit'],
            $this->_attrs['memHardLimit'],
            $this->_attrs['procSoftLimit'],
            $this->_attrs['procHardLimit']
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_wsgisettings'), $attrs, 'wsgiDefaults');
    }

    protected function add_S_NODEJS($id) {
        $attrs = [
            self::newPathAttr('binPath', Msg::aLbl('l_nodebin'), 'file', 1, 'x', true, 'nodeBin'),
            $this->_attrs['appserverEnv'],
            $this->_attrs['ext_maxConns'],
            $this->_attrs['ext_env'],
            $this->_attrs['ext_initTimeout'],
            $this->_attrs['ext_retryTimeout'],
            $this->_attrs['pcKeepAliveTimeout'],
            $this->_attrs['ext_respBuffer'],
            $this->_attrs['ext_backlog'],
            $this->_attrs['ext_runOnStartUp'],
            self::newIntAttr('extMaxIdleTime', Msg::aLbl('l_maxidletime'), true, -1),
            $this->_attrs['priority']->dup(null, null, 'extAppPriority'),
            $this->_attrs['memSoftLimit'],
            $this->_attrs['memHardLimit'],
            $this->_attrs['procSoftLimit'],
            $this->_attrs['procHardLimit']
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_nodesettings'), $attrs, 'nodeDefaults');
    }

    protected function add_V_TOP($id) {
        $align = ['left', 'left', 'center'];

        $attrs = [
            self::newViewAttr('name', Msg::aLbl('l_name')),
            self::newViewAttr('vhRoot', Msg::aLbl('l_vhroot')),
            self::NewActionAttr('V_TOPD', 'Xd')
        ];
        $this->tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_vhostlist'), $attrs, 'name', 'V_TOPD', $align, null, 'web', true);
    }

    protected function add_V_BASE($id) {
        $attrs = [
            self::newTextAttr('name', Msg::aLbl('l_vhname'), 'vhname', false, 'vhName'),
            self::newTextAttr('vhRoot', Msg::aLbl('l_vhroot'), 'cust', false), // do not check path for vhroot, it may be different owner
            self::newPathAttr('configFile', Msg::aLbl('l_configfile'), 'filevh', 3, 'rwc', false),
            $this->_attrs['note']
        ];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_base'), $attrs, 'name');
    }

    protected function add_V_BASE_CONN($id) {
        $attrs = [
            $this->_attrs['vh_maxKeepAliveReq'],
        ];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_connection'), $attrs, 'name');
    }

    protected function add_V_BASE_THROTTLE($id) {
        $attrs = [
            $this->_attrs['staticReqPerSec'],
            $this->_attrs['dynReqPerSec'],
            $this->_attrs['outBandwidth'],
            $this->_attrs['inBandwidth']
        ];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_perclientthrottle'), $attrs, 'name');
    }

    protected function add_L_TOP($id) {
        $align = ['center', 'center', 'center', 'center', 'center'];

        $attrs = [
            self::newViewAttr('name', Msg::aLbl('l_listenername')),
            self::newViewAttr('ip', Msg::aLbl('l_ip')),
            self::newViewAttr('port', Msg::aLbl('l_port')),
            self::newBoolAttr('secure', Msg::aLbl('l_secure')),
            self::NewActionAttr('L_GENERAL', 'Xd')
        ];
        $this->tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_listenerlist'), $attrs, 'name', 'L_GENERAL', $align, null, 'link', true);
    }

    protected function add_ADM_L_TOP($id) {
        $align = ['center', 'center', 'center', 'center', 'center'];

        $attrs = [
            self::newViewAttr('name', Msg::aLbl('l_listenername')),
            self::newViewAttr('ip', Msg::aLbl('l_ip')),
            self::newViewAttr('port', Msg::aLbl('l_port')),
            self::newBoolAttr('secure', Msg::aLbl('l_secure')),
            self::NewActionAttr('ADM_L_GENERAL', 'Xd', false)//cannot delete all
        ];
        $this->tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_listenerlist'), $attrs, 'name', 'ADM_L_GENERAL', $align, null, 'link', true);
    }

    protected function add_ADM_L_GENERAL($id) {
        $name = self::newTextAttr('name', Msg::aLbl('l_listenername'), 'name', false, 'listenerName');
        $addr = self::newCustFlagAttr('address', Msg::aLbl('l_address'), (Attr::BM_HIDE | Attr::BM_NOEDIT), false);
        $ip = self::NewSelAttr('ip', Msg::aLbl('l_ip'), $this->options['ip'], false, 'listenerIP');
        $ip->SetFlag(Attr::BM_NOFILE);
        $port = self::newIntAttr('port', Msg::aLbl('l_port'), false, 0, 65535, 'listenerPort');
        $port->SetFlag(Attr::BM_NOFILE);

        $attrs = [
            $name,
            $addr, $ip, $port,
            self::newBoolAttr('secure', Msg::aLbl('l_secure'), false, 'listenerSecure'),
            $this->_attrs['note'],
        ];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_adminlistenersettings'), $attrs, 'name');
    }

    protected function add_L_VHMAP($id) {
        $attrs = [
            $this->_attrs['l_vhost'],
            $this->_attrs['l_domain']
        ];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_vhmappings'), $attrs, 'vhost', 'virtualHostMapping');
    }

    protected function add_L_VHMAP_TOP($id) {
        $align = ['left', 'left', 'center'];

        $attrs = [
            $this->_attrs['l_vhost'],
            $this->_attrs['l_domain'],
            self::NewActionAttr('L_VHMAP', 'Ed')
        ];
        $this->tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_vhmappings'), $attrs, 'vhost', 'L_VHMAP', $align, 'virtualHostMapping', 'web_link', false);
    }

    protected function add_LVT_SSL_CERT($id) {
        $attrs = [
            self::newTextAttr('keyFile', Msg::aLbl('l_keyfile'), 'cust'),
            self::newTextAttr('certFile', Msg::aLbl('l_certfile'), 'cust'),
            self::newBoolAttr('certChain', Msg::aLbl('l_certchain')),
            self::newTextAttr('CACertPath', Msg::aLbl('l_cacertpath'), 'cust'),
            self::newTextAttr('CACertFile', Msg::aLbl('l_cacertfile'), 'cust'),
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_ssl'), $attrs, 'sslCert');
    }

    protected function add_LVT_SSL($id) {
        $attrs = [
            self::newCheckBoxAttr('sslProtocol', Msg::aLbl('l_protocolver'), ['1' => 'SSL v3.0', '2' => 'TLS v1.0', '4' => 'TLS v1.1', '8' => 'TLS v1.2', '16' => 'TLS v1.3']),
            self::newTextAttr('ciphers', Msg::aLbl('l_ciphers'), 'cust'),
            self::newBoolAttr('enableECDHE', Msg::aLbl('l_enableecdhe')),
            self::newBoolAttr('enableDHE', Msg::aLbl('l_enabledhe')),
            self::newTextAttr('DHParam', Msg::aLbl('l_dhparam'), 'cust'),
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_sslprotocol'), $attrs);
    }

    protected function add_L_SSL_FEATURE($id) {
        $attrs = [
            $this->_attrs['ssl_renegProtection'],
            $this->_attrs['sslSessionCache'],
            $this->_attrs['sslSessionTickets'],
            self::newCheckBoxAttr('enableSpdy', Msg::aLbl('l_enablespdy'), ['1' => 'SPDY/2', '2' => 'SPDY/3', '4' => 'HTTP/2', '8' => 'HTTP/3', '0' => Msg::aLbl('o_none')]),
            self::newBoolAttr('enableQuic', Msg::aLbl('l_allowquic'), true, 'allowQuic'),
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_securityandfeatures'), $attrs);
    }

    protected function add_VT_SSL_FEATURE($id) {
        $attrs = [
            $this->_attrs['ssl_renegProtection'],
            $this->_attrs['sslSessionCache'],
            $this->_attrs['sslSessionTickets'],

            self::newCheckBoxAttr('enableSpdy', Msg::aLbl('l_enablespdy'), ['1' => 'SPDY/2', '2' => 'SPDY/3', '4' => 'HTTP/2', '8' => 'HTTP/3', '0' => Msg::aLbl('o_none')]),
            self::newBoolAttr('enableQuic', Msg::aLbl('l_enablequic'), true, 'vhEnableQuic'),
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::UIStr('tab_sec'), $attrs);
    }

    protected function add_LVT_SSL_OCSP($id) {
        $attrs = [
            self::newBoolAttr('enableStapling', Msg::aLbl('l_enablestapling')),
            self::newIntAttr('ocspRespMaxAge', Msg::aLbl('l_ocsprespmaxage'), true, -1),
            self::newTextAttr('ocspResponder', Msg::aLbl('l_ocspresponder'), 'httpurl'),
            self::newTextAttr('ocspCACerts', Msg::aLbl('l_ocspcacerts'), 'cust'),
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_ocspstapling'), $attrs);
    }

    protected function add_T_TOP($id) {
        $align = ['left', 'left', 'center'];

        $attrs = [
            self::newViewAttr('name', Msg::aLbl('l_name')),
            self::newViewAttr('listeners', Msg::aLbl('l_mappedlisteners')),
            self::NewActionAttr('T_TOPD', 'Xd')
        ];
        $this->tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_tplist'), $attrs, 'name', 'T_TOPD', $align, null, 'form', true);
    }

    protected function add_T_TOPD($id) {
        $attrs = [
            self::newTextAttr('name', Msg::aLbl('l_tpname'), 'vhname', false, 'templateName'),
            $this->_attrs['tp_templateFile'],
            $this->_attrs['tp_listeners'],
            $this->_attrs['note']
        ];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_vhtemplate'), $attrs, 'name');
    }

    protected function add_T_MEMBER_TOP($id) {
        $align = ['left', 'left', 'center'];

        $attrs = [
            $this->_attrs['tp_vhName'],
            $this->_attrs['tp_vhDomain'],
            self::NewActionAttr('T_MEMBER', 'vEdi')
        ];

        $this->tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_membervhosts'), $attrs, 'vhName', 'T_MEMBER', $align, null, 'web', false);
    }

    protected function add_T_MEMBER($id) {
        $vhroot = self::newTextAttr('vhRoot', Msg::aLbl('l_membervhroot'), 'cust', true, 'memberVHRoot');
        $vhroot->_note = Msg::aLbl('l_membervhroot_note');

        $attrs = [
            $this->_attrs['tp_vhName'],
            $this->_attrs['tp_vhDomain'],
            $this->_attrs['tp_vhAliases'],
            $vhroot
        ];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_membervhosts'), $attrs, 'vhName');
    }

    protected function add_V_LOG($id) {
        $attrs = [
            self::newBoolAttr('useServer', Msg::aLbl('l_useServer'), false, 'logUseServer'),
            $this->_attrs['fileName3']->dup(null, null, 'vhlog_fileName'),
            self::NewSelAttr('logLevel', Msg::aLbl('l_loglevel'), $this->options['logLevel'], true, 'vhlog_logLevel'),
            $this->_attrs['rollingSize'],
            $this->_attrs['keepDays'],
        ];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_vhlog'), $attrs, 'fileName');
    }

    protected function add_V_ACLOG_TOP($id) {
        $attrs = [
            self::NewSelAttr('useServer', Msg::aLbl('l_logcontrol'), $this->options['aclogctrl'], false, 'aclogUseServer'),
            $this->_attrs['fileName3']->dup(null, null, 'accessLog_fileName'),
            $this->_attrs['logFormat'],
            $this->_attrs['rollingSize'],
            self::NewActionAttr('V_ACLOG', 'Ed'),
        ];
        $this->tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_accesslog'), $attrs, 'fileName', 'V_ACLOG');
    }

    protected function add_V_ACLOG($id) {
        $attrs = [
            self::NewSelAttr('useServer', Msg::aLbl('l_logcontrol'), $this->options['aclogctrl'], false, 'aclogUseServer'),
            $this->_attrs['fileName3']->dup(null, null, 'vhaccessLog_fileName'),
            self::NewSelAttr('pipedLogger', Msg::aLbl('l_pipedlogger'), 'extprocessor:logger', true, 'accessLog_pipedLogger'),
            $this->_attrs['logFormat'],
            $this->_attrs['logHeaders'],
            $this->_attrs['rollingSize'],
            $this->_attrs['keepDays'],
            self::newPathAttr('bytesLog', Msg::aLbl('l_byteslog'), 'file0', 3, 'r', true, 'accessLog_bytesLog'),
            $this->_attrs['compressArchive']
        ];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_accesslog'), $attrs, 'fileName');
    }

    protected function add_VT_INDXF($id) {
        $attrs = [
            self::NewSelAttr('useServer', Msg::aLbl('l_useserverindexfiles'), [0 => Msg::aLbl('o_no'), 1 => Msg::aLbl('o_yes'), 2 => 'Addition'], false, 'indexUseServer'),
            $this->_attrs['indexFiles'],
            $this->_attrs['autoIndex'],
            self::newTextAttr('autoIndexURI', Msg::aLbl('l_autoindexuri'), 'uri')
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_indexfiles'), $attrs);
    }

    protected function get_cust_status_code() {
        $status = [
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Time-out',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Large',
            415 => 'Unsupported Media Type',
            416 => 'Requested range not satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Time-out',
            505 => 'HTTP Version not supported'
        ];
        $options = [];
        foreach ($status as $key => $value) {
            $options[$key] = "${key}  ${value}";
        }

        return $options;
    }

    protected function add_VT_ERRPG_TOP($id) {
        $align = ['left', 'left', 'center'];
        $errCodeOptions = $this->get_cust_status_code();
        $attrs = [
            self::NewSelAttr('errCode', Msg::aLbl('l_errcode'), $errCodeOptions, false),
            self::newViewAttr('url', Msg::aLbl('l_url')),
            self::NewActionAttr('VT_ERRPG', 'Ed')
        ];
        $this->tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_custerrpages'), $attrs, 'errCode', 'VT_ERRPG', $align, 'errPage', 'file', true);
    }

    protected function add_VT_ERRPG($id) {
        $attrs = [
            self::NewSelAttr('errCode', Msg::aLbl('l_errcode'), $this->get_cust_status_code(), false),
            self::newTextAttr('url', Msg::aLbl('l_url'), 'cust', false, 'errURL'),
            $this->_attrs['note'],
        ];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_custerrpages'), $attrs, 'errCode', 'errPage');
    }

    protected function get_realm_attrs() {
        return [
            'realm_type' => self::NewSelAttr('type', Msg::aLbl('l_realmtype'), $this->options['realmType'], false, 'realmType'),
            'realm_name' => self::newTextAttr('name', Msg::aLbl('l_realmname'), 'vhname', false, 'realmName'),
            'realm_udb_maxCacheSize' => self::newIntAttr('userDB:maxCacheSize', Msg::aLbl('l_userdbmaxcachesize'), true, 0, '100K', 'userDBMaxCacheSize'),
            'realm_udb_cacheTimeout' => self::newIntAttr('userDB:cacheTimeout', Msg::aLbl('l_userdbcachetimeout'), true, 0, 3600, 'userDBCacheTimeout'),
            'realm_gdb_maxCacheSize' => self::newIntAttr('groupDB:maxCacheSize', Msg::aLbl('l_groupdbmaxcachesize'), true, 0, '100K', 'groupDBMaxCacheSize'),
            'realm_gdb_cacheTimeout' => self::newIntAttr('groupDB:cacheTimeout', Msg::aLbl('l_groupdbcachetimeout'), true, 0, 3600, 'groupDBCacheTimeout')];
    }

    protected function add_V_REALM_FILE($id) {
        $udbLoc = self::newPathAttr('userDB:location', Msg::aLbl('l_userdblocation'), 'file', 3, 'rwc', false, 'userDBLocation');
        $udbLoc->href = '&t1=V_UDB_TOP&r1=$R';
        $gdbLoc = self::newPathAttr('groupDB:location', Msg::aLbl('l_groupdblocation'), 'file', 3, 'rwc', true, 'GroupDBLocation');
        $gdbLoc->href = '&t1=V_GDB_TOP&r1=$R';

        $realm_attr = $this->get_realm_attrs();
        $attrs = [
            $realm_attr['realm_name'],
            $this->_attrs['note'],
            $udbLoc,
            $realm_attr['realm_udb_maxCacheSize'],
            $realm_attr['realm_udb_cacheTimeout'],
            $gdbLoc,
            $realm_attr['realm_gdb_maxCacheSize'],
            $realm_attr['realm_gdb_cacheTimeout']
        ];
        $defaultExtract = ['type' => 'file'];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_passfilerealmdef'), $attrs, 'name', null, $defaultExtract);
    }

    protected function add_T_REALM_FILE($id) {
        $realm_attr = $this->get_realm_attrs();
        $attrs = [
            $realm_attr['realm_name'],
            $this->_attrs['note'],
            self::newTextAttr('userDB:location', Msg::aLbl('l_userdblocation'), 'cust', false, 'userDBLocation'),
            $realm_attr['realm_udb_maxCacheSize'],
            $realm_attr['realm_udb_cacheTimeout'],
            self::newTextAttr('groupDB:location', Msg::aLbl('l_groupdblocation'), 'cust', true, 'GroupDBLocation'),
            $realm_attr['realm_gdb_maxCacheSize'],
            $realm_attr['realm_gdb_cacheTimeout']
        ];
        $defaultExtract = ['type' => 'file'];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_passfilerealmdef'), $attrs, 'name', null, $defaultExtract);
    }

    protected function add_V_UDB_TOP($id) {
        $align = ['left', 'left', 'center'];

        $attrs = [
            self::newViewAttr('name', Msg::aLbl('l_username')),
            self::newViewAttr('group', Msg::aLbl('l_groups')),
            self::NewActionAttr('V_UDB', 'Ed')
        ];
        $this->tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_userdbentries'), $attrs, 'name', 'V_UDB', $align, null, 'administrator', false);
        $this->tblDef[$id]->set(Tbl::FLD_SHOWPARENTREF, true);
    }

    protected function add_V_UDB($id) {
        $attrs = [
            self::newTextAttr('name', Msg::aLbl('l_username'), 'name', false, 'UDBusername'),
            self::newTextAttr('group', Msg::aLbl('l_groups'), 'name', true, 'UDBgroup', 1),
            self::NewPassAttr('pass', Msg::aLbl('l_newpass'), false, 'UDBpass'),
            self::NewPassAttr('pass1', Msg::aLbl('l_retypepass'), false, 'UDBpass1')
        ];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_userdbentry'), $attrs, 'name');
        $this->tblDef[$id]->set(Tbl::FLD_SHOWPARENTREF, true);
    }

    protected function add_V_GDB_TOP($id) {
        $align = ['left', 'left', 'center'];

        $attrs = [
            self::newViewAttr('name', Msg::aLbl('l_groupname')),
            self::newViewAttr('users', Msg::aLbl('l_users')),
            self::NewActionAttr('V_GDB', 'Ed')
        ];
        $this->tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_groupdbentries'), $attrs, 'name', 'V_GDB', $align);
        $this->tblDef[$id]->set(Tbl::FLD_SHOWPARENTREF, true);
    }

    protected function add_V_GDB($id) {
        $users = self::NewTextAreaAttr('users', Msg::aLbl('l_users'), 'name', true, 15, 'gdb_users', 0, 0, 1);
        $users->SetGlue(' ');

        $attrs = [
            self::newTextAttr('name', Msg::aLbl('l_groupname'), 'vhname', false, 'gdb_groupname'),
            $users,
        ];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_groupdbentry'), $attrs, 'name');
        $this->tblDef[$id]->set(Tbl::FLD_SHOWPARENTREF, true);
    }

    protected function add_VT_CTX_SEL($id) {
        $attrs = [$this->_attrs['ctx_type']];
        $this->tblDef[$id] = Tbl::NewSel($id, Msg::aLbl('l_newcontext'), $attrs, $this->options['ctxTbl']);
    }

    protected function get_ctx_attrs($type) {
        if ($type == 'auth') {
            return [
                self::NewSelAttr('realm', Msg::aLbl('l_realm'), 'realm'),
                self::newTextAttr('authName', Msg::aLbl('l_authname'), 'name'),
                self::newTextAttr('required', Msg::aLbl('l_requiredauthuser'), 'cust'),
                self::NewTextAreaAttr('accessControl:allow', Msg::aLbl('l_accessallowed'), 'subnet', true, 3, 'accessAllowed', 0, 0, 1),
                self::NewTextAreaAttr('accessControl:deny', Msg::aLbl('l_accessdenied'), 'subnet', true, 3, 'accessDenied', 0, 0, 1),
                self::NewSelAttr('authorizer', Msg::aLbl('l_authorizer'), 'extprocessor:fcgiauth', true, 'extAuthorizer')
            ];
        }
        if ($type == 'rewrite') {
            $rules = self::NewTextAreaAttr('rewrite:rules', Msg::aLbl('l_rewriterules'), 'cust', true, 6, 'rewriteRules', 1, 1);
            $rules->SetFlag(Attr::BM_RAWDATA);

            return [
                self::newBoolAttr('rewrite:enable', Msg::aLbl('l_enablerewrite'), true, 'enableRewrite'),
                self::newBoolAttr('rewrite:inherit', Msg::aLbl('l_rewriteinherit'), true, 'rewriteInherit'),
                self::newTextAttr('rewrite:base', Msg::aLbl('l_rewritebase'), 'uri', true, 'rewriteBase'),
                $rules,
            ];
        }
        if ($type == 'charset') {
            return [// todo: merge below
                self::NewSelAttr('addDefaultCharset', Msg::aLbl('l_adddefaultcharset'), ['off' => 'Off', 'on' => 'On']),
                self::newTextAttr('defaultCharsetCustomized', Msg::aLbl('l_defaultcharsetcustomized'), 'cust'),
                $this->_attrs['enableIpGeo']
            ];
        }
        if ($type == 'uri') {
            return [
                $this->_attrs['ctx_uri'],
                $this->_attrs['ctx_order']];
        }
    }

    protected function add_VT_WBSOCK_TOP($id) {
        $align = ['left', 'left', 'center'];

        $attrs = [
            self::newViewAttr('uri', Msg::aLbl('l_uri')),
            self::newViewAttr('address', Msg::aLbl('l_address')),
            self::NewActionAttr('VT_WBSOCK', 'Ed')
        ];
        $this->tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_websocketsetup'), $attrs, 'uri', 'VT_WBSOCK', $align, null, 'web_link', true);
    }

    protected function add_VT_WBSOCK($id) {
        $attrs = [
            $this->_attrs['ctx_uri']->dup(null, null, 'wsuri'),
            $this->_attrs['ext_address']->dup(null, null, 'wsaddr'),
            $this->_attrs['note'],
        ];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_websocketdef'), $attrs, 'uri');
    }

    protected function add_T_GENERAL1($id) {
        $attrs = [
            $this->_attrs['tp_vhRoot'],
            self::newParseTextAttr('configFile', Msg::aLbl('l_configfile'), '/\$VH_NAME.*\.conf$/', Msg::aLbl('parse_tpvhconffile'), false, 'templateVHConfigFile'),
            $this->_attrs['vh_maxKeepAliveReq'],
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_base'), $attrs);
    }

    protected function add_T_SEC_FILE($id) {
        $attrs = [
            $this->_attrs['vh_allowSymbolLink'],
            $this->_attrs['vh_enableScript'],
            $this->_attrs['vh_restrained']
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_fileaccesscontrol'), $attrs);
    }

    protected function add_T_SEC_CONN($id) {
        $attrs = [
            $this->_attrs['staticReqPerSec'],
            $this->_attrs['dynReqPerSec'],
            $this->_attrs['outBandwidth'],
            $this->_attrs['inBandwidth']
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_perclientthrottle'), $attrs);
    }

    protected function add_T_LOG($id) {
        $this->tblDef[$id] = $this->dupTblDef('V_LOG', $id);
        $this->tblDef[$id]->resetAttrEntry(1, $this->_attrs['tp_vrFile']);
    }

    protected function add_T_ACLOG_TOP($id) {
        $attrs = [
            self::NewSelAttr('useServer', Msg::aLbl('l_logcontrol'), $this->options['aclogctrl'], false, 'aclogUseServer'),
            $this->_attrs['tp_vrFile'],
            $this->_attrs['logFormat'],
            $this->_attrs['rollingSize'],
            self::NewActionAttr('T_ACLOG', 'Ed'),
        ];
        $this->tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_accesslog'), $attrs, 'fileName', 'T_ACLOG');
    }

    protected function add_T_ACLOG($id) {
        $this->tblDef[$id] = $this->dupTblDef('V_ACLOG', $id);
        $this->tblDef[$id]->resetAttrEntry(1, $this->_attrs['tp_vrFile']);
    }

    protected function add_ADM_PHP($id) {
        $attrs = [
            self::newBoolAttr('enableCoreDump', Msg::aLbl('l_enablecoredump'), false),
            self::newIntAttr('sessionTimeout', Msg::aLbl('l_sessiontimeout'), true, 60, null, 'consoleSessionTimeout')
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::UIStr('tab_g'), $attrs);
    }

    protected function add_ADM_USR_TOP($id) {
        $align = ['left', 'center'];
        $attrs = [
            self::newViewAttr('name', Msg::aLbl('l_username')),
            self::NewActionAttr('ADM_USR', 'Ed', false) //not allow null - cannot delete all
        ];
        $this->tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_adminusers'), $attrs, 'name', 'ADM_USR_NEW', $align, null, 'administrator');
    }

    protected function add_ADM_USR($id) {
        $attrs = [
            self::newTextAttr('name', Msg::aLbl('l_username'), 'admname', false),
            self::NewPassAttr('oldpass', Msg::aLbl('l_oldpass'), false, 'adminOldPass'),
            self::NewPassAttr('pass', Msg::aLbl('l_newpass'), false),
            self::NewPassAttr('pass1', Msg::aLbl('l_retypepass'), false)
        ];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_adminuser'), $attrs, 'name');
    }

    protected function add_ADM_USR_NEW($id) {
        $attrs = [
            self::newTextAttr('name', Msg::aLbl('l_username'), 'admname', false),
            self::NewPassAttr('pass', Msg::aLbl('l_newpass'), false),
            self::NewPassAttr('pass1', Msg::aLbl('l_retypepass'), false)
        ];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_newadminuser'), $attrs, 'name');
    }

    protected function add_ADM_ACLOG($id) {
        $attrs = [
            self::NewSelAttr('useServer', Msg::aLbl('l_logcontrol'), [0 => Msg::aLbl('o_ownlogfile'), 1 => Msg::aLbl('o_serverslogfile'), 2 => Msg::aLbl('o_disabled')], false, 'aclogUseServer'),
            $this->_attrs['fileName3']->dup(null, null, 'accessLog_fileName'),
            $this->_attrs['logFormat'],
            $this->_attrs['logHeaders'],
            $this->_attrs['rollingSize'],
            $this->_attrs['keepDays'],
            self::newPathAttr('bytesLog', Msg::aLbl('l_byteslog'), 'file0', 3, 'r', true, 'accessLog_bytesLog'),
            $this->_attrs['compressArchive']
        ];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_accesslog'), $attrs, 'fileName');
    }

    protected function add_S_MIME_TOP($id) {
        $align = ['left', 'left', 'center'];

        $attrs = [
            self::newViewAttr('suffix', Msg::aLbl('l_suffix')),
            self::newViewAttr('type', Msg::aLbl('l_mimetype')),
            self::NewActionAttr('S_MIME', 'Ed')
        ];
        $this->tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_mimetypedef'), $attrs, 'suffix', 'S_MIME', $align, null, 'file');
    }

    protected function add_S_MIME($id) {
        $attrs = [
            $this->_attrs['suffix']->dup('suffix', Msg::aLbl('l_suffix'), 'mimesuffix'),
            self::newParseTextAttr('type', Msg::aLbl('l_mimetype'), "/^[A-z0-9_\-\.\+]+\/[A-z0-9_\-\.\+]+(\s*;?.*)$/", Msg::aLbl('parse_mimetype'), false, 'mimetype')
        ];
        $this->tblDef[$id] = Tbl::newIndexed($id, Msg::aLbl('l_mimetypeentry'), $attrs, 'suffix');
    }

    protected function add_SERVICE_SUSPENDVH($id) {
        $attrs = [self::newCustFlagAttr('suspendedVhosts', null, (Attr::BM_HIDE | Attr::BM_NOEDIT), true, 'vhname', null, null, 1)
        ];
        $this->tblDef[$id] = Tbl::newRegular($id, Msg::aLbl('l_suspendvh'), $attrs);
    }
}

<?php
use CVendor_LiteSpeed_Msg as Msg;
use CVendor_LiteSpeed_Tbl as Tbl;
use CVendor_LiteSpeed_Node as Node;
use CVendor_LiteSpeed_OWS_Attr as Attr;
use CVendor_LiteSpeed_TblDefBase as TblDefBase;

class CVendor_LiteSpeed_OWS_TblDef extends CVendor_LiteSpeed_TblDefBase {
    private static $instance;

    public static function getInstance() {
        if (static::$instance == null) {
            static::$instance = new CVendor_LiteSpeed_OWS_TblDef();
        }

        return static::$instance;
    }

    private function __construct() {
        $this->loadCommonOptions();
        $this->loadCommonAttrs();
        $this->loadSpecials();
    }

    protected function loadSpecials() {
        // define special block contains raw data
        parent::loadSpecials();

        $this->addSpecial('phpIniOverride', [], 'data');
        $this->addSpecial('rewrite', ['enable', 'autoLoadHtaccess', 'logLevel', 'map', 'inherit', 'base'], 'rules');
        $this->addSpecial('virtualHostConfig:rewrite', ['enable', 'autoLoadHtaccess', 'logLevel', 'map', 'inherit', 'base'], 'rules'); // for template

        $tags = array_merge(['ls_enabled', 'note', 'internal', 'urlFilter'], $this->getModuleTags());
        $this->addSpecial('module', $tags, 'param');

        $this->addSpecial('urlFilter', ['ls_enabled'], 'param');
    }

    protected function loadCommonOptions() {
        parent::loadCommonOptions();
        $this->_options['scriptHandler'] = [
            'fcgi' => 'Fast CGI', 'servlet' => 'Servlet Engine',
            'lsapi' => 'LiteSpeed SAPI',
            'proxy' => 'Web Server', 'cgi' => 'CGI',
            'loadbalancer' => 'Load Balancer', 'module' => 'Module Handler'];

        $this->_options['ctxType'] = [
            'null' => 'Static', 'webapp' => 'Java Web App',
            'servlet' => 'Servlet', 'fcgi' => 'Fast CGI',
            'lsapi' => 'LiteSpeed SAPI',
            'proxy' => 'Proxy', 'cgi' => 'CGI',
            'loadbalancer' => 'Load Balancer',
            'redirect' => 'Redirect',
            'appserver' => 'App Server', 'module' => 'Module Handler'];

        $this->_options['ctxTbl'] = [
            0 => 'type', 1 => 'VT_CTXG',
            'null' => 'VT_CTXG', 'webapp' => 'VT_CTXJ',
            'servlet' => 'VT_CTXS', 'fcgi' => 'VT_CTXF',
            'lsapi' => 'VT_CTXL',
            'proxy' => 'VT_CTXP', 'cgi' => 'VT_CTXC',
            'loadbalancer' => 'VT_CTXB',
            'redirect' => 'VT_CTXR',
            'appserver' => 'VT_CTXAS',
            'module' => 'VT_CTXMD'];

        $this->_options['realmType'] = ['file' => 'Password File'];
    }

    protected function loadCommonAttrs() {
        parent::loadCommonAttrs();
        $param = self::NewTextAreaAttr('param', Msg::aLbl('l_moduleparams'), 'cust', true, 4, 'modParams', 1, 1);
        $param->SetFlag(Attr::BM_RAWDATA);
        $this->_attrs['mod_params'] = $param;
        $this->_attrs['mod_enabled'] = self::newBoolAttr('ls_enabled', Msg::aLbl('l_enablehooks'), true, 'moduleEnabled');
    }

    protected function add_S_PROCESS($id) { //keep
        $attrs = [
            self::newTextAttr('serverName', Msg::aLbl('l_servername'), 'name'),
            self::newIntAttr('httpdWorkers', Msg::aLbl('l_numworkers'), true, 1, 16),
            self::newCustFlagAttr('runningAs', Msg::aLbl('l_runningas'), (Attr::BM_NOFILE | Attr::BM_NOEDIT)),
            self::newCustFlagAttr('user', null, (Attr::BM_HIDE | Attr::BM_NOEDIT), false),
            self::newCustFlagAttr('group', null, (Attr::BM_HIDE | Attr::BM_NOEDIT), false),
            $this->_attrs['priority']->dup(null, null, 'serverPriority'),
            TblDefBase::newIntAttr('cpuAffinity', Msg::aLbl('l_cpuaffinity'), true, 0, 64),
            TblDefBase::NewSelAttr(
                'enableLVE',
                Msg::aLbl('l_enablelve'),
                [0 => Msg::aLbl('o_disabled'), 1 => 'LVE', 2 => 'CageFS', 3 => Msg::aLbl('o_cagefswithoutsuexec')]
            ),
            self::newIntAttr('inMemBufSize', Msg::aLbl('l_inmembufsize'), false, 0),
            self::newTextAttr('swappingDir', Msg::aLbl('l_swappingdir'), 'cust', false),
            self::newBoolAttr('autoFix503', Msg::aLbl('l_autofix503')),
            self::newBoolAttr('enableh2c', Msg::aLbl('l_enableh2c')),
            self::newIntAttr('gracefulRestartTimeout', Msg::aLbl('l_gracefulrestarttimeout'), true, -1, 2592000),
            self::newTextAttr('statDir', Msg::aLbl('l_statDir'), 'cust')
        ];
        $this->_tblDef[$id] = Tbl::NewRegular($id, Msg::aLbl('l_serverprocess'), $attrs);
    }

    protected function add_S_GENERAL($id) { // keep
        $attr_mime = self::NewPathAttr('mime', Msg::aLbl('l_mimesettings'), 'file', 2, 'rw', false);
        $attr_mime->_href = '&t=S_MIME_TOP';

        $attrs = [
            $attr_mime,
            self::newBoolAttr('disableInitLogRotation', Msg::aLbl('l_disableinitlogrotation')),
            self::NewSelAttr(
                'showVersionNumber',
                Msg::aLbl('l_serversig'),
                [
                    '0' => Msg::aLbl('o_hidever'),
                    '1' => Msg::aLbl('o_showver'),
                    '2' => Msg::aLbl('o_hidefullheader'),
                ],
                false
            ),
            $this->_attrs['enableIpGeo'],
            self::NewSelAttr(
                'useIpInProxyHeader',
                Msg::aLbl('l_useipinproxyheader'),
                [
                    '0' => Msg::aLbl('o_no'),
                    '1' => Msg::aLbl('o_yes'),
                    '2' => Msg::aLbl('o_trustediponly'),
                    '3' => Msg::aLbl('o_keepheaderfortrusted'),
                    '4' => Msg::aLbl('o_use_last_ip_for elb'),
                ]
            ),
            $this->_attrs['adminEmails'],
        ];
        $this->_tblDef[$id] = Tbl::NewRegular($id, Msg::aLbl('l_generalsettings'), $attrs);
    }

    protected function add_S_AUTOLOADHTA($id) {
        $attrs = [
            self::newBoolAttr('autoLoadHtaccess', Msg::aLbl('l_autoLoadRewriteHtaccess')),
        ];
        $this->_tblDef[$id] = Tbl::NewRegular($id, Msg::aLbl('l_rewritecontrol'), $attrs);
    }

    protected function add_S_SEC_CGI($id) {
        $attrs = [
            self::newTextAttr('cgidSock', Msg::aLbl('l_cgidsock'), 'addr'),
            self::newIntAttr('maxCGIInstances', Msg::aLbl('l_maxCGIInstances'), true, 1, 2000),
            self::newIntAttr('minUID', Msg::aLbl('l_minuid'), true, 10),
            self::newIntAttr('minGID', Msg::aLbl('l_mingid'), true, 5),
            self::newIntAttr('forceGID', Msg::aLbl('l_forcegid'), true, 0),
            $this->_attrs['cgiUmask'],
            $this->_attrs['priority']->dup(null, Msg::aLbl('l_cgipriority'), 'CGIPriority'),
            self::newIntAttr('CPUSoftLimit', Msg::aLbl('l_cpusoftlimit'), true, 0),
            self::newIntAttr('CPUHardLimit', Msg::aLbl('l_cpuhardlimit'), true, 0),
            $this->_attrs['memSoftLimit'],
            $this->_attrs['memHardLimit'],
            $this->_attrs['procSoftLimit'],
            $this->_attrs['procHardLimit'],
            self::NewSelAttr('cgroups', Msg::aLbl('l_cgroups'), ['0' => Msg::aLbl('o_off'), '1' => Msg::aLbl('o_on'), '2' => Msg::aLbl('o_disabled')]),
        ];

        $this->_tblDef[$id] = Tbl::NewRegular($id, Msg::aLbl('l_cgisettings'), $attrs, 'cgiResource');
    }

    protected function add_VT_REWRITE_CTRL($id) {
        $attrs = [
            self::newBoolAttr('enable', Msg::aLbl('l_enablerewrite'), true, 'enableRewrite'),
            self::newBoolAttr('autoLoadHtaccess', Msg::aLbl('l_autoLoadRewriteHtaccess')),
            self::newIntAttr('logLevel', Msg::aLbl('l_loglevel'), true, 0, 9, 'rewriteLogLevel')
        ];
        $this->_tblDef[$id] = Tbl::NewRegular($id, Msg::aLbl('l_rewritecontrol'), $attrs);
    }

    protected function add_VT_REWRITE_MAP_TOP($id) {
        $align = ['left', 'left', 'center'];
        $name = self::newViewAttr('name', Msg::aLbl('l_name'));
        $location = self::newViewAttr('location', Msg::aLbl('l_location'));
        $action = self::NewActionAttr('VT_REWRITE_MAP', 'Ed');
        $name->cyberpanelBlocked = true;
        $location->cyberpanelBlocked = true;
        $action->cyberpanelBlocked = true;
        $label = Msg::aLbl('l_rewritemap');
        if (CVendor_LiteSpeed_PathTool::isCyberPanel()) {
            $label .= ' (Disabled by CyberPanel)';
        }
        $attrs = [
            $name,
            $location,
            $action
        ];
        $this->_tblDef[$id] = Tbl::newTop($id, $label, $attrs, 'name', 'VT_REWRITE_MAP', $align, null, 'redirect', true);
    }

    protected function add_VT_REWRITE_MAP($id) {
        $parseFormat = "/^((txt|rnd):\/*)|(int:(toupper|tolower|escape|unescape))$/";
        $name = self::newTextAttr('name', Msg::aLbl('l_name'), 'name', false, 'rewriteMapName');
        $location = self::newParseTextAttr('location', Msg::aLbl('l_location'), $parseFormat, Msg::aLbl('parse_rewritemaplocation'), true, 'rewriteMapLocation');
        $note = $this->_attrs['note']->dup(null, null, null);
        $name->cyberpanelBlocked = true;
        $location->cyberpanelBlocked = true;
        $note->cyberpanelBlocked = true;

        $attrs = [
            $name,
            $location,
            $note,
        ];
        $label = Msg::aLbl('l_rewritemap');
        if (CVendor_LiteSpeed_PathTool::isCyberPanel()) {
            $label .= ' (Disabled by CyberPanel)';
        }
        $this->_tblDef[$id] = Tbl::NewIndexed($id, $label, $attrs, 'name');
    }

    protected function add_VT_REWRITE_RULE($id) {
        $rules = self::NewTextAreaAttr('rules', null, 'cust', true, 5, null, 1, 1);
        $rules->cyberpanelBlocked = true;
        $attrs = [
            $rules
        ];
        $label = Msg::aLbl('l_rewriterules');
        if (CVendor_LiteSpeed_PathTool::isCyberPanel()) {
            $label .= ' (Disabled by CyberPanel)';
        }
        $this->_tblDef[$id] = Tbl::NewRegular($id, $label, $attrs, 'rewriteRules', 1);
    }

    protected function add_S_FILEUPLOAD($id) {
        $attrs = [
            self::NewPathAttr('uploadTmpDir', Msg::aLbl('l_uploadtmpdir'), 'path', 2),
            self::newParseTextAttr('uploadTmpFilePermission', Msg::aLbl('l_uploadtmpfilepermission'), $this->_options['parseFormat']['filePermission3'], Msg::aLbl('parse_uploadtmpfilepermission')),
            self::newBoolAttr('uploadPassByPath', Msg::aLbl('l_uploadpassbypath'))
        ];
        $this->_tblDef[$id] = Tbl::NewRegular($id, Msg::aLbl('l_uploadfile'), $attrs, 'fileUpload');
    }

    protected function add_VT_FILEUPLOAD($id) {
        $attrs = [
            self::NewPathAttr('uploadTmpDir', Msg::aLbl('l_uploadtmpdir'), 'path', 3),
            self::newParseTextAttr('uploadTmpFilePermission', Msg::aLbl('l_uploadtmpfilepermission'), $this->_options['parseFormat']['filePermission3'], Msg::aLbl('parse_uploadtmpfilepermission')),
            self::newBoolAttr('uploadPassByPath', Msg::aLbl('l_uploadpassbypath'))
        ];
        $this->_tblDef[$id] = Tbl::NewRegular($id, Msg::aLbl('l_uploadfile'), $attrs, 'fileUpload');
    }

    protected function add_VT_PHPINIOVERRIDE($id) {
        $override = self::NewTextAreaAttr('data', null, 'cust', true, 6, null, 1, 1);
        $override->SetFlag(Attr::BM_RAWDATA);
        $attrs = [
            $override,
        ];
        $this->_tblDef[$id] = Tbl::NewRegular($id, Msg::aLbl('l_phpinioverride'), $attrs, 'phpIniOverride', 1);
    }

    protected function add_S_TUNING_OS($id) {
        //keep
        $attrs = [
            self::newTextAttr('shmDefaultDir', Msg::aLbl('l_shmDefaultDir'), 'cust'),
        ];

        $this->_tblDef[$id] = Tbl::NewRegular($id, Msg::aLbl('l_tuningos'), $attrs);
    }

    protected function add_S_TUNING_STATIC($id) {
        $attrs = [
            self::newIntAttr('maxCachedFileSize', Msg::aLbl('l_maxcachedfilesize'), false, 0, 1048576),
            self::newIntAttr('totalInMemCacheSize', Msg::aLbl('l_totalinmemcachesize'), false, 0),
            self::newIntAttr('maxMMapFileSize', Msg::aLbl('l_maxmmapfilesize'), false, 0),
            self::newIntAttr('totalMMapCacheSize', Msg::aLbl('l_totalmmapcachesize'), false, 0),
            self::newBoolAttr('useSendfile', Msg::aLbl('l_usesendfile')),
            self::newCheckBoxAttr(
                'fileETag',
                Msg::aLbl('l_fileetag'),
                ['4' => 'iNode', '8' => Msg::aLbl('o_modifiedtime'), '16' => Msg::aLbl('o_size'), '0' => Msg::aLbl('o_none')],
                true,
                null,
                28
            ),
        ];

        $this->_tblDef[$id] = Tbl::NewRegular($id, Msg::aLbl('l_tuningstatic'), $attrs);
    }

    protected function add_S_TUNING_SSL($id) {
        $attrs = [
            self::newBoolAttr('sslStrongDhKey', Msg::aLbl('l_sslStrongDhKey')),
            self::newBoolAttr('sslEnableMultiCerts', Msg::aLbl('l_sslEnableMultiCerts')),
            $this->_attrs['sslSessionCache'],
            self::newIntAttr('sslSessionCacheSize', Msg::aLbl('l_sslSessionCacheSize'), true, 512),
            self::newIntAttr('sslSessionCacheTimeout', Msg::aLbl('l_sslSessionCacheTimeout'), true, 10, 1000000),
            $this->_attrs['sslSessionTickets'],
            self::newIntAttr('sslSessionTicketLifetime', Msg::aLbl('l_sslSessionTicketLifetime'), true, 10, 1000000),
            self::newTextAttr('sslSessionTicketKeyFile', Msg::aLbl('l_sslSessionTicketKeyFile'), 'cust')
        ];
        $this->_tblDef[$id] = Tbl::NewRegular($id, Msg::aLbl('l_tuningsslsettings'), $attrs, 'sslGlobal');
    }

    protected function add_S_MOD_TOP($id) {
        $align = ['center', 'center', 'center', 'center'];

        $attrs = [self::newViewAttr('name', Msg::aLbl('l_module')),
            self::newBoolAttr('internal', Msg::aLbl('l_internal'), true, 'internalmodule'),
            $this->_attrs['mod_params'],
            $this->_attrs['mod_enabled'],
            self::NewActionAttr('S_MOD', 'vEd')
        ];
        $this->_tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_servermodulesdef'), $attrs, 'name', 'S_MOD', $align, null, 'module', true);
    }

    protected function getModuleTags() {
        $tags = ['L4_BEGINSESSION', 'L4_ENDSESSION', 'L4_RECVING', 'L4_SENDING',
            'HTTP_BEGIN', 'RECV_REQ_HEADER', 'URI_MAP', 'HTTP_AUTH',
            'RECV_REQ_BODY', 'RCVD_REQ_BODY', 'RECV_RESP_HEADER', 'RECV_RESP_BODY', 'RCVD_RESP_BODY',
            'HANDLER_RESTART', 'SEND_RESP_HEADER', 'SEND_RESP_BODY', 'HTTP_END',
            'MAIN_INITED', 'MAIN_PREFORK', 'MAIN_POSTFORK', 'WORKER_POSTFORK', 'WORKER_ATEXIT', 'MAIN_ATEXIT'];

        return $tags;
    }

    protected function add_S_MOD($id) {
        $attrs = [self::newTextAttr('name', Msg::aLbl('l_module'), 'modulename', false, 'modulename'),
            $this->_attrs['note'],
            self::newBoolAttr('internal', Msg::aLbl('l_internal'), true, 'internalmodule'),
            $this->_attrs['mod_params'],
            $this->_attrs['mod_enabled']];

        $tags = $this->getModuleTags();

        $hook = Msg::aLbl('l_hook');
        $priority = Msg::aLbl('l_priority');
        foreach ($tags as $tag) {
            $attrs[] = self::newIntAttr($tag, "${hook} ${tag} ${priority}", true, -6000, 6000);
        }
        $this->_tblDef[$id] = Tbl::NewIndexed($id, Msg::aLbl('l_servermoduledef'), $attrs, 'name', 'servModules');
    }

    protected function add_VT_MOD_TOP($id) {
        $align = ['center', 'center', 'center', 'center'];

        $attrs = [self::newViewAttr('name', Msg::aLbl('l_module')),
            $this->_attrs['mod_params'],
            $this->_attrs['mod_enabled']->dup(null, null, 'moduleEnabled_vh'),
            self::NewActionAttr('VT_MOD', 'vEd')
        ];
        $this->_tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_moduleconf'), $attrs, 'name', 'VT_MOD', $align, 'vhModules', 'module', true);
    }

    protected function add_VT_MOD($id) {
        $attrs = [self::NewSelAttr('name', Msg::aLbl('l_module'), 'module', false, 'moduleNameSel'),
            $this->_attrs['note'],
            $this->_attrs['mod_params'],
            $this->_attrs['mod_enabled']->dup(null, null, 'moduleEnabled_vh')
        ];

        $this->_tblDef[$id] = Tbl::NewIndexed($id, Msg::aLbl('l_moduleconf'), $attrs, 'name', 'vhModules');
        $this->_tblDef[$id]->set(Tbl::FLD_LINKEDTBL, ['VT_MOD_FILTERTOP']);
    }

    protected function add_VT_MOD_FILTERTOP($id) {
        $align = ['center', 'center', 'center', 'center'];

        $attrs = [self::newViewAttr('uri', Msg::aLbl('l_uri')),
            $this->_attrs['mod_params'],
            self::NewActionAttr('VT_MOD_FILTER', 'vEd')
        ];

        $this->_tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_urlfilter'), $attrs, 'uri', 'VT_MOD_FILTER', $align, 'vhModuleUrlFilters', 'filter', false);
        $this->_tblDef[$id]->set(Tbl::FLD_SHOWPARENTREF, true);
    }

    protected function add_VT_MOD_FILTER($id) {
        $attrs = [$this->_attrs['ctx_uri'],
            $this->_attrs['mod_params'],
        ];

        $this->_tblDef[$id] = Tbl::NewIndexed($id, Msg::aLbl('l_urlfilter'), $attrs, 'uri', 'vhModuleUrlFilters');
        $this->_tblDef[$id]->set(Dtbl::FLD_SHOWPARENTREF, true);
    }

    protected function add_L_MOD_TOP($id) {
        $align = ['center', 'center', 'center', 'center'];

        $attrs = [self::newViewAttr('name', Msg::aLbl('l_module')),
            $this->_attrs['mod_params'],
            $this->_attrs['mod_enabled']->dup(null, null, 'moduleEnabled_lst'),
            self::NewActionAttr('L_MOD', 'vEd')
        ];
        $this->_tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_moduleconf'), $attrs, 'name', 'L_MOD', $align, 'listenerModules', 'module', true);
    }

    protected function add_L_MOD($id) {
        $attrs = [self::NewSelAttr('name', Msg::aLbl('l_module'), 'module', false, 'moduleNameSel'),
            $this->_attrs['note'],
            $this->_attrs['mod_params'],
            $this->_attrs['mod_enabled']->dup(null, null, 'moduleEnabled_lst')
        ];
        $this->_tblDef[$id] = Tbl::NewIndexed($id, Msg::aLbl('l_moduleconf'), $attrs, 'name', 'listenerModules');
    }

    protected function add_V_TOPD($id) {
        $attrs = [
            self::newTextAttr('name', Msg::aLbl('l_vhname'), 'vhname', false, 'vhName'),
            self::newTextAttr('vhRoot', Msg::aLbl('l_vhroot'), 'cust', false), //no validation, maybe suexec owner
            self::NewPathAttr('configFile', Msg::aLbl('l_configfile'), 'filevh', 3, 'rwc', false),
            $this->_attrs['note'],
            $this->_attrs['vh_allowSymbolLink'],
            $this->_attrs['vh_enableScript'],
            $this->_attrs['vh_restrained'],
            $this->_attrs['vh_maxKeepAliveReq'],
            $this->_attrs['vh_setUIDMode'],
            $this->_attrs['vh_suexec_user'],
            $this->_attrs['vh_suexec_group'],
            $this->_attrs['staticReqPerSec'],
            $this->_attrs['dynReqPerSec'],
            $this->_attrs['outBandwidth'],
            $this->_attrs['inBandwidth'],
        ];
        $this->_tblDef[$id] = Tbl::NewIndexed($id, Msg::aLbl('l_vhost'), $attrs, 'name');
    }

    protected function add_V_BASE_SEC($id) {
        $attrs = [
            $this->_attrs['vh_allowSymbolLink'],
            $this->_attrs['vh_enableScript'],
            $this->_attrs['vh_restrained'],
            $this->_attrs['vh_setUIDMode'],
            $this->_attrs['vh_suexec_user'],
            $this->_attrs['vh_suexec_group'],
        ];
        $this->_tblDef[$id] = Tbl::NewIndexed($id, Msg::UIStr('tab_sec'), $attrs, 'name');
    }

    protected function add_V_GENERAL($id) {
        $attrs = [
            self::newTextAttr('docRoot', Msg::aLbl('l_docroot'), 'cust', false), //no validation, maybe suexec owner
            $this->_attrs['tp_vhDomain'], // this setting is a new way, will merge with listener map settings for backward compatible
            $this->_attrs['tp_vhAliases'],
            $this->_attrs['adminEmails']->dup(null, null, 'vhadminEmails'),
            $this->_attrs['vh_enableGzip'],
            $this->_attrs['enableIpGeo'],
            self::NewSelAttr('cgroups', Msg::aLbl('l_cgroups'), ['0' => Msg::aLbl('o_off'), '1' => Msg::aLbl('o_on')]),
        ];
        $this->_tblDef[$id] = Tbl::NewRegular($id, Msg::UIStr('tab_g'), $attrs);
    }

    protected function add_T_GENERAL2($id) {
        $attrs = [
            $this->_attrs['tp_vrFile']->dup('docRoot', Msg::aLbl('l_docroot'), 'templateVHDocRoot'),
            $this->_attrs['adminEmails']->dup(null, null, 'vhadminEmails'),
            $this->_attrs['vh_enableGzip'],
            $this->_attrs['enableIpGeo'],
            self::NewSelAttr('cgroups', Msg::aLbl('l_cgroups'), ['0' => Msg::aLbl('o_off'), '1' => Msg::aLbl('o_on')]),
        ];
        $this->_tblDef[$id] = Tbl::NewRegular($id, Msg::aLbl('l_base2'), $attrs);
    }

    protected function add_V_REALM_TOP($id) {
        $align = ['center', 'center', 'center'];
        $realm_attr = $this->get_realm_attrs();

        $attrs = [
            $realm_attr['realm_name'],
            self::newViewAttr('userDB:location', Msg::aLbl('l_userdblocation'), 'userDBLocation'),
            self::NewActionAttr('V_REALM_FILE', 'vEd')
        ];
        $this->_tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_realmlist'), $attrs, 'name', 'V_REALM_FILE', $align, 'realms', 'shield', true);
    }

    protected function add_T_REALM_TOP($id) {
        $align = ['center', 'center', 'center'];
        $realm_attr = $this->get_realm_attrs();

        $attrs = [
            $realm_attr['realm_name'],
            self::newViewAttr('userDB:location', Msg::aLbl('l_userdblocation'), 'userDBLocation'),
            self::NewActionAttr('T_REALM_FILE', 'vEd')
        ];
        $this->_tblDef[$id] = Tbl::newTop($id, Msg::aLbl('l_realmlist'), $attrs, 'name', 'T_REALM_FILE', $align, 'realms', 'shield', true);
    }

    protected function add_VT_CTX_TOP($id) {
        $align = ['center', 'left', 'center', 'center'];

        $attrs = [
            $this->_attrs['ctx_type'],
            self::newViewAttr('uri', Msg::aLbl('l_uri')),
            self::newBoolAttr('allowBrowse', Msg::aLbl('l_allowbrowse'), false),
            self::newCustFlagAttr('order', Msg::aLbl('l_order'), (Attr::BM_NOFILE | Attr::BM_NOEDIT), true, 'ctxseq'),
            self::NewActionAttr($this->_options['ctxTbl'], 'vEd')
        ];
        $this->_tblDef[$id] = Tbl::newTop(
            $id,
            Msg::aLbl('l_contextlist'),
            $attrs,
            'uri',
            'VT_CTX_SEL',
            $align,
            null,
            ['null' => 'file', 'proxy' => 'network', 'redirect' => 'redirect', 'module' => 'module'],
            true
        );
    }

    protected function add_VT_CTXG($id) {
        $override = self::NewTextAreaAttr('phpIniOverride:data', Msg::aLbl('l_phpinioverride'), 'cust', true, 6, 'phpIniOverride', 1, 1);
        $override->SetFlag(Attr::BM_RAWDATA);

        $attrs = array_merge(
            $this->get_ctx_attrs('uri'),
            [
                $this->_attrs['ctx_location'],
                self::newBoolAttr('allowBrowse', Msg::aLbl('l_allowbrowse'), false),
                $this->_attrs['note']],
            $this->get_expires_attrs(),
            [
                $this->_attrs['extraHeaders'],
                self::NewParseTextAreaAttr(
                    'addMIMEType',
                    Msg::aLbl('l_mimetype'),
                    "/[A-z0-9_\-\.\+]+\/[A-z0-9_\-\.\+]+(\s+[A-z0-9_\-\+]+)+/",
                    Msg::aLbl('parse_mimetype'),
                    true,
                    2,
                    null,
                    0,
                    0,
                    1
                ),
                self::newParseTextAttr(
                    'forceType',
                    Msg::aLbl('l_forcemimetype'),
                    "/^([A-z0-9_\-\.\+]+\/[A-z0-9_\-\.\+]+)|(NONE)$/i",
                    Msg::aLbl('parse_forcemimetype')
                ),
                self::newParseTextAttr(
                    'defaultType',
                    Msg::aLbl('l_defaultmimetype'),
                    "/^[A-z0-9_\-\.\+]+\/[A-z0-9_\-\.\+]+$/",
                    Msg::aLbl('parse_defaultmimetype')
                ),
                $this->_attrs['indexFiles'],
                $this->_attrs['autoIndex']],
            $this->get_ctx_attrs('auth'),
            $this->get_ctx_attrs('rewrite'),
            $this->get_ctx_attrs('charset'),
            [
                $override,
            ]
        );
        $defaultExtract = ['type' => 'null'];
        $this->_tblDef[$id] = Tbl::NewIndexed($id, Msg::aLbl('l_ctxg'), $attrs, 'uri', 'generalContext', $defaultExtract);
    }

    protected function add_VT_CTXJ($id) {
        $attrs = array_merge(
            [
                self::newTextAttr('uri', Msg::aLbl('l_uri'), 'uri', false),
                $this->_attrs['ctx_order'],
                $this->_attrs['ctx_location']->dup(null, null, 'javaWebApp_location'),
                $this->_attrs['ctx_shandler'],
                $this->_attrs['note']],
            $this->get_expires_attrs(),
            [
                $this->_attrs['extraHeaders'],
                $this->_attrs['indexFiles'],
                $this->_attrs['autoIndex']],
            $this->get_ctx_attrs('auth'),
            $this->get_ctx_attrs('charset')
        );
        $defaultExtract = ['type' => 'webapp'];
        $this->_tblDef[$id] = Tbl::NewIndexed($id, Msg::aLbl('l_ctxj'), $attrs, 'uri', 'javaWebAppContext', $defaultExtract);
    }

    protected function add_VT_CTXAS($id) {
        $attrs = array_merge(
            $this->get_ctx_attrs('uri'),
            [
                self::newTextAttr('location', Msg::aLbl('l_location'), 'cust', false, 'as_location'),
                self::NewPathAttr('binPath', Msg::aLbl('l_binpath'), 'file', 1, 'x'),
                self::NewSelAttr(
                    'appType',
                    Msg::aLbl('l_apptype'),
                    ['' => '', 'rails' => 'Rails', 'wsgi' => 'WSGI', 'node' => 'Node']
                ),
                self::newTextAttr('startupFile', Msg::aLbl('l_startupfile'), 'cust', true, 'as_startupfile'),
                $this->_attrs['note'],
                $this->_attrs['appserverEnv'],
                self::newIntAttr('maxConns', Msg::aLbl('l_maxconns'), true, 1, 2000),
                $this->_attrs['ext_env']],
            $this->get_expires_attrs(),
            [
                $this->_attrs['extraHeaders'],
                $this->_attrs['indexFiles'],
                $this->_attrs['autoIndex']],
            $this->get_ctx_attrs('auth'),
            $this->get_ctx_attrs('rewrite'),
            $this->get_ctx_attrs('charset')
        );
        $defaultExtract = ['type' => 'appserver'];
        $this->_tblDef[$id] = Tbl::NewIndexed($id, Msg::aLbl('l_ctxas'), $attrs, 'uri', 'appserverContext', $defaultExtract);
    }

    protected function add_VT_CTXS($id) {
        $attrs = array_merge(
            $this->get_ctx_attrs('uri'),
            [
                $this->_attrs['ctx_shandler'],
                $this->_attrs['note'],
                $this->_attrs['extraHeaders']],
            $this->get_ctx_attrs('auth'),
            $this->get_ctx_attrs('charset')
        );
        $defaultExtract = ['type' => 'servlet'];
        $this->_tblDef[$id] = Tbl::NewIndexed($id, Msg::aLbl('l_ctxs'), $attrs, 'uri', 'servletContext', $defaultExtract);
    }

    protected function add_VT_CTXF($id) {
        $attrs = array_merge(
            $this->get_ctx_attrs('uri'),
            [
                self::NewSelAttr('handler', Msg::aLbl('l_fcgiapp'), 'extprocessor:fcgi', false, 'fcgiapp'),
                $this->_attrs['note'],
                $this->_attrs['extraHeaders']],
            $this->get_ctx_attrs('auth'),
            $this->get_ctx_attrs('charset')
        );
        $defaultExtract = ['type' => 'fcgi'];
        $this->_tblDef[$id] = Tbl::NewIndexed($id, Msg::aLbl('l_ctxf'), $attrs, 'uri', 'fcgiContext', $defaultExtract);
    }

    protected function add_VT_CTXL($id) {
        $attrs = array_merge(
            $this->get_ctx_attrs('uri'),
            [
                self::NewSelAttr('handler', Msg::aLbl('l_lsapiapp'), 'extprocessor:lsapi', false, 'lsapiapp'),
                $this->_attrs['note'],
                $this->_attrs['extraHeaders']],
            $this->get_ctx_attrs('auth'),
            $this->get_ctx_attrs('charset')
        );
        $defaultExtract = ['type' => 'lsapi'];
        $this->_tblDef[$id] = Tbl::NewIndexed($id, Msg::aLbl('l_ctxl'), $attrs, 'uri', 'lsapiContext', $defaultExtract);
    }

    protected function add_VT_CTXMD($id) {
        $attrs = array_merge(
            $this->get_ctx_attrs('uri'),
            [
                self::NewSelAttr('handler', Msg::aLbl('l_modulehandler'), 'module', false, 'moduleNameSel'),
                $this->_attrs['note'],
                $this->_attrs['mod_params'],
                $this->_attrs['extraHeaders']],
            $this->get_ctx_attrs('auth'),
            $this->get_ctx_attrs('charset')
        );
        $defaultExtract = ['type' => 'module'];
        $this->_tblDef[$id] = Tbl::NewIndexed($id, Msg::aLbl('l_ctxmd'), $attrs, 'uri', 'lmodContext', $defaultExtract);
    }

    protected function add_VT_CTXB($id) {
        $attrs = array_merge(
            $this->get_ctx_attrs('uri'),
            [
                self::NewSelAttr('handler', Msg::aLbl('l_loadbalancer'), 'extprocessor:loadbalancer', false, 'lbapp'),
                $this->_attrs['note'],
                $this->_attrs['extraHeaders']],
            $this->get_ctx_attrs('auth'),
            $this->get_ctx_attrs('charset')
        );
        $defaultExtract = ['type' => 'loadbalancer'];
        $this->_tblDef[$id] = Tbl::NewIndexed($id, Msg::aLbl('l_ctxb'), $attrs, 'uri', 'lbContext', $defaultExtract);
    }

    protected function add_VT_CTXP($id) {
        $attrs = array_merge(
            $this->get_ctx_attrs('uri'),
            [
                self::NewSelAttr('handler', Msg::aLbl('l_webserver'), 'extprocessor:proxy', false, 'proxyWebServer'),
                $this->_attrs['note'],
                $this->_attrs['extraHeaders']],
            $this->get_ctx_attrs('auth'),
            $this->get_ctx_attrs('charset')
        );
        $defaultExtract = ['type' => 'proxy'];
        $this->_tblDef[$id] = Tbl::NewIndexed($id, Msg::aLbl('l_ctxp'), $attrs, 'uri', 'proxyContext', $defaultExtract);
    }

    protected function add_VT_CTXC($id) {
        $attrs = array_merge(
            $this->get_ctx_attrs('uri'),
            [
                $this->_attrs['ctx_location']->dup(null, Msg::aLbl('l_path'), 'cgi_path'),
                $this->_attrs['note'],
                $this->_attrs['extraHeaders'],
                self::newBoolAttr('allowSetUID', Msg::aLbl('l_allowsetuid'))],
            $this->get_ctx_attrs('auth'),
            $this->get_ctx_attrs('rewrite'),
            $this->get_ctx_attrs('charset')
        );
        $defaultExtract = ['type' => 'cgi'];
        $this->_tblDef[$id] = Tbl::NewIndexed($id, Msg::aLbl('l_ctxc'), $attrs, 'uri', 'cgiContext', $defaultExtract);
    }

    protected function add_VT_CTXR($id) {
        $options = $this->get_cust_status_code();

        $attrs = array_merge(
            $this->get_ctx_attrs('uri'),
            [
                self::newBoolAttr('externalRedirect', Msg::aLbl('l_externalredirect'), false, 'externalredirect'),
                self::NewSelAttr('statusCode', Msg::aLbl('l_statuscode'), $options, true, 'statuscode'),
                self::newTextAttr('location', Msg::aLbl('l_desturi'), 'url', true, 'destinationuri'),
                $this->_attrs['note'],
                $this->_attrs['extraHeaders']],
            $this->get_ctx_attrs('auth')
        );
        $defaultExtract = ['type' => 'redirect'];
        $this->_tblDef[$id] = Tbl::NewIndexed($id, Msg::aLbl('l_ctxr'), $attrs, 'uri', 'redirectContext', $defaultExtract);
    }

    protected function add_T_SEC_CGI($id) {
        $attrs = [
            $this->_attrs['vh_setUIDMode'],
            $this->_attrs['vh_suexec_user'],
            $this->_attrs['vh_suexec_group'],
        ];
        $this->_tblDef[$id] = Tbl::NewRegular($id, Msg::aLbl('l_extappsec'), $attrs);
    }

    protected function add_L_GENERAL($id) {
        $ip = self::NewSelAttr('ip', Msg::aLbl('l_ip'), $this->_options['ip'], false, 'listenerIP');
        $ip->SetFlag(Attr::BM_NOFILE);
        $port = self::newIntAttr('port', Msg::aLbl('l_port'), false, 0, 65535, 'listenerPort');
        $port->SetFlag(Attr::BM_NOFILE);

        $processes = isset($_SERVER['LSWS_CHILDREN']) ? $_SERVER['LSWS_CHILDREN'] : 1;
        for ($i = 1; $i <= $processes; ++$i) {
            $bindoptions[1 << ($i - 1)] = "Process ${i}";
        }

        $attrs = [
            self::newTextAttr('name', Msg::aLbl('l_listenername'), 'name', false, 'listenerName'),
            self::newCustFlagAttr('address', Msg::aLbl('l_address'), (Attr::BM_HIDE | Attr::BM_NOEDIT), false),
            $ip, $port,
            self::newCheckBoxAttr('binding', Msg::aLbl('l_binding'), $bindoptions, true, 'listenerBinding'),
            self::newBoolAttr('reusePort', Msg::aLbl('l_reuseport')),
            self::newBoolAttr('secure', Msg::aLbl('l_secure'), false, 'listenerSecure'),
            $this->_attrs['note'],
        ];
        $this->_tblDef[$id] = Tbl::NewIndexed($id, Msg::aLbl('l_addresssettings'), $attrs, 'name');
    }

    protected function add_LVT_SSL_CLVERIFY($id) {
        $attrs = [
            self::NewSelAttr(
                'clientVerify',
                Msg::aLbl('l_clientverify'),
                ['0' => 'none', '1' => 'optional', '2' => 'require', '3' => 'optional_no_ca']
            ),
            self::newIntAttr('verifyDepth', Msg::aLbl('l_verifydepth'), true, 0, 100),
            self::newTextAttr('crlPath', Msg::aLbl('l_crlpath'), 'cust'),
            self::newTextAttr('crlFile', Msg::aLbl('l_crlfile'), 'cust'),
        ];
        $this->_tblDef[$id] = Tbl::NewRegular($id, Msg::aLbl('l_clientverify'), $attrs);
    }
}

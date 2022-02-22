<?php
use CVendor_LiteSpeed_Msg as Msg;
use CVendor_LiteSpeed_Info as Info;
use CVendor_LiteSpeed_Page as Page;
use CVendor_LiteSpeed_TblMap as TblMap;

class CVendor_LiteSpeed_OWS_PageDef {
    protected $pageDef = [];

    protected $fileDef = [];

    private static $instance;

    /**
     * Get singleton instance.
     *
     * @return CVendor_LiteSpeed_OWS_PageDef
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    protected function __construct() {
        $this->defineAll();
    }

    public static function getPage($dinfo) {
        $pagedef = static::instance();
        $type = $dinfo->get(Info::FLD_VIEW);
        $pid = $dinfo->Get(Info::FLD_PID);

        return $pagedef->pageDef[$type][$pid];
    }

    public function getFileMap($type) {
        if (!isset($this->fileDef[$type])) {
            $funcname = 'add_FileMap_' . $type;
            if (!method_exists($this, $funcname)) {
                die("invalid func name ${funcname}");
            }
            $this->$funcname();
        }

        return $this->fileDef[$type];
    }

    private function add_FileMap_serv() {
        $map = new TblMap(
            ['httpServerConfig', ''],
            ['S_PROCESS',
                'S_GENERAL',
                new TblMap(['logging:log', 'errorlog$fileName'], 'S_LOG'),
                new TblMap(['logging:*accessLog', '*accesslog$fileName'], 'S_ACLOG'),
                'S_INDEX',
                new TblMap('expires', 'A_EXPIRES'),
                'S_AUTOLOADHTA', 'S_FILEUPLOAD',
                new TblMap(['ipToGeo:geoipDB', '*geoipdb$geoipDBFile'], 'S_GEOIP'),
                new TblMap('ip2locDB', 'S_IP2LOCATION'),
                new TblMap('tuning', ['S_TUNING_OS', 'S_TUNING_CONN', 'S_TUNING_REQ', 'S_TUNING_STATIC', 'S_TUNING_GZIP', 'S_TUNING_SSL', 'S_TUNING_QUIC']),
                new TblMap(['security:fileAccessControl', 'fileAccessControl'], 'S_SEC_FILE'),
                new TblMap(['security:perClientConnLimit', 'perClientConnLimit'], 'S_SEC_CONN'),
                new TblMap(['security:CGIRLimit', 'CGIRLimit'], 'S_SEC_CGI'),
                new TblMap(['security', ''], 'S_SEC_BUBBLEWRAP'),
                new TblMap(['security:accessDenyDir', 'accessDenyDir'], 'S_SEC_DENY'),
                new TblMap(['security:accessControl', 'accessControl'], 'A_SEC_AC'),
                new TblMap('lsrecaptcha', 'S_SEC_RECAP'),
                new TblMap(['extProcessorList:*extProcessor', '*extprocessor$name'], 'A_EXT_SEL'),
                new TblMap(['scriptHandlerList', 'scripthandler'], new TblMap(['*scriptHandler', '*addsuffix$suffix'], 'A_SCRIPT')),
                new TblMap('railsDefaults', 'S_RAILS'),
                new TblMap('wsgiDefaults', 'S_WSGI'),
                new TblMap('nodeDefaults', 'S_NODEJS'),
                new TblMap(['moduleList:*module', '*module$name'], 'S_MOD'),
                new TblMap(['virtualHostList:*virtualHost', '*virtualhost$name'], 'V_TOPD'),
                new TblMap(['listenerList:*listener', '*listener$name'], ['L_GENERAL',
                    new TblMap(['vhostMapList:*vhostMap', '*vhmap$vhost'], 'L_VHMAP'),
                    'LVT_SSL_CERT', 'LVT_SSL', 'L_SSL_FEATURE', 'LVT_SSL_OCSP', 'LVT_SSL_CLVERIFY',
                    new TblMap(['moduleList:*module', '*module$name'], 'L_MOD')]),
                new TblMap(['vhTemplateList:*vhTemplate', '*vhTemplate$name'], ['T_TOPD', new TblMap(['*member', '*member$vhName'], 'T_MEMBER')]),
                'SERVICE_SUSPENDVH']
        );

        $this->fileDef['serv'] = $map;
    }

    private function add_FileMap_vh_() {
        $map = new TblMap(
            ['virtualHostConfig', ''],
            [
                'V_GENERAL',
                new TblMap(['logging:log', 'errorlog$fileName'], 'V_LOG'),
                new TblMap(['logging:*accessLog', '*accesslog$fileName'], 'V_ACLOG'),
                new TblMap('index', 'VT_INDXF'),
                new TblMap(['customErrorPages:errorPage', '*errorpage$errCode'], 'VT_ERRPG'),
                new TblMap(
                    ['scriptHandlerList', 'scripthandler'],
                    new TblMap(['*scriptHandler', '*addsuffix$suffix'], 'A_SCRIPT')
                ),
                new TblMap('expires', 'A_EXPIRES'),
                'VT_FILEUPLOAD',
                new TblMap('phpIniOverride', 'VT_PHPINIOVERRIDE'),
                new TblMap(['security:accessControl', 'accessControl'], 'A_SEC_AC'),
                new TblMap(['security:realmList:*realm', '*realm$name'], 'V_REALM_FILE'),
                new TblMap('lsrecaptcha', 'VT_SEC_RECAP'),
                new TblMap(['security', ''], 'VT_SEC_BUBBLEWRAP'),
                new TblMap(['extProcessorList:*extProcessor', '*extprocessor$name'], 'A_EXT_SEL'),
                new TblMap(['contextList:*context', '*context$uri'], 'VT_CTX_SEL'),
                new TblMap(
                    'rewrite',
                    ['VT_REWRITE_CTRL',
                        new TblMap(['*map', '*map$name'], 'VT_REWRITE_MAP'),
                        'VT_REWRITE_RULE']
                ),
                new TblMap(
                    'vhssl',
                    ['LVT_SSL_CERT', 'LVT_SSL', 'VT_SSL_FEATURE', 'LVT_SSL_OCSP', 'LVT_SSL_CLVERIFY']
                ),
                new TblMap(['websocketList:*websocket', '*websocket$uri'], 'VT_WBSOCK'),
                new TblMap(
                    ['moduleList:*module', '*module$name'],
                    ['VT_MOD',
                        new TblMap(['urlFilterList:*urlFilter', '*urlFilter$uri'], 'VT_MOD_FILTER')]
                )
            ]
        );

        $this->fileDef['vh_'] = $map;
    }

    private function add_FileMap_tp_() {
        $map = new TblMap(
            ['virtualHostTemplate', ''],
            [
                'T_GENERAL1',
                'T_SEC_FILE',
                'T_SEC_CONN',
                'T_SEC_CGI',
                new TblMap(
                    'virtualHostConfig',
                    [
                        'T_GENERAL2',
                        new TblMap(['logging:log', 'errorlog$fileName'], 'T_LOG'),
                        new TblMap(['logging:*accessLog', '*accesslog$fileName'], 'T_ACLOG'),
                        new TblMap('index', 'VT_INDXF'),
                        new TblMap(['customErrorPages:errorPage', '*errorpage$errCode'], 'VT_ERRPG'),
                        new TblMap(
                            ['scriptHandlerList', 'scripthandler'],
                            new TblMap(['*scriptHandler', '*addsuffix$suffix'], 'A_SCRIPT')
                        ),
                        'VT_FILEUPLOAD',
                        new TblMap('phpIniOverride', 'VT_PHPINIOVERRIDE'),
                        new TblMap('expires', 'A_EXPIRES'),
                        new TblMap(['security:accessControl', 'accessControl'], 'A_SEC_AC'),
                        new TblMap(['security:realmList:*realm', '*realm$name'], 'T_REALM_FILE'),
                        new TblMap('lsrecaptcha', 'VT_SEC_RECAP'),
                        new TblMap(['security', ''], 'VT_SEC_BUBBLEWRAP'),
                        new TblMap(['extProcessorList:*extProcessor', '*extprocessor$name'], 'T_EXT_SEL'),
                        new TblMap(['contextList:*context', '*context$uri'], 'VT_CTX_SEL'),
                        new TblMap(
                            'rewrite',
                            ['VT_REWRITE_CTRL',
                                new TblMap(['*map', '*map$name'], 'VT_REWRITE_MAP'),
                                'VT_REWRITE_RULE']
                        ),
                        new TblMap(
                            'vhssl',
                            ['LVT_SSL_CERT', 'LVT_SSL', 'VT_SSL_FEATURE', 'LVT_SSL_OCSP', 'LVT_SSL_CLVERIFY']
                        ),
                        new TblMap(['websocketList:*websocket', '*websocket$uri'], 'VT_WBSOCK'),
                        new TblMap(
                            ['moduleList:*module', '*module$name'],
                            ['VT_MOD',
                                new TblMap(['urlFilterList:*urlFilter', '*urlFilter$uri'], 'VT_MOD_FILTER')]
                        )
                    ]
                ),
            ]
        );

        $this->fileDef['tp_'] = $map;
    }

    private function add_FileMap_admin() {
        $map = new TblMap(
            ['adminConfig', ''],
            ['ADM_PHP',
                new TblMap(['logging:log', 'errorlog$fileName'], 'V_LOG'),
                new TblMap(['logging:accessLog', 'accesslog$fileName'], 'ADM_ACLOG'),
                new TblMap(['security:accessControl', 'accessControl'], 'A_SEC_AC'),
                new TblMap(
                    ['listenerList:*listener', '*listener$name'],
                    ['ADM_L_GENERAL', 'LVT_SSL_CERT', 'LVT_SSL', 'L_SSL_FEATURE', 'LVT_SSL_CLVERIFY']
                )
            ]
        );

        $this->fileDef['admin'] = $map;
    }

    public function getTabDef($view) {
        if (!isset($this->pageDef[$view])) {
            die("Invalid tabs ${view}");
        }

        $tabs = [];
        foreach ($this->pageDef[$view] as $p) {
            $tabs[$p->GetID()] = $p->GetLabel();
        }

        return $tabs;
    }

    protected function defineAll() {
        $id = 'g';
        $page = new Page(
            $id,
            Msg::UIStr('tab_g'),
            new TblMap(
                '',
                ['S_PROCESS', 'S_GENERAL', 'S_INDEX',
                    new TblMap('expires', 'A_EXPIRES'),
                    'S_AUTOLOADHTA', 'S_FILEUPLOAD',
                    new TblMap('*geoipdb$geoipDBFile', 'S_GEOIP_TOP', 'S_GEOIP'),
                    new TblMap('ip2locDB', 'S_IP2LOCATION'),
                ],
                new TblMap('*index', ['S_MIME_TOP', 'S_MIME'])
            )
        );
        $this->pageDef['serv'][$id] = $page;

        $id = 'log';
        $page = new Page(
            $id,
            Msg::UIStr('tab_log'),
            new TblMap(
                '',
                [
                    new TblMap('errorlog$fileName', 'S_LOG'),
                    new TblMap('*accesslog$fileName', 'S_ACLOG_TOP', 'S_ACLOG'),
                ]
            )
        );
        $this->pageDef['serv'][$id] = $page;

        $id = 'tuning';
        $page = new Page($id, Msg::UIStr('tab_tuning'), new TblMap(
            'tuning',
            ['S_TUNING_OS', 'S_TUNING_CONN', 'S_TUNING_REQ', 'S_TUNING_STATIC', 'S_TUNING_GZIP', 'S_TUNING_SSL', 'S_TUNING_QUIC']
        ));
        $this->pageDef['serv'][$id] = $page;

        $id = 'sec';
        $page = new Page($id, Msg::UIStr('tab_sec'), new TblMap(
            '',
            [new TblMap('fileAccessControl', 'S_SEC_FILE'),
                new TblMap('perClientConnLimit', 'S_SEC_CONN'),
                new TblMap('CGIRLimit', 'S_SEC_CGI'),
                new TblMap('lsrecaptcha', 'S_SEC_RECAP'),
                'S_SEC_BUBBLEWRAP',
                new TblMap('accessDenyDir', 'S_SEC_DENY'),
                new TblMap('accessControl', 'A_SEC_AC')]
        ));
        $this->pageDef['serv'][$id] = $page;

        $id = 'ext';
        $page = new Page(
            $id,
            Msg::UIStr('tab_ext'),
            new TblMap(
                '*extprocessor$name',
                'A_EXT_TOP',
                ['A_EXT_SEL', 'A_EXT_FCGI', 'A_EXT_FCGIAUTH', 'A_EXT_LSAPI', 'A_EXT_SERVLET', 'A_EXT_PROXY', 'A_EXT_LOGGER', 'A_EXT_LOADBALANCER']
            )
        );
        $this->pageDef['serv'][$id] = $page;

        $id = 'sh';
        $page = new Page($id, Msg::UIStr('tab_sh'), new TblMap('scripthandler:*addsuffix$suffix', 'A_SCRIPT_TOP', 'A_SCRIPT'));
        $this->pageDef['serv'][$id] = $page;

        $id = 'appserver';
        $page = new Page($id, Msg::UIStr('tab_rails'), new TblMap(
            '',
            [new TblMap('railsDefaults', 'S_RAILS'),
                new TblMap('wsgiDefaults', 'S_WSGI'),
                new TblMap('nodeDefaults', 'S_NODEJS')]
        ));
        $this->pageDef['serv'][$id] = $page;

        $id = 'mod';
        $page = new Page($id, Msg::UIStr('tab_mod'), new TblMap('*module$name', 'S_MOD_TOP', 'S_MOD'));
        $this->pageDef['serv'][$id] = $page;

        $id = 'top';
        $page = new Page($id, Msg::UIStr('tab_top'), new TblMap('*listener$name', 'L_TOP', 'L_GENERAL'));
        $this->pageDef['sl'][$id] = $page;

        $id = 'lg';
        $page = new Page($id, Msg::UIStr('tab_g'), new TblMap(
            '*listener$name',
            ['L_GENERAL', new TblMap('*vhmap$vhost', 'L_VHMAP_TOP', 'L_VHMAP')]
        ));
        $this->pageDef['sl_'][$id] = $page;

        $id = 'lsec';
        $page = new Page($id, Msg::UIStr('tab_ssl'), new TblMap(
            '*listener$name',
            ['LVT_SSL_CERT', 'LVT_SSL', 'L_SSL_FEATURE', 'LVT_SSL_OCSP', 'LVT_SSL_CLVERIFY']
        ));
        $this->pageDef['sl_'][$id] = $page;

        $id = 'lmod';
        $page = new Page($id, Msg::UIStr('tab_mod'), new TblMap('*listener$name', new TblMap('*module$name', 'L_MOD_TOP', 'L_MOD')));
        $this->pageDef['sl_'][$id] = $page;

        $id = 'top';
        $page = new Page($id, Msg::UIStr('tab_top'), new TblMap('*virtualhost$name', 'V_TOP', 'V_TOPD'));
        $this->pageDef['vh'][$id] = $page;

        //$id = 'top';
        $page = new Page($id, Msg::UIStr('tab_top'), new TblMap('*vhTemplate$name', 'T_TOP', 'T_TOPD'));
        $this->pageDef['tp'][$id] = $page;

        $id = 'mbr';
        $page = new Page($id, Msg::UIStr('tab_tp'), new TblMap(
            '*vhTemplate$name',
            ['T_TOPD', new TblMap('*member$vhName', 'T_MEMBER_TOP', 'T_MEMBER')]
        ));
        $this->pageDef['tp_'][$id] = $page;

        $id = 'base';
        $page = new Page($id, Msg::UIStr('tab_base'), new TblMap(
            '*virtualhost$name',
            ['V_BASE', 'V_BASE_CONN', 'V_BASE_SEC', 'V_BASE_THROTTLE']
        ));
        $this->pageDef['vh_'][$id] = $page;

        $id = 'g';
        $page = new Page($id, Msg::UIStr('tab_g'), new TblMap(
            '',
            [
                'V_GENERAL',
                new TblMap('index', 'VT_INDXF'),
                new TblMap('*errorpage$errCode', 'VT_ERRPG_TOP', 'VT_ERRPG'),
                new TblMap('expires', 'A_EXPIRES'),
                'VT_FILEUPLOAD',
                new TblMap('phpIniOverride', 'VT_PHPINIOVERRIDE'),
            ]
        ));

        $this->pageDef['vh_'][$id] = $page;

        $page = new Page($id, Msg::UIStr('tab_g'), new TblMap(
            '',
            ['T_GENERAL1', new TblMap(
                'virtualHostConfig',
                [
                    'T_GENERAL2',
                    new TblMap('index', 'VT_INDXF'),
                    new TblMap('*errorpage$errCode', 'VT_ERRPG_TOP', 'VT_ERRPG'),
                    new TblMap('scripthandler', new TblMap(['*scriptHandler', '*addsuffix$suffix'], 'A_SCRIPT')),
                    new TblMap('expires', 'A_EXPIRES'),
                    'VT_FILEUPLOAD',
                    new TblMap('phpIniOverride', 'VT_PHPINIOVERRIDE'),
                ]
            )]
        ));
        $this->pageDef['tp_'][$id] = $page;

        $id = 'log';
        $page = new Page($id, Msg::UIStr('tab_log'), new TblMap(
            '',
            [
                new TblMap('errorlog$fileName', 'V_LOG'),
                new TblMap('*accesslog$fileName', 'V_ACLOG_TOP', 'V_ACLOG'),
            ]
        ));
        $this->pageDef['vh_'][$id] = $page;

        $page = new Page($id, Msg::UIStr('tab_log'), new TblMap(
            'virtualHostConfig',
            [new TblMap('errorlog$fileName', 'T_LOG'),
                new TblMap('*accesslog$fileName', 'T_ACLOG_TOP', 'T_ACLOG')]
        ));
        $this->pageDef['tp_'][$id] = $page;

        $id = 'sec';
        $page = new Page($id, Msg::UIStr('tab_sec'), new TblMap(
            '',
            [new TblMap('lsrecaptcha', 'VT_SEC_RECAP'),
                'VT_SEC_BUBBLEWRAP',
                new TblMap('accessControl', 'A_SEC_AC'),
                new TblMap('*realm$name', 'V_REALM_TOP', 'V_REALM_FILE')],
            new TblMap('*index', ['V_UDB_TOP', 'V_UDB', 'V_GDB_TOP', 'V_GDB'])
        ));
        $this->pageDef['vh_'][$id] = $page;

        $page = new Page($id, Msg::UIStr('tab_sec'), new TblMap(
            '',
            ['T_SEC_FILE', 'T_SEC_CONN', 'T_SEC_CGI',
                new TblMap(
                    'virtualHostConfig',
                    [new TblMap('lsrecaptcha', 'VT_SEC_RECAP'),
                        'VT_SEC_BUBBLEWRAP',
                        new TblMap('accessControl', 'A_SEC_AC'),
                        new TblMap('*realm$name', 'T_REALM_TOP', 'T_REALM_FILE')]
                )]
        ));
        $this->pageDef['tp_'][$id] = $page;

        $id = 'ext';
        $page = new Page(
            $id,
            Msg::UIStr('tab_ext'),
            new TblMap(
                '*extprocessor$name',
                'A_EXT_TOP',
                ['A_EXT_SEL', 'A_EXT_FCGI', 'A_EXT_FCGIAUTH', 'A_EXT_LSAPI', 'A_EXT_SERVLET', 'A_EXT_PROXY', 'A_EXT_LOGGER', 'A_EXT_LOADBALANCER']
            )
        );
        $this->pageDef['vh_'][$id] = $page;

        $page = new Page(
            $id,
            Msg::UIStr('tab_ext'),
            new TblMap(
                'virtualHostConfig:*extprocessor$name',
                'T_EXT_TOP',
                ['T_EXT_SEL', 'T_EXT_FCGI', 'T_EXT_FCGIAUTH', 'T_EXT_LSAPI', 'T_EXT_SERVLET', 'T_EXT_PROXY', 'T_EXT_LOGGER', 'T_EXT_LOADBALANCER']
            )
        );
        $this->pageDef['tp_'][$id] = $page;

        $id = 'sh';
        $page = new Page($id, Msg::UIStr('tab_sh'), new TblMap('scripthandler:*addsuffix$suffix', 'A_SCRIPT_TOP', 'A_SCRIPT'));
        $this->pageDef['vh_'][$id] = $page;

        $page = new Page($id, Msg::UIStr('tab_sh'), new TblMap('virtualHostConfig:scripthandler:*addsuffix$suffix', 'A_SCRIPT_TOP', 'A_SCRIPT'));
        $this->pageDef['tp_'][$id] = $page;

        $id = 'rw';
        $page = new Page($id, Msg::UIStr('tab_rewrite'), new TblMap(
            'rewrite',
            ['VT_REWRITE_CTRL',
                new TblMap('*map$name', 'VT_REWRITE_MAP_TOP', 'VT_REWRITE_MAP'),
                'VT_REWRITE_RULE',
            ]
        ));
        $this->pageDef['vh_'][$id] = $page;

        $page = new Page($id, Msg::UIStr('tab_rewrite'), new TblMap(
            'virtualHostConfig:rewrite',
            ['VT_REWRITE_CTRL',
                new TblMap('*map$name', 'VT_REWRITE_MAP_TOP', 'VT_REWRITE_MAP'),
                'VT_REWRITE_RULE',
            ]
        ));
        $this->pageDef['tp_'][$id] = $page;

        $id = 'ctx';
        $page = new Page($id, Msg::UIStr('tab_ctx'), new TblMap(
            '*context$uri',
            'VT_CTX_TOP',
            ['VT_CTX_SEL', 'VT_CTXG', 'VT_CTXJ', 'VT_CTXS', 'VT_CTXF', 'VT_CTXL',
                'VT_CTXP', 'VT_CTXC', 'VT_CTXB', 'VT_CTXR', 'VT_CTXAS', 'VT_CTXMD']
        ));
        $this->pageDef['vh_'][$id] = $page;

        $page = new Page($id, Msg::UIStr('tab_ctx'), new TblMap(
            'virtualHostConfig:*context$uri',
            'VT_CTX_TOP',
            ['VT_CTX_SEL', 'VT_CTXG', 'VT_CTXJ', 'VT_CTXS', 'VT_CTXF', 'VT_CTXL',
                'VT_CTXP', 'VT_CTXC', 'VT_CTXB', 'VT_CTXR', 'VT_CTXAS']
        ));
        $this->pageDef['tp_'][$id] = $page;

        $id = 'vhssl';
        $page = new Page($id, Msg::UIStr('tab_ssl'), new TblMap(
            'vhssl',
            ['LVT_SSL_CERT', 'LVT_SSL', 'VT_SSL_FEATURE', 'LVT_SSL_OCSP', 'LVT_SSL_CLVERIFY']
        ));
        $this->pageDef['vh_'][$id] = $page;

        $page = new Page($id, Msg::UIStr('tab_ssl'), new TblMap(
            'virtualHostConfig:vhssl',
            ['LVT_SSL_CERT', 'LVT_SSL', 'VT_SSL_FEATURE', 'LVT_SSL_OCSP', 'LVT_SSL_CLVERIFY']
        ));
        $this->pageDef['tp_'][$id] = $page;

        $id = 'wsp';
        $page = new Page($id, Msg::UIStr('tab_wsp'), new TblMap('*websocket$uri', 'VT_WBSOCK_TOP', 'VT_WBSOCK'));
        $this->pageDef['vh_'][$id] = $page;

        $page = new Page($id, Msg::UIStr('tab_wsp'), new TblMap('virtualHostConfig:*websocket$uri', 'VT_WBSOCK_TOP', 'VT_WBSOCK'));
        $this->pageDef['tp_'][$id] = $page;

        $id = 'mod';
        $page = new Page($id, Msg::UIStr('tab_mod'), new TblMap('*module$name', ['VT_MOD_TOP',
            new TblMap('*urlFilter$uri', null, ['VT_MOD_FILTERTOP', 'VT_MOD_FILTER'])], 'VT_MOD'));

        $this->pageDef['vh_'][$id] = $page;

        $page = new Page($id, Msg::UIStr('tab_mod'), new TblMap('virtualHostConfig:*module$name', ['VT_MOD_TOP',
            new TblMap('*urlFilter$uri', null, ['VT_MOD_FILTERTOP', 'VT_MOD_FILTER'])], 'VT_MOD'));
        $this->pageDef['tp_'][$id] = $page;

        $id = 'g';
        $page = new Page($id, Msg::UIStr('tab_g'), new TblMap('', ['ADM_PHP',
            new TblMap('errorlog$fileName', 'V_LOG'),
            new TblMap('accesslog$fileName', 'ADM_ACLOG'),
            new TblMap('accessControl', 'A_SEC_AC')]));
        $this->pageDef['admin'][$id] = $page;

        $id = 'usr';
        $page = new Page($id, Msg::UIStr('tab_user'), new TblMap('*index', 'ADM_USR_TOP', ['ADM_USR', 'ADM_USR_NEW']));
        $this->pageDef['admin'][$id] = $page;

        $id = 'top';
        $page = new Page($id, Msg::UIStr('tab_top'), new TblMap('*listener$name', 'ADM_L_TOP', 'ADM_L_GENERAL'));
        $this->pageDef['al'][$id] = $page;

        $id = 'lg';
        $page = new Page($id, Msg::UIStr('tab_g'), new TblMap('*listener$name', 'ADM_L_GENERAL'));
        $this->pageDef['al_'][$id] = $page;

        $id = 'lsec';
        $page = new Page($id, Msg::UIStr('tab_ssl'), new TblMap('*listener$name', ['LVT_SSL_CERT', 'LVT_SSL', 'L_SSL_FEATURE', 'LVT_SSL_CLVERIFY']));
        $this->pageDef['al_'][$id] = $page;
    }
}

<?php

define('ASSETS_URL', 'res');
use CVendor_LiteSpeed_Msg as Msg;
use CVendor_LiteSpeed_Info as Info;
use CVendor_LiteSpeed_UIProperty as UIProperty;

class CVendor_LiteSpeed_UIBase {
    //array("Display Name" => "URL");
    protected $uiproperty;

    protected function __construct() {
        $this->uiproperty = new UIProperty();
    }

    protected function confformStart() {
        $formaction = $this->uiproperty->get(UIProperty::FLD_FORM_ACTION);
        $buf = '
		<!-- ========================== confform STARTS HERE ========================== -->
		<form name="confform" id="confform" method="post" action="' . $formaction . '">
		';

        return $buf;
    }

    protected function confformEnd() {
        $buf = '';
        $hiddenvars = $this->uiproperty->get(UIProperty::FLD_FORM_HIDDENVARS);
        foreach ($hiddenvars as $n => $v) {
            $buf .= '<input type="hidden" name="' . $n . '" value="' . $v . '">';
        }
        $buf .= '</form>
<!-- ========================== confform ENDS HERE ========================== -->
		';

        return $buf;
    }

    protected function printConfPage($disp, $page) {
        $this->uiproperty->set(UIProperty::FLD_FORM_ACTION, '#view/confMgr.php');

        $disp->InitUIProps($this->uiproperty);

        $icontitle = $disp->get(Info::FLD_ICON_TITLE);
        echo $this->contentHeader($icontitle[0], $icontitle[1], $page->GetLabel());
        echo $this->confformStart();
        echo $this->mainTabs();
        echo '<div class="tab-content margin-top-10 padding-10">';

        $page->PrintHtml($disp);

        echo "</div>\n";
        echo $this->confformEnd();
    }

    protected function mainTabs() {
        $tabs = $this->uiproperty->get(UIProperty::FLD_TABS);

        $buf = '<div><ul class="nav nav-tabs" role="tablist">';

        foreach ($tabs as $name => $uri) {
            $buf .= '<li';
            if ($name[0] == '1') {
                $buf .= ' class="active"';
            }

            $buf .= '><a href="' . $uri . '">' . substr($name, 1) . '</a></li>';
        }

        $buf .= "</ul></div>\n";

        return $buf;
    }

    public static function contentHeader($icon, $title, $subtitle = '') {
        $serverload = implode(', ', sys_getloadavg());
        $pid = Service::ServiceData(SInfo::DATA_PID);

        if ($subtitle != '') {
            $title .= ' <span>&gt; ' . $subtitle . '</span>';
        }

        $buf = '<div class="row">
			<div class="col-xs-12 col-sm-7 col-md-7 col-lg-7">
				<h2 class="page-title txt-color-blueDark"><i class="fa-fw fa ' . $icon
                    . '"></i> ' . $title . '</h2>
			</div>
			<div class="col-xs-12 col-sm-5 col-md-5 col-lg-5">
				<ul id="sparks" class="">
					<li class="sparks-info">
						<h5>LSWS PID <span id="lst-pid" class="txt-color-blue"> ' . $pid . ' </span></h5>&nbsp;
						<a class="btn btn-success" title="' . Msg::UIStr('menu_restart') . '" href="javascript:lst_restart()"><i class="fa fa-lg fa-repeat"></i></a>
					</li>
					<li class="sparks-info">
						<h5> ' . Msg::UIStr('note_loadavg') . ' <span id="lst-load" class="txt-color-purple"> ' . $serverload . ' </span></h5>&nbsp;
						<a class="btn btn-info" title="' . Msg::UIStr('menu_rtstats') . '" href="#view/realtimestats.php"><i class="fa fa-lg fa-bar-chart-o"></i></a>
					</li>

				</ul>
			</div>
		</div>
				';

        return $buf;
    }

    public static function getActionButtons($actdata, $type) {
        $buf = '<div ';
        if ($type == 'toolbar') {
            $buf .= 'class="jarviswidget-ctrls" role="menu">';

            foreach ($actdata as $act) {
                $buf .= '<a href="' . $act['href']
                . '" class="button-icon jarviswidget-toggle-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="'
                        . $act['label'] . '"><i class="fa ' . $act['ico'] . '"></i></a>';
            }
        } elseif ($type == 'icon') {
            $buf .= 'class="btn-toolbar"><ul class="action-btns">';

            foreach ($actdata as $act) {
                $buf .= '<li><a href="' . $act['href']
                . '"  class="btn bg-color-blueLight btn-xs txt-color-white" rel="tooltip" title="" data-placement="bottom" data-original-title="'
                        . $act['label'] . '"><i class="fa ' . $act['ico'] . '"></i></a></li> ' . "\n";
            }

            $buf .= '</ul>';
        } elseif ($type == 'text') {
            $buf .= 'class="btn-toolbar"><ul class="action-btns">';
            foreach ($actdata as $act) {
                $buf .= '<li class="padding-10"><a href="' . $act['href']
                . '"  class="btn btn-labeled btn-default"><span class="btn-label"><i class="fa ' . $act['ico'] . '"></i></span> <strong>'
                        . $act['label'] . ' </strong></a></li> ' . "\n";
            }

            $buf .= '</ul>';
        }
        $buf .= '</div>';

        return $buf;
    }

    public static function getTblTips($tips) {
        $buf = '<div class="alert alert-success fade in"><ul>';
        foreach ($tips as $tip) {
            if ($tip != '') {
                $buf .= "<li>${tip}</li>\n";
            }
        }
        $buf .= "</ul></div>\n";

        return $buf;
    }

    public static function message($title = '', $msg = '', $type = 'normal') {
        return '<div class="alert alert-danger">' . $msg . '</div>';
    }

    public static function errorDivmesg($msg) {
        return '<div class="alert alert-danger">' . $msg . '</div>';
    }

    public static function infoDivmesg($msg) {
        return '<div class="alert alert-info">' . $msg . '</div>';
    }

    public static function warnDivmesg($msg) {
        return '<div class="alert alert-warning">' . $msg . '</div>';
    }

    public static function genOptions($options, $selValue, $keyIsValue = false) {
        $o = '';
        if ($options) {
            foreach ($options as $key => $value) {
                if ($keyIsValue) {
                    $key = $value;
                }
                if ($key === 'forcesel') {
                    $o .= '<option disabled ';
                    if ($selValue === null || $selValue === '') {
                        $o .= 'selected';
                    }
                } else {
                    $o .= '<option value="' . $key . '"';

                    if (($key == $selValue)
                        && !($selValue === '' && $key === 0)
                        && !($selValue === null && $key === 0)
                        && !($selValue === '0' && $key === '')
                        && !($selValue === 0 && $key === '')
                    ) {
                        $o .= ' selected="selected"';
                    }
                }
                $o .= ">${value}</option>\n";
            }
        }

        return $o;
    }

    // for plot
    // tabstatus = 1: active, 0: not active, -1: not tab

    public static function genPlotTab($tab_id, $bottomdef, $plotdef, $tabstatus) {
        $buf = '<div id="' . $tab_id . '"';
        if ($tabstatus != -1) {
            // is tabpane
            $buf .= ' class="tab-pane fade padding-10 no-padding-bottom';
            if ($tabstatus == 1) {
                $buf .= ' active in';
            }
            $buf .= '"';
        }
        $buf .= '><div class="widget-body-toolbar bg-color-white smart-form" id="rev-toggles"><div class="inline-group">';

        //array(seq, label, checked)
        foreach ($plotdef as $d) {
            $buf .= '<label class="checkbox"><input type="checkbox" ';
            if ($d[2]) {
                $buf .= 'checked="checked" ';
            }
            $buf .= 'data-lst-stat-idx="' . $d[0] . '"><i></i> ' . $d[1] . " </label>\n";
        }

        $buf .= '</div></div>
				<div class="widget-body">
					<div class="chart-large txt-color-blue"></div>
				';

        //$buf .= '<div class="show-stat-microcharts font-sm lst-stat-bottom" data-lst-stat-id="' . $tab_id . '">';
        $buf .= '<div class="show-stat-microcharts font-sm">';

        foreach ($bottomdef as $btngroup) {
            $buf .= '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">';
            foreach ($btngroup as $div) {
                $buf .= self::statBottomDiv($div[0], $div[1], $div[2], $div[3]);
            }
            $buf .= "</div>\n";
        }

        $buf .= "</div>\n";

        $buf .= '</div>
		</div>';

        return $buf;
    }

    private static function statBottomDiv($seq, $label, $txtclr, $maxclr) {
        $buf = '<div>' . $label . ': <span class="lst-stat-val';
        if ($txtclr != '') {
            $buf .= ' text-' . $txtclr;
        }
        $buf .= '" data-lst-stat-idx="' . $seq . '"></span>';
        if ($maxclr != '') {
            $buf .= '<div class="smaller-stat hidden-sm pull-right"><span class="label hide lst-stat-max bg-color-'
                    . $maxclr . '" data-lst-stat-idx="' . $seq . '"></span></div>';
        }
        $buf .= "</div>\n";

        return $buf;
    }

    public static function getLangDropdown() {
        $langlist = Msg::getSupportedLang($curlang);

        $buf = '<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span>'
                . $langlist[$curlang][0] . '</span> <i class="fa fa-angle-down"></i> </a>
				<ul id="lst-lang" class="dropdown-menu pull-right">';

        foreach ($langlist as $lang => $linfo) {
            $buf .= '<li data-lang="' . $lang . '"';
            if ($lang == $curlang) {
                $buf .= ' class="active"';
            }
            $buf .= '><a href="javascript:void(0);">' . $linfo[0] . '</a></li>';
        }
        $buf .= "</ul>\n";

        return $buf;
    }

    public static function grabInput($origin, $name, $type = '') {
        if ($name == '' || $origin == '') {
            return null;
        }

        $temp = null;

        switch (strtoupper($origin)) {
            case 'REQUEST':
            case 'ANY':
                $temp = $_REQUEST;

                break;
            case 'GET':
                $temp = $_GET;

                break;
            case 'POST':
                $temp = $_POST;

                break;
            case 'COOKIE':
                $temp = $_COOKIE;

                break;
            case 'FILE':
                $temp = $_FILES;

                break;
            case 'SERVER':
                $temp = $_SERVER;

                break;
            default:
                die('input extract error.');
        }

        if (array_key_exists($name, $temp)) {
            $temp = $temp[$name];
        } else {
            $temp = null;
        }

        switch ($type) {
            case 'int':
                return (int) $temp;
            case 'float':
                // filter_var($temp, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND); filter module not in admin_php
                return (float) str_replace(',', '', $temp);
            case 'string':
                return trim((string) $temp);
            case 'array':
                return is_array($temp) ? $temp : null;
            case 'object':
                return is_object($temp) ? $temp : null;
            default:
                return trim((string) $temp); //default string
        }
    }

    public static function grabGoodInput($origin, $name, $type = '') {
        $val = self::grabInput($origin, $name, $type);
        if ($val != null && strpos($val, '<') !== false) {
            $val = null;
        }

        return $val;
    }

    public static function grabGoodInputWithReset($origin, $name, $type = '') {
        $val = self::grabInput($origin, $name, $type);
        if ($val != null && strpos($val, '<') !== false) {
            switch (strtoupper($origin)) {
                case 'REQUEST':
                case 'ANY':
                    $_REQUEST[$name] = null;

                    break;
                case 'GET':
                    $_GET[$name] = null;

                    break;
                case 'POST':
                    $_POST[$name] = null;

                    break;
                case 'COOKIE':
                    $_COOKIE[$name] = null;

                    break;
                case 'FILE':
                    $_FILES[$name] = null;

                    break;
                case 'SERVER':
                    $_SERVER[$name] = null;

                    break;
            }
            $val = null;
        }

        return $val;
    }
}

<?php

/**
 * Description of StyleRenderer
 *
 * @author Hery
 */
class CProfiler_StyleRenderer {

    public function render() {
        $coreStyle = '#kohana-profiler
{
	font-family: Monaco, \'Courier New\';
	background-color: #F8FFF8;
	margin-top: 20px;
	clear: both;
	padding: 10px 10px 0;
	border: 1px solid #E5EFF8;
	text-align: left;
}
#kohana-profiler pre
{
	margin: 0;
	font: inherit;
}
#kohana-profiler .kp-meta
{
	margin: 0 0 10px;
	padding: 4px;
	background: #FFF;
	border: 1px solid #E5EFF8;
	color: #A6B0B8;
	text-align: center;
}';
        $tableStyle = '#kohana-profiler .kp-table
{
	font-size: 1.0em;
	color: #4D6171;
	width: 100%;
	border-collapse: collapse;
	border-top: 1px solid #E5EFF8;
	border-right: 1px solid #E5EFF8;
	border-left: 1px solid #E5EFF8;
	margin-bottom: 10px;
}
#kohana-profiler .kp-table td
{
	background-color: #FFFFFF;
	border-bottom: 1px solid #E5EFF8;
	padding: 3px;
	vertical-align: top;
}
#kohana-profiler .kp-table .kp-title td
{
	font-weight: bold;
	background-color: inherit;
}
#kohana-profiler .kp-table .kp-altrow td
{
	background-color: #F7FBFF;
}
#kohana-profiler .kp-table .kp-totalrow td
{
	background-color: #FAFAFA;
	border-top: 1px solid #D2DCE5;
	font-weight: bold;
}
#kohana-profiler .kp-table .kp-column
{
	width: 100px;
	border-left: 1px solid #E5EFF8;
	text-align: center;
}
#kohana-profiler .kp-table .kp-data, #kohana-profiler .kp-table .kp-name
{
	background-color: #FAFAFB;
	vertical-align: top;
}
#kohana-profiler .kp-table .kp-name
{
	width: 200px;
	border-right: 1px solid #E5EFF8;
}
#kohana-profiler .kp-table .kp-altrow .kp-data, #kohana-profiler .kp-table .kp-altrow .kp-name
{
	background-color: #F6F8FB;
}';

        return $coreStyle . $tableStyle;
    }

}

<?php

class CReport_Jasper_Report {
    public static $defaultFolder = 'app.jrxml';

    public static $locale = 'en_us';

    public static $columnHeaderRepeat = false;

    /**
     * Type of instruction generation
     * value after : process intructions after generate all intrucions
     * value inline : process intrucions after gerenate each detail.
     *
     * @var string
     */
    public static $proccessintructionsTime = 'after';

    public $pageChanged;

    public $arrayVariable;

    public $arrayfield;

    public $arrayParameter;

    public $arrayProperty;

    public $arrayPageSetting;

    public $arrayGroup;

    public $sql;

    public $print_expression_result;

    public $returnedValues = [];

    public $arrayStyles;

    protected $param;

    /**
     * @var CReport_Jasper_Element_Root
     */
    protected $root;

    /**
     * @var CReport_Jasper_Report_DataIterator
     */
    private $data;

    /**
     * @var CReport_Jasper_ProcessorAbstract
     */
    private $processor;

    public function __construct($xmlFile, $param) {
        $keyword = '<queryString>
        <![CDATA[';
        $xmlFile = str_replace($keyword, '<queryString><![CDATA[', $xmlFile);
        $xmlElement = simplexml_load_string($xmlFile, null, LIBXML_NOCDATA);
        $this->param = $param;
        $this->root = new CReport_Jasper_Element_Root($xmlElement);
        // $this->name = get_class($this);

        // atribui o conteúdo do label
        foreach ($xmlElement as $obj => $value) {
            if (ucfirst($obj) == 'Style') {
                $this->addStyle($value);
            }
        }
        $this->parameterHandler($xmlElement, $param);
        $this->propertyHandler($xmlElement, $param);
        $this->fieldHandler($xmlElement);
        $this->variableHandler($xmlElement);
        $this->pageSetting($xmlElement);
        $this->queryStringHandler($xmlElement);
        $this->groupHandler($xmlElement);
    }

    /**
     * @param CCollection $data
     *
     * @return CReport_Jasper_Report
     */
    public function setData(CCollection $data) {
        $this->data = new CReport_Jasper_Report_DataIterator($data);

        return $this;
    }

    /**
     * @return CReport_Jasper_Report_DataIterator
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @return null|CReport_Jasper_ProcessorAbstract
     */
    public function getProcessor() {
        return $this->processor;
    }

    /**
     * @return CReport_Jasper_Report_DataRow
     */
    public function getCurrentRow() {
        return $this->data->current();
    }

    public function pageSetting($xml_path) {
        $this->arrayPageSetting['orientation'] = 'P';
        $this->arrayPageSetting['name'] = $xml_path['name'];
        $this->arrayPageSetting['language'] = $xml_path['language'];
        $this->arrayPageSetting['pageWidth'] = $xml_path['pageWidth'];
        $this->arrayPageSetting['pageHeight'] = $xml_path['pageHeight'];
        if (isset($xml_path['orientation'])) {
            $this->arrayPageSetting['orientation'] = mb_substr($xml_path['orientation'], 0, 1);
        }
        $this->arrayPageSetting['columnWidth'] = $xml_path['columnWidth'];
        $this->arrayPageSetting['columnCount'] = $xml_path['columnCount'];
        $this->arrayPageSetting['CollumnNumber'] = 1;
        $this->arrayPageSetting['leftMargin'] = $xml_path['leftMargin'];
        $this->arrayPageSetting['defaultLeftMargin'] = $xml_path['leftMargin'];
        $this->arrayPageSetting['rightMargin'] = $xml_path['rightMargin'];
        $this->arrayPageSetting['topMargin'] = $xml_path['topMargin'];
        // $this->yAxis = $xml_path['topMargin'];
        $this->arrayPageSetting['bottomMargin'] = $xml_path['bottomMargin'];
    }

    public function fieldHandler($xml_path) {
        foreach ($xml_path->field as $field) {
            $this->arrayfield[] = $field['name'];
        }
    }

    public function parameterHandler($xml_path, $param) {
        $this->arrayParameter = [];
        if ($xml_path->parameter) {
            foreach ($xml_path->parameter as $parameter) {
                $paraName = (string) $parameter['name'];
                $this->arrayParameter[$paraName] = array_key_exists($paraName, $param) ? $param[$paraName] : '';
            }
        } else {
            $this->arrayParameter = [];
        }
    }

    public function propertyHandler($xml_path, $param) {
        $this->arrayProperty = [];
        if ($xml_path->property) {
            foreach ($xml_path->property as $property) {
                $paraName = (string) $property['name'];
                $this->arrayProperty[$paraName] = (string) $property['value'];
            }
        } else {
            $this->arrayProperty = [];
        }
    }

    public function variableHandler($xml_path) {
        $this->arrayVariable = [];
        foreach ($xml_path->variable as $variable) {
            $varName = (string) $variable['name'];
            $this->arrayVariable[$varName] = [
                'calculation' => $variable['calculation'] . '',
                'target' => $variable->variableExpression,
                'class' => $variable['class'] . '',
                'resetType' => $variable['resetType'] . '',
                'resetGroup' => $variable['resetGroup'] . '',
                'initialValue' => (string) $variable->initialValueExpression . '',
                'incrementType' => $variable['incrementType']
            ];
        }
    }

    public function groupHandler($xml_path) {
        $this->arrayGroup = [];
        foreach ($xml_path->group as $group) {
            $groupName = (string) $group['name'];
            $this->arrayGroup[$groupName] = $group;
            $group->addAttribute('resetVariables', 'false');
        }
    }

    public function prepareSql($sql, $arrayParameter = []) {
        if (isset($arrayParameter) && !empty($arrayParameter)) {
            foreach ($arrayParameter as $v => $a) {
                if (is_array($a)) {
                    foreach ($a as $x) {
                        // se for um inteiro
                        if (is_integer($x)) {
                            $foo[] = $x;
                        } elseif (is_string($x)) {
                            // se for string, adiciona aspas
                            $foo[] = "'$x'";
                        }
                    }
                    // converte o array em string separada por ","
                    $result = '(' . implode(',', $foo) . ')';
                    $sql = str_replace('$P{' . $v . '}', $result, $sql);
                } else {
                    /* if (is_integer($a))
                      {
                      $x = $a ;
                      }
                      else if (is_string($a))
                      {
                      // se for string, adiciona aspas
                      $x= "'$a'";
                      } */
                    $sql = str_replace('$P{' . $v . '}', $a, $sql);
                    $sql = str_replace('$P!{' . $v . '}', $a, $sql);
                }
            }
        }

        return $sql;
    }

    public function queryStringHandler($xml_path) {
        //var_dump($xml_path);
        $this->sql = (string) $xml_path->queryString;
        if (strlen(trim($xml_path->queryString)) > 0) {
            if (isset($this->arrayParameter)) {
                $this->sql = $this->prepareSql($this->sql, $this->arrayParameter);
            }
        }
    }

    public function variablesCalculation($row) {
        if ($this->arrayVariable) {
            foreach ($this->arrayVariable as $k => $out) {
                $this->variableCalculation($k, $out, $row);
            }
        }
        if ($this->pageChanged == true) {
            $this->pageChanged = false;
        }
    }

    public function setReturnVariables($subReportTag, $arrayVariablesSubReport) {
        if ($subReportTag->returnValues) {
            foreach ($subReportTag->returnValues as $key => $value) {
                $val = (array) $value;
                $subreportVariable = (string) $value['subreportVariable'];
                $toVariable = (string) $value['toVariable'];
                $ans = (array_key_exists('ans', $arrayVariablesSubReport[$subreportVariable])) ? $arrayVariablesSubReport[$subreportVariable]['ans'] : '';
                $val['ans'] = $ans;
                $val['calculation'] = (string) $value['calculation'];
                $val['class'] = (string) $value['class'];
                $this->returnedValues[$toVariable] = $val;
            }
            $this->returnedValuesCalculation();
        }
    }

    public function returnedValuesCalculation() {
        foreach ($this->returnedValues as $k => $out) {
            $out['target'] = '$F{' . $k . '}';
            //var_dump($out);
            $subreportVariable = (string) $out['@attributes']['subreportVariable'];
            $toVariable = (string) $out['@attributes']['toVariable'];
            $row = [];
            $row[$k] = $out['ans'];
            $this->variableCalculation($k, $out, (object) $row);
        }
    }

    public function getExpression($text, CReport_Jasper_Report_DataRow $row = null, $writeHTML = null, $element = null) {
        preg_match_all("/P{(\w+)}/", $text, $matchesP);
        if ($matchesP) {
            foreach ($matchesP[1] as $macthP) {
                $text = str_ireplace(['$P{' . $macthP . '}', '"'], [$this->arrayParameter[$macthP], ''], $text);
            }
        }

        $originalText = $text;
        preg_match_all("/V{(\w+)}/", $text, $matchesV);
        if ($matchesV) {
            foreach ($matchesV[1] as $matchV) {
                $text = $this->getValOfVariable($matchV, $text, $writeHTML, $element);
            }
        }

        if ($row) {
            preg_match_all('/F{[^}]*}/', $text, $matchesF);
            if ($matchesF) {
                //var_dump($matchesF);
                foreach ($matchesF[0] as $matchF) {
                    $match = str_ireplace(['F{', '}'], '', $matchF);
                    $text = $this->getValOfField($match, $row, $text, $writeHTML);
                }
            }
        }

        // Regex pattern to capture the entire expression
        $ternaryPattern = '/(.*?)\?(.*?)\:(.*)/';

        if (preg_match($ternaryPattern, $text, $matchesTernary) > 0) {
            // Extract the components

            $condition = trim($matchesTernary[1]);
            $valueIfTrue = trim($matchesTernary[2]);
            $valueIfFalse = trim($matchesTernary[3]);
            $text = $this->evaluateCondition($condition) ? $valueIfTrue : $valueIfFalse;
            // $mathValue = eval('return (' . $out['target'] . ');');

             // error_reporting(5);
        }

        return $text;
    }

    public function evaluateCondition($condition) {
        // Handle basic condition evaluation
        // Handle basic condition evaluation
        if (preg_match('/(.*?)==(.*?)$/', $condition, $condMatches)) {
            $leftOperand = trim(trim($condMatches[1], "\n\r\t\b\0\"' "));
            $rightOperand = trim(trim($condMatches[2], "\n\r\t\b\0\"' "));

            return $leftOperand == $rightOperand;
        }
        // Add more condition checks as needed (e.g., !=, <, >, etc.)

        return false;
    }

    public function getValOfVariable($variable, $text, $htmlentities = false, $element = null) {
        $val = array_key_exists($variable, $this->arrayVariable) ? $this->arrayVariable[$variable] : [];
        $ans = array_key_exists('ans', $val)
            ? $val['ans']
            : (
                array_key_exists('initialValue', $val)
                ? $val['initialValue']
                : ''
            );
        if (preg_match_all('/V{' . $variable . "}\.toString/", $text, $matchesV) > 0) {
            //$ans = $ans+0;
            $ans = ($ans) ? number_format($ans, 2, ',', '.') : $ans;

            return str_ireplace(['$V{' . $variable . '}.toString()'], [$ans], $text);
        } elseif (preg_match_all('/V{' . $variable . "}\.numberToText/", $text, $matchesV) > 0) {
            return str_ireplace(['$V{' . $variable . '}.numberToText()'], [$this->numberToText($ans, false)], $text);
        } elseif (preg_match_all('/V{' . $variable . "}\.(\w+)/", $text, $matchesV) > 0) {
            $funcName = $matchesV[1][0];
            if (method_exists($this, $funcName)) {
                return str_ireplace(['$V{' . $variable . '}'], [call_user_func_array([$this, $funcName], [$ans, true])], $text);
            } else {
                return str_ireplace(['$V{' . $variable . '}'], [call_user_func($funcName, $ans)], $text);
            }
        } elseif ($variable == 'MASTER_TOTAL_PAGES') {
            return str_ireplace(['$V{MASTER_TOTAL_PAGES}'], ['{:ptp:}'], $text);
        } elseif ($variable == 'PAGE_NUMBER' || $variable == 'MASTER_CURRENT_PAGE' || $variable == 'CURRENT_PAGE_NUMBER') {
            if ((CReport_Jasper_Instructions::$processingPageFooter && CReport_Jasper_Instructions::$lastPageFooter)
               || (isset($element->evaluationTime) && $element->evaluationTime == 'Report')
            ) {
                return str_ireplace(['$V{' . $variable . '}'], ['{:ptp:}'], $text);
            }

            return str_ireplace(['$V{' . $variable . '}'], [CReport_Jasper_Instructions::$currrentPage], $text);
        } else {
            return str_ireplace(['$V{' . $variable . '}'], [$ans], $text);
        }
    }

    public function getValOfField($field, CReport_Jasper_Report_DataRow $row, $text, $htmlentities = false) {
        error_reporting(0);
        $fieldParts = strpos($field, '->') ? explode('->', $field) : explode('-&gt;', $field);
        $obj = $row;
        //var_dump($fieldParts);
        // exit;
        foreach ($fieldParts as $part) {
            if (preg_match_all("/\w+/", $part, $matArray)) {
                if (count($matArray[0]) > 1) {
                    $objArrayName = $matArray[0][0];
                    $objCounter = $matArray[0][1];
                    $obj = $obj->$objArrayName;
                    $obj = $obj[$objCounter];
                } elseif (is_array($obj) || $obj instanceof CCollection) {
                    if ($obj instanceof CCollection) {
                        cdbg::dd($obj, $part, cdbg::getTraceString());
                        $obj = $obj->get($part);
                    } else {
                        if (array_key_exists($part, $obj)) {
                            $obj = $obj[$part];
                        } else {
                            $obj = '';
                        }
                    }
                } elseif (is_object($obj)) {
                    preg_match_all("/(\w+)\(\)/", $part, $matchMethod);
                    if ($matchMethod && array_key_exists(0, $matchMethod[1])) {
                        $method = $matchMethod[1][0];
                        $obj = $obj->$method();
                    } else {
                        $obj = $obj->$part;
                    }
                } else {
                    $obj = '';
                }
            }
        }
        $val = $obj;
        error_reporting(5);
        $fieldRegExp = str_ireplace('[', "\[", $field);
        if (preg_match_all('/F{' . $fieldRegExp . "}\.toString/", $text, $matchesV) > 0) {
            //$val = ($val)?$val:0;
            $val = ($val) ? number_format($val, 2, ',', '.') : $val;

            return str_ireplace(['$F{' . $field . '}.toString()'], [$val], $text);
        } elseif (preg_match_all('/F{' . $fieldRegExp . "}\.numberToText/", $text, $matchesV) > 0) {
            return str_ireplace(['$F{' . $field . '}.numberToText()'], [$this->numberToText($val, false)], $text);
        } elseif (preg_match_all('/F{' . $fieldRegExp . "}\.(\w+)\((\w+)\)/", $text, $matchesV) > 0) {
            $funcName = $matchesV[1][0];
            //return str_ireplace(array('$'.$matchesV[0][0]),array(call_user_func_array(array($this,$funcName),array($val,$matchesV[2][0]))),$text);
            if (method_exists($this, $funcName)) {
                return str_ireplace(['$' . $matchesV[0][0]], [call_user_func_array([$this, $funcName], [$val, $matchesV[2][0]])], $text);
            } else {
                return str_ireplace(['$' . $matchesV[0][0]], [call_user_func($funcName, $val)], $text);
            }
        } elseif (preg_match_all('/F{' . $fieldRegExp . "}\.(\w+)/", $text, $matchesV) > 0) {
            $funcName = $matchesV[1][0];
            if (method_exists($this, $funcName)) {
                return str_ireplace(['$' . $matchesV[0][0] . '()'], [call_user_func_array([$this, $funcName], [$val, true])], $text);
            } else {
                return str_ireplace(['$' . $matchesV[0][0] . '()'], [call_user_func($funcName, $val)], $text);
            }
        } elseif (is_array($val)) {
            return $val;
        } elseif ($val === false) {
            return str_ireplace('$F{' . $field . '}', '0', $text);
        } else {
            return str_ireplace(['$F{' . $field . '}'], [($val)], $text);
        }
    }

    public function calculateMathExpression($expression) {
        // Remove any unwanted characters (for security)
        $expression = preg_replace('/[^0-9\.\+\-\*\/\s]/', '', $expression);

        // Split the expression into tokens (numbers and operators)
        $tokens = preg_split('/\s*([\+\-\*\/])\s*/', $expression, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        // Initialize the result with the first number
        $result = array_shift($tokens);

        // Ensure the initial result is a float
        $result = floatval($result);

        // Iterate through the tokens and perform the calculations
        while (!empty($tokens)) {
            $operator = array_shift($tokens);
            $number = array_shift($tokens);

            // Ensure the number is a float
            $number = floatval($number);

            // Perform the calculation based on the operator
            switch ($operator) {
                case '+':
                    $result += $number;

                    break;
                case '-':
                    $result -= $number;

                    break;
                case '*':
                    $result *= $number;

                    break;
                case '/':
                    if ($number == 0) {
                        throw new Exception('Division by zero');
                    }
                    $result /= $number;

                    break;
            }
        }

        return $result;
    }

    public function variableCalculation($k, $out, $row) {
        preg_match_all("/P{(\w+)}/", $out['target'], $matchesP);
        if ($matchesP) {
            foreach ($matchesP[1] as $macthP) {
                $out['target'] = str_ireplace(['$P{' . $macthP . '}'], [$this->arrayParameter[$macthP]], $out['target']);
            }
        }
        preg_match_all("/V{(\w+)}/", $out['target'], $matchesV);
        if ($matchesV) {
            foreach ($matchesV[1] as $macthV) {
                if (is_array($this->arrayVariable[$macthV])) {
                    $ans = array_key_exists('ans', $this->arrayVariable[$macthV])
                        ? $this->arrayVariable[$macthV]['ans']
                        : (
                            array_key_exists('initialValue', $this->arrayVariable[$macthV])
                            ? $this->arrayVariable[$macthV]['initialValue']
                            : ''
                        );
                } else {
                    $ans = '';
                }
                $defVal = $ans != '' ? $ans : $this->arrayVariable[$macthV]['initialValue'];

                $out['target'] = str_ireplace(['$V{' . $macthV . '}'], [$ans], $out['target']);
            }
        }

        preg_match_all("/F{(\w+)}/", $out['target'], $matchesF);
        if ($matchesF) {
            foreach ($matchesF[1] as $matchF) {
                $out['target'] = $this->getValOfField($matchF, $row, $out['target']); //str_ireplace(array('$F{'.$macthF.'}'),array(utf8_encode($row->$macthF)),$out['target']);
                // $ans = $this->getValOfField($matchF, $row, $out['target']);
                // cdbg::dd($ans);
                // if($ans) {
                //     str_ireplace(['$F{' . $matchF . '}'], [$ans], $out['target']);
                // }
            }
        }

        $htmlData = array_key_exists('htmlData', $this->arrayVariable) ? $this->arrayVariable['htmlData']['class'] : '';
        if (preg_match('/(\d+)(?:\s*)([\+\-\*\/])(?:\s*)/', $out['target'], $matchesMath) > 0 && $htmlData != 'HTMLDATA') {
            // error_reporting(0);
            $mathValue = $this->calculateMathExpression($out['target']);
            // $mathValue = eval('return (' . $out['target'] . ');');

            // error_reporting(5);
        }

        $value = (array_key_exists('ans', $this->arrayVariable[$k])) ? $this->arrayVariable[$k]['ans'] : null;
        $newValue = (isset($mathValue)) ? $mathValue : $out['target'];
        $resetType = (array_key_exists('resetType', $out)) ? $out['resetType'] : '';

        switch ($out['calculation']) {
            case 'Sum':
                if (isset($this->arrayVariable[$k]['class']) && $this->arrayVariable[$k]['class'] == 'java.sql.Time') {
                    $value = CReport_Jasper_Utils_TimeUtils::timeToSecond($value);

                    $value += CReport_Jasper_Utils_TimeUtils::timeToSecond($newValue);
                    $value = CReport_Jasper_Utils_TimeUtils::secondToTime($value);
                } else {
                    $value += is_numeric($newValue) ? $newValue : 0;
                }

                break;
            case 'Average':
                if (isset($this->arrayVariable[$k]['class']) && $this->arrayVariable[$k]['class'] == 'java.sql.Time') {
                    $value = CReport_Jasper_Utils_TimeUtils::timeToSecond($value);

                    $value += CReport_Jasper_Utils_TimeUtils::timeToSecond($newValue);
                    $value = CReport_Jasper_Utils_TimeUtils::secondToTime($value);
                } else {
                    $value = ($value * ($this->report_count - 1) + $newValue) / $this->report_count;
                }

                break;
            case 'DistinctCount':
                break;
            case 'Lowest':
                foreach ($this->data as $rowData) {
                    $lowest = $rowData->$out['target'];
                    if ($rowData->$out['target'] < $lowest) {
                        $lowest = $rowData->$out['target'];
                    }
                    $value = $lowest;
                }

                break;
            case 'Highest':
                $out['ans'] = 0;
                foreach ($this->data as $rowData) {
                    if ($rowData->$out['target'] > $out['ans']) {
                        $value = $rowData->$out['target'];
                    }
                }

                break;
            case 'Count':
                $value = $this->arrayVariable[$k]['ans'];
                $value++;

                break;
            case '':
            case 'System':
                $value = $newValue;

                break;
        }
        if ($resetType == 'Page') {
            if ($this->pageChanged == 'true') {
                $value = $newValue;
            }
        }
        $this->arrayVariable[$k]['lastValue'] = $newValue;
        if ($resetType == 'Group') {
            if ($this->arrayGroup[$out['resetGroup']]->resetVariables == 'true') {
                $value = $newValue;
            }
        }
        $this->arrayVariable[$k]['ans'] = $value;
    }

    public function getPageNo() {
        $pdf = CReport_Jasper_Instructions::get();

        return $pdf->getPage();
    }

    public function getAliasNbPages() {
        $pdf = CReport_Jasper_Instructions::get();

        return $pdf->getNumPages();
    }

    public function updatePageNo($s) {
        $pdf = CReport_Jasper_Instructions::get();

        return str_replace('$this->PageNo()', $pdf->PageNo(), $s);
    }

    public function right($value, $count) {
        return mb_substr($value, ($count * -1));
    }

    public function left($string, $count) {
        return mb_substr($string, 0, $count);
    }

    public static function formatText($txt, $pattern) {
    }

    public function numberToText($valor = 0, $maiusculas = false, $money = true) {
        $singular = [' centavo', '', ' mil', 'milhão', 'bilhão', 'trilhão', 'quatrilhão'];
        $plural = [' centavos', '', ' mil', 'milhões', 'bilhões', 'trilhões',
            'quatrilhões'];

        $c = ['', 'cem', 'duzentos', 'trezentos', 'quatrocentos',
            'quinhentos', 'seiscentos', 'setecentos', 'oitocentos', 'novecentos'];
        $d = ['', 'dez', 'vinte', 'trinta', 'quarenta', 'cinquenta',
            'sessenta', 'setenta', 'oitenta', 'noventa'];
        $d10 = ['dez', 'onze', 'doze', 'treze', 'quatorze', 'quinze',
            'dezesseis', 'dezesete', 'dezoito', 'dezenove'];
        $u = ['', 'um', 'dois', 'tres', 'quatro', 'cinco', 'seis',
            'sete', 'oito', 'nove'];

        $z = 0;
        $rt = '';
        $valor = ($valor) ? $valor : 0;
        $valor = (strpos($valor, ',') == false) ? number_format($valor, 2, '.', '.') : number_format(str_replace(',', '.', str_replace('.', '', $valor)), 2, '.', '.');
        $inteiro = explode('.', $valor);
        for ($i = 0; $i < count($inteiro); $i++) {
            for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++) {
                $inteiro[$i] = '0' . $inteiro[$i];
            }
        }

        $fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
        for ($i = 0; $i < count($inteiro); $i++) {
            $valor = $inteiro[$i];
            $rc = (($valor > 100) && ($valor < 200)) ? 'cento' : $c[$valor[0]];
            $rd = ($valor[1] < 2) ? '' : $d[$valor[1]];
            $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : '';

            $r = $rc . (($rc && ($rd || $ru)) ? ' e ' : '') . $rd . (($rd
                    && $ru) ? ' e ' : '') . $ru;
            $t = count($inteiro) - 1 - $i;
            $r .= $r ? ($valor > 1 ? $plural[$t] : $singular[$t]) : '';
            if ($valor == '000') {
                $z++;
            } elseif ($z > 0) {
                $z--;
            }
            if (($t == 1) && ($z > 0) && ($inteiro[0] > 0)) {
                $r .= (($z > 1) ? ' de ' : '') . $plural[$t];
            }
            if ($r) {
                $rt = $rt . ((($i > 0) && ($i <= $fim)
                        && ($inteiro[0] > 0) && ($z < 1)) ? (($i < $fim) ? ', ' : ' e ') : '') . $r;
            }
        }

        if (!$maiusculas) {
            return $rt ? $rt : 'zero';
        } else {
            if ($rt) {
                $rt = str_ireplace(' E ', ' e ', ucwords($rt));
            }

            return ($rt) ? ($rt) : 'Zero';
        }
    }

    /**
     * @return null|CReport_Jasper_Report_Generator
     */
    public function generator() {
        return CReport_Jasper_Manager::instance()->getGenerator();
    }

    /**
     * @return CReport_Jasper_Element_Root
     */
    public function getRoot() {
        return $this->root;
    }

    public function addStyle($style) {
        //print_r($style);return;
        $attributes = $style->attributes();
        $key = $attributes['name'];
        $this->arrayStyles["{$key}"] = $style; // here you can trate all parameter of style
    }

    public function getStyle($key) {
        if (isset($this->arrayStyles["{$key}"])) {
            return $this->arrayStyles["{$key}"];
        }
    }

    public function applyStyle($key, &$reportElement, $rowData) {
        $style = $this->getStyle($key);
        if ($style) {
            //default
            $attributes = $style->attributes();
            if (isset($style->conditionalStyle)) {
                //percore os styles
                foreach ($style->conditionalStyle as $styleNew) {
                    $expression = $styleNew->conditionExpression;
                    //echo $expression;
                    $resultExpression = false;
                    $expression = $this->getExpression($expression, $rowData);
                    //echo 'if(' . $expression . '){$resultExpression=true;}<br/>';
                    eval('if(' . $expression . '){$resultExpression=true;}');
                    //echo $resultExpression."<br/>";
                    if ($resultExpression) {
                        //get definition style condicional
                        $attributCondicional = $styleNew->style->attributes();
                        $attributes = $attributCondicional;

                        break;
                        //var_dump($attributCondicional);
                    }
                }
            }
            //change properties
            foreach ($attributes as $key => $value) {
                //ignore
                if (!in_array($key, ['name'])) {
                    //echo "{$key} - {$value}<br/>";
                    $reportElement[$key] = $value;
                }
            }
        }
    }

    public function getPdf() {
        // $this->report()->setProcessor($this->manager()->createPdfProcessor());
        $this->processor = new CReport_Jasper_Processor_PdfProcessor($this);

        $generator = new CReport_Jasper_Report_Generator($this);
        CReport_Jasper_Manager::instance()->setGenerator($generator);
        //$this->parameter_handler($this->xmlElement, $param);
        //$this->variable_handler($this->xmlElement);
        //$this->queryString_handler($this->xmlElement);
        //var_dump($this->xmlElement);

        // exibe a tag
        // CReport_Jasper_Instructions::setJasperObj($this);
        $this->root->generate($this);
        //CReport_Jasper_Instructions::runInstructions();
        //CReport_Jasper_Instructions::clearInstructrions();

        CReport_Jasper_Instructions::runInstructions();
        //$this->runInstructions($instructions);

        $pdf = CReport_Jasper_Instructions::get();
        CReport_Jasper_Manager::instance()->unsetGenerator();

        return $pdf;
    }
}

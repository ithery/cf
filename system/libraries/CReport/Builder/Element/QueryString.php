<?php

class CReport_Builder_Element_QueryString extends CReport_Builder_ElementAbstract {
    protected $sql;

    public function __construct() {
        parent::__construct();
    }

    public function getSql() {
        return $this->sql;
    }

    public function setSql($sql) {
        $this->sql = $sql;

        return $this;
    }

    public static function fromXml(SimpleXMLElement $xml) {
        $element = new self();

        $element->setSql((string) $xml);

        return $element;
    }

    public function toJrXml() {
        $openTag = '<queryString>';

        $body = $this->getChildrenJrXml();
        $closeTag = '</queryString>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }

    protected function prepareSql(CReport_Generator $generator, $sql) {
        $bindings = [];
        $pattern = '/(\$P{(\w+)})/';

        // Lakukan pencocokan dengan pola regex untuk menemukan setiap parameter $P{param_name}
        preg_match_all($pattern, $sql, $matches, PREG_SET_ORDER);

        // Lakukan penggantian setiap placeholder ? dengan nilai dari parameter yang sesuai
        foreach ($matches as $match) {
            $placeholder = $match[1];
            $paramName = $match[2];
            $value = $generator->getParameterValue($paramName); // Ambil nilai dari fungsi getParameterValue

            // Gantikan placeholder dengan nilai parameter
            $sql = str_replace($placeholder, '?', $sql);

            // Simpan nilai parameter ke dalam array bindings
            $bindings[] = $value;
        }

        return [$sql, $bindings];
    }

    public function generate(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        $sql = $this->getSql();
        list($sql, $bindings) = $this->prepareSql($generator, $sql);

        $generator->setDataProvider(CManager::createSqlDataProvider($sql, $bindings));
    }
}

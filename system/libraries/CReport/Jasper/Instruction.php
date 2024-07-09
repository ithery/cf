<?php

class CReport_Jasper_Instruction {
    const TYPE_LINE = 'line';

    const TYPE_SET_CELL_HEIGHT_RATIO = 'setCellHeightRatio';

    const TYPE_PREVENT_Y_AXIS = 'preventYAxis';

    const TYPE_SET_Y_AXIS = 'setYAxis';

    const TYPE_SET_XY = 'setXY';

    const TYPE_SET_TEXT_COLOR = 'setTextColor';

    const TYPE_SET_DRAW_COLOR = 'setDrawColor';

    const TYPE_SET_FILL_COLOR = 'setFillColor';

    const TYPE_SET_FONT = 'setFont';

    const TYPE_MULTI_CELL = 'multiCell';

    const TYPE_ROUNDED_RECT = 'roundedRect';

    protected $type;

    protected $params;

    protected $callerInfo;

    public function __construct($type, $params, $callerInfo = null) {
        $this->type = $type;
        $this->params = $params;
        $this->callerInfo = $callerInfo;
    }

    public function method() {
        $typeMethodMap = [
            'break' => 'breaker'
        ];
        $method = carr::get($typeMethodMap, $this->type, $this->type);

        return $method;
    }

    public function run(CReport_Jasper_ProcessorAbstract $processor) {
        $method = $this->method();
        if (method_exists($processor, $method)) {
            $processor->$method($this->params);
        } else {
            throw new Exception('Method name ' . $method . 'is not exists on ' . get_class($processor));
        }
    }
}

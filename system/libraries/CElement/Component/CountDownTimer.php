<?php

class CElement_Component_CountDownTimer extends CElement_Component {
    /**
     * @var DateTime
     */
    protected $expiredDate;

    /**
     * @var string
     */
    protected $expiredText;

    /**
     * @var int
     */
    protected $autoStart;

    protected $displayFormat;

    public function __construct($id = null) {
        parent::__construct($id);
        $this->expiredDate = c::now()->addHours(1);
        $this->autoStart = true;
        $this->expiredText = 'Expired';
        $this->displayFormat = '%DD:%HH:%mm:%ss'; //moment format with % prefix
    }

    public static function factory($id = null) {
        // @phpstan-ignore-next-line
        return new static($id);
    }

    /**
     * @param DateTime $expiredDate
     *
     * @return $this
     */
    public function setExpiredDate(DateTime $expiredDate) {
        $this->expiredDate = $expiredDate;

        return $this;
    }

    /**
     * @param string $expiredText
     *
     * @return $this
     */
    public function setExpiredText($expiredText) {
        $this->expiredText = $expiredText;

        return $this;
    }

    public function setDisplayFormat($displayFormat) {
        $this->displayFormat = $displayFormat;

        return $this;
    }

    protected function build() {
        $this->addClass('cres:element:component:CountDownTimer');
        $config = [
            'expiredText' => $this->expiredText,
            'timestamp' => (int) $this->expiredDate->getTimestamp() * 1000,
            'displayFormat' => $this->displayFormat,
        ];

        $this->setAttr('cres-element', 'component:CountDownTimer');
        $this->setAttr('cres-config', htmlspecialchars(json_encode($config), ENT_QUOTES, 'UTF-8'));
    }
}

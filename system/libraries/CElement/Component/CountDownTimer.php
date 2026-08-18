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
     * @var bool
     */
    protected $autoStart;

    /**
     * @var string
     */
    protected $displayFormat;

    /**
     * @var bool
     */
    protected $countUp;

    /**
     * @param string|null $id
     *
     * @return void
     */
    public function __construct($id = null) {
        parent::__construct($id);
        $this->expiredDate = c::now()->addHours(1);
        $this->autoStart = true;
        $this->expiredText = 'Expired';
        $this->displayFormat = '%DD:%HH:%mm:%ss'; //moment format with % prefix
        $this->countUp = false;
    }

    /**
     * @param string|null $id
     *
     * @return static
     */
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

    /**
     * @param string $displayFormat
     *
     * @return $this
     */
    public function setDisplayFormat($displayFormat) {
        $this->displayFormat = $displayFormat;

        return $this;
    }

    /**
     * @return $this
     */
    public function setCountUp() {
        $this->countUp = true;

        return $this;
    }

    /**
     * @return void
     */
    protected function build() {
        $this->addClass('cres:element:component:CountDownTimer');
        $config = [
            'expiredText' => $this->expiredText,
            'timestamp' => (int) $this->expiredDate->getTimestamp() * 1000,
            'displayFormat' => $this->displayFormat,
            'countUp' => $this->countUp,
        ];

        $this->setAttr('cres-element', 'component:CountDownTimer');
        $this->setAttr('cres-config', c::json($config));
    }
}

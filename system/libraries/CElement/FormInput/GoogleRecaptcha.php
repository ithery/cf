<?php

class CElement_FormInput_GoogleRecaptcha extends CElement_FormInput {
    /**
     * @var null|CVendor_Google_Recaptcha_AbstractRecaptcha
     */
    protected $recaptcha;

    /**
     * @var string
     */
    protected $recaptchaType;

    /**
     * @var string
     */
    protected $recaptchaLabel;

    /**
     * @var string
     */
    protected $recaptchaInputName;

    public function __construct($id) {
        parent::__construct($id);
        $this->type = 'hidden';
        $this->recaptcha = null;
        $this->recaptchaType = 'simple';
        $this->recaptchaLabel = 'Send';
        $this->recaptchaInputName = 'g-recaptcha-response';
        $this->addClass('capp-google-recaptcha-control');
    }

    protected function build() {
        $this->setAttr('type', $this->type);
        $this->setAttr('value', $this->value);
        parent::build();
    }

    public function getRecaptcha() {
        if ($this->recaptcha == null) {
            $this->recaptcha = CVendor_Google::recaptchaV3();
        }

        return $this->recaptcha;
    }

    public function setRecaptcha(CVendor_Google_Recaptcha_AbstractRecaptcha $recaptcha) {
        $this->recaptcha = $recaptcha;

        return $this;
    }

    public function setRecaptchaType($type) {
        $this->recaptchaType = $type;

        return $this;
    }

    public function setRecaptchaLabel($label) {
        $this->recaptchaLabel = $label;

        return $this;
    }

    public function setRecaptchaInputName($name) {
        $this->recaptchaInputName = $name;

        return $this;
    }

    protected function buttonCallbackName() {
        return cstr::camel($this->id) . 'CallbackSubmit';
    }

    public function html($indent = 0) {
        $recaptcha = $this->getRecaptcha();
        $html = '';
        if ($recaptcha instanceof CVendor_Google_Recaptcha_RecaptchaV2) {
            if ($this->recaptchaType != 'simple') {
                if ($this->recaptchaType == 'button') {
                    $html = $recaptcha->button($this->recaptchaLabel, [], $this->buttonCallbackName());
                } else {
                    $html = $recaptcha->display('captcha', ['data-type' => $this->recaptchaType]);
                }
            } else {
                $html = $recaptcha->display();
            }
        }
        if ($recaptcha instanceof CVendor_Google_Recaptcha_RecaptchaV3) {
            $html = $recaptcha->input('g-recaptcha-response');
        }

        return $html;
    }

    public function js($indent = 0) {
        $recaptcha = $this->getRecaptcha();
        $js = '';
        if ($recaptcha instanceof CVendor_Google_Recaptcha_RecaptchaV2) {
            if ($this->recaptchaType == 'button') {
                $js .= 'function ' . $this->buttonCallbackName() . "(token) {
                    console.log('#" . $this->id . "');
                    $('#" . $this->id . "').closest('form').submit();
                }";
            }
            //$js= $recaptcha->script();
        }

        if ($recaptcha instanceof CVendor_Google_Recaptcha_RecaptchaV3) {
            $js .= $recaptcha->getApiScript();
            $js .= "
                grecaptcha.ready(function() {
                    window.noCaptcha.render('login', function (token) {
                        document.querySelector('#" . $this->recaptchaInputName . "').value = token;
                    });
                });
                ";
        }

        return $js;
    }
}

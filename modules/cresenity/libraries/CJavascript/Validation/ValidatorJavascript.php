<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 14, 2019, 1:04:19 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CJavascript_Validation_ValidatorJavascript implements CInterface_Arrayable {

    /**
     * Registered validator instance.
     *
     * @var CJavascript_Validation_ValidatorHandler
     */
    protected $validator;

    /**
     * Selector used in javascript generation.
     *
     * @var string
     */
    protected $selector;

    /**
     * Template that renders Javascript.
     *
     * @var string
     */
    protected $view;

    /**
     * Enable or disable remote validations.
     *
     * @var bool
     */
    protected $remote;

    /**
     * Enable or disable focusOnError.
     *
     * @var bool
     */
    protected $focusOnError;

    /**
     * Duration for animate scroll when focusOnError is enabled.
     *
     * @var bool
     */
    protected $animateDuration;

    /**
     * 'ignore' option for jQuery Validation Plugin.
     *
     * @var string
     */
    protected $ignore;

    /**
     * @param \Proengsoft\JsValidation\Javascript\ValidatorHandler $validator
     * @param array $options
     */
    public function __construct(CJavascript_Validation_ValidatorHandler $validator, $options = []) {
        $this->validator = $validator;
        $this->setDefaults($options);
    }

    /**
     * Set default parameters.
     *
     * @param $options
     * @return void
     */
    protected function setDefaults($options) {
        $this->selector = empty($options['selector']) ? 'form' : $options['selector'];
        $this->template = empty($options['template']) ? 'CJavascript/Validation/Validate' : $options['template'];
        $this->remote = isset($options['remote']) ? $options['remote'] : true;
        $this->focusOnError = isset($options['focus_on_error']) ? $options['focus_on_error'] : true;
        $this->animateDuration = isset($options['animate_duration']) ? $options['animate_duration'] : 1000;
    }

    /**
     * Render the specified view with validator data.
     *
     * @param null|\Illuminate\Contracts\View\View|string $view
     * @param null|string $selector
     * @return string
     */
    public function render($template = null, $selector = null) {
        $this->template($template);
        $this->selector($selector);
        $template = new CTemplate($this->template, array('validator' => $this->getTemplateData()));
        $output = $template->render();
        preg_match_all('#<script>(.*?)</script>#ims', $output, $matches);
        $outputJs = '';
        foreach ($matches[1] as $value) {
            $outputJs .= $value;
        }
        return $outputJs;
    }

    /**
     * Get the template data as an array.
     *
     * @return array
     */
    public function toArray() {
        return $this->getTemplateData();
    }

    /**
     * Get the string resulting of render default view.
     *
     * @return string
     */
    public function __toString() {
        try {
            return $this->render();
        } catch (Exception $exception) {
            return trigger_error($exception->__toString(), E_USER_ERROR);
        }
    }

    /**
     * Gets value from view data.
     *
     * @param $name
     * @return string
     *
     * @throws \Proengsoft\JsValidation\Exceptions\PropertyNotFoundException
     */
    public function __get($name) {
        $data = $this->getTemplateData();
        if (!array_key_exists($name, $data)) {
            throw new PropertyNotFoundException($name, get_class());
        }
        return $data[$name];
    }

    /**
     * Gets view data.
     *
     * @return array
     */
    protected function getTemplateData() {
        $this->validator->setRemote($this->remote);
        $data = $this->validator->validationData();
        $data['selector'] = $this->selector;
        if (!is_null($this->ignore)) {
            $data['ignore'] = $this->ignore;
        }
        $data['focus_on_error'] = $this->focusOnError;
        $data['animate_duration'] = $this->animateDuration;
        return $data;
    }

    /**
     * Set the form selector to validate.
     *
     * @param string $selector
     * @deprecated
     */
    public function setSelector($selector) {
        $this->selector = $selector;
    }

    /**
     * Set the form selector to validate.
     *
     * @param string $selector
     * @return \Proengsoft\JsValidation\Javascript\JavascriptValidator
     */
    public function selector($selector) {
        $this->selector = is_null($selector) ? $this->selector : $selector;
        return $this;
    }

    /**
     * Set the input selector to ignore for validation.
     *
     * @param string $ignore
     * @return \Proengsoft\JsValidation\Javascript\JavascriptValidator
     */
    public function ignore($ignore) {
        $this->ignore = $ignore;
        return $this;
    }

    /**
     * Set the view to render Javascript Validations.
     *
     * @param null|\Illuminate\Contracts\View\View|string $view
     * @return \Proengsoft\JsValidation\Javascript\JavascriptValidator
     */
    public function template($template) {
        $this->view = is_null($template) ? $this->template : $template;
        return $this;
    }

    /**
     * Enables or disables remote validations.
     *
     * @param null|bool $enabled
     * @return \Proengsoft\JsValidation\Javascript\JavascriptValidator
     */
    public function remote($enabled = true) {
        $this->remote = $enabled;
        return $this;
    }

    /**
     * Validate Conditional Validations using Ajax in specified fields.
     *
     * @param string $attribute
     * @param string|array $rules
     * @return \Proengsoft\JsValidation\Javascript\JavascriptValidator
     */
    public function sometimes($attribute, $rules) {
        $this->validator->sometimes($attribute, $rules);
        return $this;
    }

}

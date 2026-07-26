<?php

class CElement_FormInput_FileAjax extends CElement_FormInput_File {
    use CElement_Trait_UseViewTrait;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var int
     */
    protected $maxUploadSize;   // in MB

    /**
     * @var array
     */
    protected $allowedExtension;

    /**
     * @var null|callable|CFunction_SerializableClosure
     */
    protected $validationCallback;

    /**
     * @var bool
     */
    protected $disabledUpload;

    /**
     * @var null|string
     */
    protected $tempStorage;

    /**
     * @var bool
     */
    protected $withInfo;

    /**
     * @param string|null $id
     *
     * @return void
     */
    public function __construct($id) {
        parent::__construct($id);
        $this->type = 'file';
        $this->tag = 'div';
        $this->fileName = '';
        $this->withInfo = false;
        $this->acceptFile = '.doc,.docx,.xml,.pdf';
        $this->maxUploadSize = 0;
        $this->allowedExtension = [];
        $this->disabledUpload = false;
        $this->view = 'cresenity/element/form-input/file-ajax';
        $this->onBeforeParse(function (CView_View $view) {
            $ajaxName = $this->name;
            $ajaxName = str_replace('[', '-', $ajaxName);
            $ajaxName = str_replace(']', '-', $ajaxName);

            $ajaxUrl = CAjax::createMethod()->setType(CAjax_Engine_FileUpload::class)
                ->setData('inputName', $ajaxName)
                ->setData('allowedExtension', $this->allowedExtension)
                ->setData('validationCallback', $this->validationCallback)
                ->setData('withInfo', $this->withInfo)
                ->makeUrl();

            $view->with('id', $this->id);
            $view->with('fileName', $this->fileName);
            $view->with('acceptFile', $this->acceptFile);
            $view->with('maxUploadSize', $this->maxUploadSize);
            $view->with('disabledUpload', $this->disabledUpload);
            $view->with('preTag', $this->pretag());
            $view->with('postTag', $this->posttag());
            $view->with('name', $this->name);
            $view->with('value', $this->value);
            $view->with('ajaxName', $ajaxName);
            $view->with('ajaxUrl', $ajaxUrl);
            $view->with('cresConfig', c::json([
                'id' => $this->id,
                'ajaxName' => $ajaxName,
                'ajaxUrl' => $ajaxUrl,
                'maxUploadSize' => $this->maxUploadSize,
                'acceptFile' => $this->acceptFile,
                'messages' => [
                    'acceptFileNotAllowed' => c::__('element/file.errorMessageAcceptFileNotAllowed'),
                    'maxUploadSize' => c::__('element/file.errorMessageMaxUploadSize'),
                    'uploadFailed' => c::__('element/upload.errorMessageUploadFailed'),
                ],
            ]));
        });
    }

    /**
     * @param string $fileName
     *
     * @return $this
     */
    public function setFileName($fileName) {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @param int $size
     *
     * @return $this
     */
    public function setMaxUploadSize($size) {
        $this->maxUploadSize = $size;

        return $this;
    }

    /**
     * @param bool $withInfo
     *
     * @return $this
     */
    public function setWithInfo($withInfo = true) {
        $this->withInfo = $withInfo;

        return $this;
    }

    /**
     * @param string|array $ext
     *
     * @return $this
     */
    public function setAllowedExtension($ext) {
        $arr = $ext;
        if (!is_array($arr)) {
            $arr = [$ext];
        }
        $this->allowedExtension = $arr;

        return $this;
    }

    /**
     * @param callable|Closure $callback
     *
     * @return $this
     */
    public function setValidationCallback($callback) {
        $this->validationCallback = c::toSerializableClosure($callback);

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setDisabledUpload($bool) {
        $this->disabledUpload = $bool;

        return $this;
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function html($indent = 0) {
        $templateHtml = $this->getViewHtml();
        $html = $templateHtml;

        return $html;
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function js($indent = 0) {
        $templateJs = $this->getViewJs();
        $js = $templateJs;

        return $js;
    }

    /**
     * @param string $tempStorage
     *
     * @return $this
     */
    public function setTempStorage($tempStorage) {
        $this->tempStorage = $tempStorage;

        return $this;
    }
}

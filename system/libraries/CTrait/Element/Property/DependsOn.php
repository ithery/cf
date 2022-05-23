<?php
trait CTrait_Element_Property_DependsOn {
    /**
     * @var CElement_Depends_DependsOn[]
     */
    protected $dependsOn = [];

    /**
     * @param CRenderable|string $selector
     * @param callable           $resolver
     * @param array              $options
     *
     * @return $this
     */
    public function setDependsOn($selector, $resolver, array $options = []) {
        $this->dependsOn[] = new CElement_Depends_DependsOn($selector, $resolver, $options);

        return $this;
    }

    /**
     * @return CElement_Depends_DependsOn[]
     */
    public function getDependsOn() {
        return $this->dependsOn;
    }

    public function getDependsOnContentJavascript() {
        $js = new CStringBuilder();
        foreach ($this->dependsOn as $dependOn) {
            //we create ajax method

            $dependsOnSelector = $dependOn->getSelector();
            $targetSelector = '#' . $this->id();
            $ajaxMethod = CAjax::createMethod();
            $ajaxMethod->setType('DependsOn');
            $ajaxMethod->setMethod('post');
            $ajaxMethod->setData('dependsOn', serialize($dependOn));
            $ajaxMethod->setData('from', static::class);
            $ajaxUrl = $ajaxMethod->makeUrl();
            $throttle = $dependOn->getThrottle();
            $optionsJson = '{';
            $optionsJson .= "url:'" . $ajaxUrl . "',";
            $optionsJson .= "method:'" . 'post' . "',";
            $optionsJson .= !$dependOn->getBlock() ? 'block: false,' : '';

            $optionsJson .= "dataAddition: { value: $('" . $dependsOnSelector . "').val() },";
            $optionsJson .= "onSuccess: (data) => {
                let jQueryTarget = $('" . $targetSelector . "');
                jQueryTarget.empty();
                if(typeof data == 'object') {
                    if(typeof data.html === 'undefined') {
                        cresenity.htmlModal(data);
                    } else {
                        jQueryTarget.html(data.html);
                        if (data.js && data.js.length > 0) {
                            let script = cresenity.base64.decode(data.js);
                            eval(script);
                        }
                    }
                } else {
                    jQueryTarget.html(data);
                }


            },";
            $optionsJson .= 'handleJsonResponse: true';
            $optionsJson .= '}';

            $optionsJson = str_replace(["\r\n", "\n", "\r"], '', $optionsJson);

            $dependsOnFunctionName = 'dependsOnFunction' . uniqid();
            $js->appendln('
                 let ' . $dependsOnFunctionName . ' = () => {
                     cresenity.ajax(' . $optionsJson . ");
                 };
                 $('" . $dependsOnSelector . "').change(cresenity.debounce(" . $dependsOnFunctionName . ' ,' . $throttle . '));
                 ' . $dependsOnFunctionName . '();
             ');
        }

        return $js->text();
    }
}

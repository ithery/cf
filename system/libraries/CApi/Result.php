<?php

class CApi_Result {
    protected $result;

    protected $request;

    public function __construct(CApi_HTTP_Request $request, $result) {
        $this->result = $result;
        $this->request = $request;
    }

    /**
     * Morph the API response to the appropriate format.
     *
     * @param string $format
     *
     * @return \CApi_Result
     */
    public function morph() {
        $this->fireMorphingEvent();
        $formatter = c::api($this->request->group())->resultFormatter();
        $transformer = c::api($this->request->group())->transformer();
        if ($transformer && $transformer->transformableResponse($this->result)) {
            $this->result = $transformer->transform($this->result);
        }

        $formatter->setOptions(c::api($this->request->group())->formatOptions($format));

        $defaultContentType = $this->headers->get('Content-Type');

        // If we have no content, we don't want to set this header, as it will be blank
        $contentType = $formatter->getContentType();
        if (!empty($contentType)) {
            $this->headers->set('Content-Type', $formatter->getContentType());
        }

        $this->fireMorphedEvent();

        if ($this->content instanceof CModel) {
            $this->content = $formatter->formatModel($this->content);
        } elseif ($this->content instanceof CModel_Collection) {
            $this->content = $formatter->formatModelCollection($this->content);
        } elseif (is_array($this->content) || $this->content instanceof ArrayObject || $this->content instanceof CInterface_Arrayable) {
            $this->content = $formatter->formatArray($this->content);
        } else {
            if (!empty($defaultContentType)) {
                $this->headers->set('Content-Type', $defaultContentType);
            }
        }

        return $this;
    }

    /**
     * Fire the morphed event.
     *
     * @return void
     */
    protected function fireMorphedEvent() {
        if (!$this->events()) {
            return;
        }

        $this->events()->dispatch(new CApi_Event_ResponseWasMorphed($this, $this->content));
    }

    /**
     * @return CEvent_Dispatcher
     */
    protected function events() {
        return CEvent::dispatcher();
    }

    /**
     * Fire the morphing event.
     *
     * @return void
     */
    protected function fireMorphingEvent() {
        if (!$this->events()) {
            return;
        }

        $this->events()->dispatch(new CApi_Event_ResponseIsMorphing($this, $this->content));
    }
}

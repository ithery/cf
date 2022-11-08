<?php

class CObservable_Listener_Handler_ToastHandler extends CObservable_Listener_Handler {
    use CTrait_Element_Property_Title;

    protected $toastType = 'success';

    protected $message = '';

    public function setMessage($message) {
        $this->message = $message;

        return $this;
    }

    public function setType($type) {
        $this->toastType = $type;

        return $this;
    }

    public function js() {
        $optionsToast = [];

        $js = "toastr['" . $this->toastType . "']('" . $this->message . "', '" . $this->title . "', {
            positionClass: 'toast-top-right',
            closeButton: true,
            progressBar: true,
            preventDuplicates: false,
            newestOnTop: false,

        });";

        return $js;
    }
}

<?php

class CElement_FormInput_Csrf extends CElement_FormInput_Hidden {
    /**
     * @param string $id
     *
     * @return void
     */
    public function __construct($id) {
        parent::__construct($id);

        $this->value = c::csrfToken();
        $this->name = '_token';
    }

    /**
     * @param null|string $id
     *
     * @return self
     */
    public static function factory($id = null) {
        return new CElement_FormInput_Csrf($id);
    }
}

<?php
interface CModel_Contract_CastsInboundAttributesInterface {
    /**
     * Transform the attribute to its underlying model values.
     *
     * @param \CModel $model
     * @param string  $key
     * @param mixed   $value
     * @param array   $attributes
     *
     * @return mixed
     */
    public function set($model, $key, $value, array $attributes);
}

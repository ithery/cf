<?php
interface CModel_Contract_CastableInterface {
    /**
     * Get the name of the caster class to use when casting from / to this cast target.
     *
     * @param array $arguments
     *
     * @return string|\CModel_Contract_CastsAttributesInterface|\CModel_Contract_CastsInboundAttributesInterface
     */
    public static function castUsing(array $arguments);
}

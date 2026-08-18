<?php

trait CApp_Concern_OrgTrait {
    /**
     * @var null|CModel|CApp_Model_Org
     */
    private $org = null;

    /**
     * @var null|int
     */
    private $orgId = null;

    /**
     * Get the model of org.
     *
     * @return CModel|CApp_Model_Org
     */
    public function org() {
        if ($this->org != null) {
            return $this->org;
        }
        $orgId = $this->orgId();

        if (c::filled($orgId) && $this->isAuthEnabled()) {
            $orgModelClass = CF::config('app.model.org', CApp_Model_Org::class);
            if (class_exists($orgModelClass)) {
                return $orgModelClass::find($orgId);
            }
        }

        return null;
    }

    /**
     * @return null|int
     */
    public function orgId() {
        return CApp_Base::orgId();
    }
}

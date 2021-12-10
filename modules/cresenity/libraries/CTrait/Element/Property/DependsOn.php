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
}

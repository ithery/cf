<?php
class CAuth_Event_StopImpersonate {
    use CQueue_Trait_DispatchableTrait;
    use CBroadcast_Trait_InteractWithSocketTrait;
    use CQueue_Trait_SerializesModels;
    /**
     * @var CAuth_AuthenticatableInterface
     */
    public $impersonator;

    /**
     * @var CAuth_AuthenticatableInterface
     */
    public $impersonated;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(CAuth_AuthenticatableInterface $impersonator, CAuth_AuthenticatableInterface $impersonated) {
        $this->impersonator = $impersonator;
        $this->impersonated = $impersonated;
    }
}

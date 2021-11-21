<?php
class CAuth_Access_Event_GateEvaluated {
    /**
     * The authenticatable model.
     *
     * @var null|\CAuth_Authenticatable
     */
    public $user;

    /**
     * The ability being evaluated.
     *
     * @var string
     */
    public $ability;

    /**
     * The result of the evaluation.
     *
     * @var null|bool
     */
    public $result;

    /**
     * The arguments given during evaluation.
     *
     * @var array
     */
    public $arguments;

    /**
     * Create a new event instance.
     *
     * @param null|\CAuth_Authenticatable $user
     * @param string                      $ability
     * @param null|bool                   $result
     * @param array                       $arguments
     *
     * @return void
     */
    public function __construct($user, $ability, $result, $arguments) {
        $this->user = $user;
        $this->ability = $ability;
        $this->result = $result;
        $this->arguments = $arguments;
    }
}

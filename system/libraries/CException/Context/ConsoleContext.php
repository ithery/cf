<?php

class CException_Context_ConsoleContext extends CException_ContextAbstract implements CException_Contract_ContextInterface {
    /**
     * @var array
     */
    private $arguments = [];

    public function __construct(array $arguments = []) {
        $this->arguments = $arguments;
    }

    public function toArray() {
        return [
            'arguments' => $this->arguments,
            'git' => $this->getGit(),
        ];
    }
}

<?php

use Illuminate\Contracts\Support\Arrayable;

class CException_Solution_SolutionTransformer implements Arrayable {
    protected $solution;

    public function __construct(CException_Contract_SolutionInterface $solution) {
        $this->solution = $solution;
    }

    public function toArray() {
        return [
            'class' => get_class($this->solution),
            'title' => $this->solution->getSolutionTitle(),
            'description' => $this->solution->getSolutionDescription(),
            'links' => $this->solution->getDocumentationLinks(),
            'is_runnable' => false,
        ];
    }
}

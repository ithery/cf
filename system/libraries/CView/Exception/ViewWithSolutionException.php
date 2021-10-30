<?php

class CView_Exception_ViewWithSolutionException extends CView_Exception_ViewException implements CException_Contract_ProvideSolutionInterface {
    protected $solution;

    public function setSolution(CException_Contract_SolutionInterface $solution) {
        $this->solution = $solution;
    }

    public function getSolution() {
        return $this->solution;
    }
}

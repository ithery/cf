<?php
class CException_Solution implements CException_Contract_SolutionInterface {
    protected $title;

    protected $description = '';

    protected $links = [];

    public static function create($title = '') {
        return new static($title);
    }

    public function __construct($title = '') {
        $this->title = $title;
    }

    public function getSolutionTitle() {
        return $this->title;
    }

    public function setSolutionTitle($title) {
        $this->title = $title;

        return $this;
    }

    public function getSolutionDescription() {
        return $this->description;
    }

    public function setSolutionDescription($description) {
        $this->description = $description;

        return $this;
    }

    public function getDocumentationLinks() {
        return $this->links;
    }

    public function setDocumentationLinks(array $links) {
        $this->links = $links;

        return $this;
    }
}

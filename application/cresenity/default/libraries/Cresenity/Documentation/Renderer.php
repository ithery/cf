<?php

namespace Cresenity\Documentation;

class Renderer {
    protected $html;

    protected $h3List;

    public function __construct($html) {
        $this->html = $html;
        $this->h3List = [];
        $this->run();
    }

    private function run() {
        $this->h3List = $this->getTextBetweenTags($this->html, 'h3', function ($matches) {
            return '<h3 id="section-' . \cstr::slug(trim($matches[1]), '-') . '">' . trim($matches[1]) . '</h3>';
        });
    }

    private function getTextBetweenTags(&$string, $tagname, $replaceCallback = null) {
        $pattern = "/<$tagname ?.*>(.*)<\/$tagname>/";
        $result = [];
        $string = \preg_replace_callback($pattern, function ($matches) use ($replaceCallback, &$result) {
            $result[] = trim($matches[1]);
            if ($replaceCallback != null && \is_callable($replaceCallback)) {
                return $replaceCallback($matches);
            }
            return $matches[0];
        }, $string);
        return $result;
    }

    public function getH3List() {
        return $this->h3List;
    }

    public function getHtml() {
        return $this->html;
    }
}

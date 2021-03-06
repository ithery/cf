<?php

class CEmail_Builder_Parser_CmlParser {
    use CEmail_Builder_Parser_CmlParserSubscriberTrait;

    /**
     * @var string
     */
    protected $cml;

    protected $components = [];

    protected $addEmptyAttributes = true;

    protected $convertBooleans = true;

    protected $keepComments = true;

    protected $filePath = '.';

    protected $ignoreIncludes = false;

    protected $currentEndingTagIndexes = [];

    protected $endingTags = [];

    protected $inEndingTag = 0;

    protected $currentNode = null;

    protected $inInclude = false;

    protected $includedIn = [];

    protected $parentNode = null;

    protected $parser = null;

    protected $lineIndexes = [];

    public function __construct($cml, $options = [], $includedIn = []) {
        $defaultOptions = [
            'addEmptyAttributes' => true,
            'components' => [],
            'convertBooleans' => true,
            'keepComments' => true,
            'filePath' => '.',
            'ignoreIncludes' => false,
        ];
        $options = array_merge($defaultOptions, $options);
        $this->cml = $cml;
        $this->addEmptyAttributes = carr::get($options, 'addEmptyAttributes', true);
        $this->components = carr::get($options, 'components', []);
        $this->convertBooleans = carr::get($options, 'convertBooleans', true);
        $this->keepComments = carr::get($options, 'keepComments', true);
        $this->filePath = carr::get($options, 'filePath', '.');
        $this->ignoreIncludes = carr::get($options, 'ignoreIncludes', false);
        $this->inEndingTag = 0;
        $this->includedIn = $includedIn;
        $this->inInclude = count($includedIn) > 0;
        $this->currentEndingTagIndexes = [
            'startIndex' => 0,
            'endIndex' => 0,
        ];
        $this->endingTags = carr::map(carr::filter(CEmail::builder()->instance()->components(), function ($component) {
            return $component::isEndingTag();
        }), function ($component) {
            return $component::getTagName();
        });

        $this->lineIndexes = [];
        $posLine = -1;
        while (($posLine = strpos($this->cml, "\n", $posLine + 1)) !== false) {
            $this->lineIndexes[] = $posLine;
        }
    }

    protected function findTag($tagName, $tree) {
        return carr::find($tree->children(), ['tagName' => $tagName]);
    }

    public function parse() {
        $options = [];
        $options['recognizeCDATA'] = true;
        $options['decodeEntities'] = false;
        $options['recognizeSelfClosing'] = true;
        $options['lowerCaseAttributeNames'] = false;

        $this->parser = CParser::createHtmlParser($options);
        $this->parser->listen(CParser_HtmlParser_Event_OnOpenTag::class, [$this, 'onOpenTag']);
        $this->parser->listen(CParser_HtmlParser_Event_OnCloseTag::class, [$this, 'onCloseTag']);
        $this->parser->listen(CParser_HtmlParser_Event_OnText::class, [$this, 'onText']);

        $this->parser->write($this->cml);
        $this->parser->end();

        //cdbg::dd($this->parentNode);
        return $this->parentNode;
    }

    public function convertBooleansOnAttrs($attrs) {
        return carr::mapRecursive(function ($val) {
            if ($val === 'true') {
                return true;
            }
            if ($val === 'false') {
                return false;
            }

            return $val;
        }, $attrs);
    }

    public function resolvePath($path) {
        return $path;
    }

    public function isSelfClosing($indexes, $parser) {
        return $indexes['startIndex'] === $parser->getStartIndex()
                && $indexes['endIndex'] === $parser->getEndIndex();
    }
}

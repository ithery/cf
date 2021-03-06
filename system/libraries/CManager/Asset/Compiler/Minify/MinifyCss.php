<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Aug 11, 2020
 */
class CManager_Asset_Compiler_Minify_MinifyCss extends CManager_Asset_Compiler_MinifyAbstract {
    /**
     * @var int maximum inport size in kB
     */
    protected $maxImportSize = 5;

    /**
     * @var string[] valid import extensions
     */
    protected $importExtensions = [
        'gif' => 'data:image/gif',
        'png' => 'data:image/png',
        'jpe' => 'data:image/jpeg',
        'jpg' => 'data:image/jpeg',
        'jpeg' => 'data:image/jpeg',
        'svg' => 'data:image/svg+xml',
        'woff' => 'data:application/x-font-woff',
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
        'xbm' => 'image/x-xbitmap',
    ];

    /**
     * Minify the data.
     * Perform CSS optimizations.
     *
     * @param string   $content
     * @param string[] $parents Parent paths, for circular reference checks
     *
     * @return string The minified data
     */
    public function execute($content, $parents = []) {
        /*
         * Let's first take out strings & comments, since we can't just
         * remove whitespace anywhere. If whitespace occurs inside a string,
         * we should leave it alone. E.g.:
         * p { content: "a   test" }
         */
        $this->extractStrings();
        $this->stripComments();
        $this->extractCalcs();
        $content = $this->replace($content);

        $content = $this->stripWhitespace($content);
        $content = $this->shortenColors($content);
        $content = $this->shortenZeroes($content);
        $content = $this->shortenFontWeights($content);
        $content = $this->stripEmptyTags($content);

        // restore the string we've extracted earlier
        $content = $this->restoreExtractedData($content);

        //$source = is_int($source) ? '' : $source;
        //$parents = $source ? array_merge($parents, array($source)) : $parents;
        //$content = $this->combineImports($source, $content, $parents);
        //$content = $this->importFiles($source, $content);

        //$content = $this->moveImportsToTop($content);

        return $content;
    }

    /**
     * Set the maximum size if files to be imported.
     *
     * Files larger than this size (in kB) will not be imported into the CSS.
     * Importing files into the CSS as data-uri will save you some connections,
     * but we should only import relatively small decorative images so that our
     * CSS file doesn't get too bulky.
     *
     * @param int $size Size in kB
     */
    public function setMaxImportSize($size) {
        $this->maxImportSize = $size;
    }

    /**
     * Set the type of extensions to be imported into the CSS (to save network
     * connections).
     * Keys of the array should be the file extensions & respective values
     * should be the data type.
     *
     * @param string[] $extensions Array of file extensions
     */
    public function setImportExtensions(array $extensions) {
        $this->importExtensions = $extensions;
    }

    /**
     * Move any import statements to the top.
     *
     * @param string $content Nearly finished CSS content
     *
     * @return string
     */
    protected function moveImportsToTop($content) {
        if (preg_match_all('/(;?)(@import (?<url>url\()?(?P<quotes>["\']?).+?(?P=quotes)(?(url)\)));?/', $content, $matches)) {
            // remove from content
            foreach ($matches[0] as $import) {
                $content = str_replace($import, '', $content);
            }

            // add to top
            $content = implode(';', $matches[2]) . ';' . trim($content, ';');
        }

        return $content;
    }

    /**
     * Combine CSS from import statements.
     *
     * @param string   $source  The file to combine imports for
     * @param string   $content The CSS content to combine imports for
     * @param string[] $parents Parent paths, for circular reference checks
     *
     * @throws FileImportException
     *
     * @return string
     */
    protected function combineImports($source, $content, $parents) {
        $importRegexes = [
            // @import url(xxx)
            '/
            # import statement
            @import
            # whitespace
            \s+
                # open url()
                url\(
                    # (optional) open path enclosure
                    (?P<quotes>["\']?)
                        # fetch path
                        (?P<path>.+?)
                    # (optional) close path enclosure
                    (?P=quotes)
                # close url()
                \)
                # (optional) trailing whitespace
                \s*
                # (optional) media statement(s)
                (?P<media>[^;]*)
                # (optional) trailing whitespace
                \s*
            # (optional) closing semi-colon
            ;?
            /ix',
            // @import 'xxx'
            '/
            # import statement
            @import
            # whitespace
            \s+
                # open path enclosure
                (?P<quotes>["\'])
                    # fetch path
                    (?P<path>.+?)
                # close path enclosure
                (?P=quotes)
                # (optional) trailing whitespace
                \s*
                # (optional) media statement(s)
                (?P<media>[^;]*)
                # (optional) trailing whitespace
                \s*
            # (optional) closing semi-colon
            ;?
            /ix',
        ];

        // find all relative imports in css
        $matches = [];
        foreach ($importRegexes as $importRegex) {
            if (preg_match_all($importRegex, $content, $regexMatches, PREG_SET_ORDER)) {
                $matches = array_merge($matches, $regexMatches);
            }
        }

        $search = [];
        $replace = [];

        // loop the matches
        foreach ($matches as $match) {
            // get the path for the file that will be imported
            $importPath = dirname($source) . '/' . $match['path'];

            // only replace the import with the content if we can grab the
            // content of the file
            if (!$this->canImportByPath($match['path']) || !$this->canImportFile($importPath)) {
                continue;
            }

            // check if current file was not imported previously in the same
            // import chain.
            if (in_array($importPath, $parents)) {
                throw new CManager_Asset_Compiler_Exception_FileImportException('Failed to import file "' . $importPath . '": circular reference detected.');
            }

            // grab referenced file & minify it (which may include importing
            // yet other @import statements recursively)
            $minifier = new static($importPath);
            $minifier->setMaxImportSize($this->maxImportSize);
            $minifier->setImportExtensions($this->importExtensions);
            $importContent = $minifier->execute($source, $parents);

            // check if this is only valid for certain media
            if (!empty($match['media'])) {
                $importContent = '@media ' . $match['media'] . '{' . $importContent . '}';
            }

            // add to replacement array
            $search[] = $match[0];
            $replace[] = $importContent;
        }

        // replace the import statements
        return str_replace($search, $replace, $content);
    }

    /**
     * Import files into the CSS, base64-ized.
     *
     * url(image.jpg) images will be loaded and their content merged into the
     * original file, to save HTTP requests.
     *
     * @param string $source  The file to import files for
     * @param string $content The CSS content to import files for
     *
     * @return string
     */
    protected function importFiles($source, $content) {
        $regex = '/url\((["\']?)(.+?)\\1\)/i';
        if ($this->importExtensions && preg_match_all($regex, $content, $matches, PREG_SET_ORDER)) {
            $search = [];
            $replace = [];

            // loop the matches
            foreach ($matches as $match) {
                $extension = substr(strrchr($match[2], '.'), 1);
                if ($extension && !array_key_exists($extension, $this->importExtensions)) {
                    continue;
                }

                // get the path for the file that will be imported
                $path = $match[2];
                $path = dirname($source) . '/' . $path;

                // only replace the import with the content if we're able to get
                // the content of the file, and it's relatively small
                if ($this->canImportFile($path) && $this->canImportBySize($path)) {
                    // grab content && base64-ize
                    $importContent = $this->load($path);
                    $importContent = base64_encode($importContent);

                    // build replacement
                    $search[] = $match[0];
                    $replace[] = 'url(' . $this->importExtensions[$extension] . ';base64,' . $importContent . ')';
                }
            }

            // replace the import statements
            $content = str_replace($search, $replace, $content);
        }

        return $content;
    }

    /**
     * Shorthand hex color codes.
     * #FF0000 -> #F00.
     *
     * @param string $content The CSS content to shorten the hex color codes for
     *
     * @return string
     */
    protected function shortenColors($content) {
        $content = preg_replace('/(?<=[: ])#([0-9a-z])\\1([0-9a-z])\\2([0-9a-z])\\3(?:([0-9a-z])\\4)?(?=[; }])/i', '#$1$2$3$4', $content);

        // remove alpha channel if it's pointless...
        $content = preg_replace('/(?<=[: ])#([0-9a-z]{6})ff?(?=[; }])/i', '#$1', $content);
        $content = preg_replace('/(?<=[: ])#([0-9a-z]{3})f?(?=[; }])/i', '#$1', $content);

        $colors = [
            // we can shorten some even more by replacing them with their color name
            '#F0FFFF' => 'azure',
            '#F5F5DC' => 'beige',
            '#A52A2A' => 'brown',
            '#FF7F50' => 'coral',
            '#FFD700' => 'gold',
            '#808080' => 'gray',
            '#008000' => 'green',
            '#4B0082' => 'indigo',
            '#FFFFF0' => 'ivory',
            '#F0E68C' => 'khaki',
            '#FAF0E6' => 'linen',
            '#800000' => 'maroon',
            '#000080' => 'navy',
            '#808000' => 'olive',
            '#CD853F' => 'peru',
            '#FFC0CB' => 'pink',
            '#DDA0DD' => 'plum',
            '#800080' => 'purple',
            '#F00' => 'red',
            '#FA8072' => 'salmon',
            '#A0522D' => 'sienna',
            '#C0C0C0' => 'silver',
            '#FFFAFA' => 'snow',
            '#D2B48C' => 'tan',
            '#FF6347' => 'tomato',
            '#EE82EE' => 'violet',
            '#F5DEB3' => 'wheat',
            // or the other way around
            'WHITE' => '#fff',
            'BLACK' => '#000',
        ];

        return preg_replace_callback(
            '/(?<=[: ])(' . implode('|', array_keys($colors)) . ')(?=[; }])/i',
            function ($match) use ($colors) {
                return $colors[strtoupper($match[0])];
            },
            $content
        );
    }

    /**
     * Shorten CSS font weights.
     *
     * @param string $content The CSS content to shorten the font weights for
     *
     * @return string
     */
    protected function shortenFontWeights($content) {
        $weights = [
            'normal' => 400,
            'bold' => 700,
        ];

        $callback = function ($match) use ($weights) {
            return $match[1] . $weights[$match[2]];
        };

        return preg_replace_callback('/(font-weight\s*:\s*)(' . implode('|', array_keys($weights)) . ')(?=[;}])/', $callback, $content);
    }

    /**
     * Shorthand 0 values to plain 0, instead of e.g. -0em.
     *
     * @param string $content The CSS content to shorten the zero values for
     *
     * @return string
     */
    protected function shortenZeroes($content) {
        // we don't want to strip units in `calc()` expressions:
        // `5px - 0px` is valid, but `5px - 0` is not
        // `10px * 0` is valid (equates to 0), and so is `10 * 0px`, but
        // `10 * 0` is invalid
        // we've extracted calcs earlier, so we don't need to worry about this
        // reusable bits of code throughout these regexes:
        // before & after are used to make sure we don't match lose unintended
        // 0-like values (e.g. in #000, or in http://url/1.0)
        // units can be stripped from 0 values, or used to recognize non 0
        // values (where wa may be able to strip a .0 suffix)
        $before = '(?<=[:(, ])';
        $after = '(?=[ ,);}])';
        $units = '(em|ex|%|px|cm|mm|in|pt|pc|ch|rem|vh|vw|vmin|vmax|vm)';

        // strip units after zeroes (0px -> 0)
        // NOTE: it should be safe to remove all units for a 0 value, but in
        // practice, Webkit (especially Safari) seems to stumble over at least
        // 0%, potentially other units as well. Only stripping 'px' for now.
        // @see https://github.com/matthiasmullie/minify/issues/60
        $content = preg_replace('/' . $before . '(-?0*(\.0+)?)(?<=0)px' . $after . '/', '\\1', $content);

        // strip 0-digits (.0 -> 0)
        $content = preg_replace('/' . $before . '\.0+' . $units . '?' . $after . '/', '0\\1', $content);
        // strip trailing 0: 50.10 -> 50.1, 50.10px -> 50.1px
        $content = preg_replace('/' . $before . '(-?[0-9]+\.[0-9]+)0+' . $units . '?' . $after . '/', '\\1\\2', $content);
        // strip trailing 0: 50.00 -> 50, 50.00px -> 50px
        $content = preg_replace('/' . $before . '(-?[0-9]+)\.0+' . $units . '?' . $after . '/', '\\1\\2', $content);
        // strip leading 0: 0.1 -> .1, 01.1 -> 1.1
        $content = preg_replace('/' . $before . '(-?)0+([0-9]*\.[0-9]+)' . $units . '?' . $after . '/', '\\1\\2\\3', $content);

        // strip negative zeroes (-0 -> 0) & truncate zeroes (00 -> 0)
        $content = preg_replace('/' . $before . '-?0+' . $units . '?' . $after . '/', '0\\1', $content);

        // IE doesn't seem to understand a unitless flex-basis value (correct -
        // it goes against the spec), so let's add it in again (make it `%`,
        // which is only 1 char: 0%, 0px, 0 anything, it's all just the same)
        // @see https://developer.mozilla.org/nl/docs/Web/CSS/flex
        $content = preg_replace('/flex:([0-9]+\s[0-9]+\s)0([;\}])/', 'flex:${1}0%${2}', $content);
        $content = preg_replace('/flex-basis:0([;\}])/', 'flex-basis:0%${1}', $content);

        return $content;
    }

    /**
     * Strip empty tags from source code.
     *
     * @param string $content
     *
     * @return string
     */
    protected function stripEmptyTags($content) {
        $content = preg_replace('/(?<=^)[^\{\};]+\{\s*\}/', '', $content);
        $content = preg_replace('/(?<=(\}|;))[^\{\};]+\{\s*\}/', '', $content);

        return $content;
    }

    /**
     * Strip comments from source code.
     */
    protected function stripComments() {
        // PHP only supports $this inside anonymous functions since 5.4
        $minifier = $this;
        $callback = function ($match) use ($minifier) {
            $count = count($minifier->extracted);
            $placeholder = '/*' . $count . '*/';
            $minifier->extracted[$placeholder] = $match[0];

            return $placeholder;
        };
        $this->registerPattern('/\n?\/\*(!|.*?@license|.*?@preserve).*?\*\/\n?/s', $callback);

        $this->registerPattern('/\/\*.*?\*\//s', '');
    }

    /**
     * Strip whitespace.
     *
     * @param string $content The CSS content to strip the whitespace for
     *
     * @return string
     */
    protected function stripWhitespace($content) {
        // remove leading & trailing whitespace
        $content = preg_replace('/^\s*/m', '', $content);
        $content = preg_replace('/\s*$/m', '', $content);

        // replace newlines with a single space
        $content = preg_replace('/\s+/', ' ', $content);

        // remove whitespace around meta characters
        // inspired by stackoverflow.com/questions/15195750/minify-compress-css-with-regex
        $content = preg_replace('/\s*([\*$~^|]?+=|[{};,>~]|!important\b)\s*/', '$1', $content);
        $content = preg_replace('/([\[(:>\+])\s+/', '$1', $content);
        $content = preg_replace('/\s+([\]\)>\+])/', '$1', $content);
        $content = preg_replace('/\s+(:)(?![^\}]*\{)/', '$1', $content);

        // whitespace around + and - can only be stripped inside some pseudo-
        // classes, like `:nth-child(3+2n)`
        // not in things like `calc(3px + 2px)`, shorthands like `3px -2px`, or
        // selectors like `div.weird- p`
        $pseudos = ['nth-child', 'nth-last-child', 'nth-last-of-type', 'nth-of-type'];
        $content = preg_replace('/:(' . implode('|', $pseudos) . ')\(\s*([+-]?)\s*(.+?)\s*([+-]?)\s*(.*?)\s*\)/', ':$1($2$3$4$5)', $content);

        // remove semicolon/whitespace followed by closing bracket
        $content = str_replace(';}', '}', $content);

        return trim($content);
    }

    /**
     * Replace all `calc()` occurrences.
     */
    protected function extractCalcs() {
        // PHP only supports $this inside anonymous functions since 5.4
        $minifier = $this;
        $callback = function ($match) use ($minifier) {
            $length = strlen($match[1]);
            $expr = '';
            $opened = 0;

            for ($i = 0; $i < $length; $i++) {
                $char = $match[1][$i];
                $expr .= $char;
                if ($char === '(') {
                    $opened++;
                } elseif ($char === ')' && --$opened === 0) {
                    break;
                }
            }
            $rest = str_replace($expr, '', $match[1]);
            $expr = trim(substr($expr, 1, -1));

            $count = count($minifier->extracted);
            $placeholder = 'calc(' . $count . ')';
            $minifier->extracted[$placeholder] = 'calc(' . $expr . ')';

            return $placeholder . $rest;
        };

        $this->registerPattern('/calc(\(.+?)(?=$|;|}|calc\()/', $callback);
        $this->registerPattern('/calc(\(.+?)(?=$|;|}|calc\()/m', $callback);
    }

    /**
     * Check if file is small enough to be imported.
     *
     * @param string $path The path to the file
     *
     * @return bool
     */
    protected function canImportBySize($path) {
        return ($size = @filesize($path)) && $size <= $this->maxImportSize * 1024;
    }

    /**
     * Check if file a file can be imported, going by the path.
     *
     * @param string $path
     *
     * @return bool
     */
    protected function canImportByPath($path) {
        return preg_match('/^(data:|https?:|\\/)/', $path) === 0;
    }
}

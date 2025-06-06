<?php

use CRunner_WkHtmlToPdf_TmpFile as File;
use CRunner_WkHtmlToPdf_Command as Command;

class CServer_Runner_WkHtmlToPdf_Pdf {
    // Type hints for `addPage()` and `addCover()`
    const TYPE_HTML = 'html';

    const TYPE_XML = 'xml';

    // Regular expression to detect HTML strings
    const REGEX_HTML = '/<(?:!doctype )?html/i';

    // Regular expression to detect XML strings
    const REGEX_XML = '/<\??xml/i';

    // Regular expression to detect URL strings
    const REGEX_URL = '/^(https?:)?\/\//i';

    // Regular expression to detect options that expect an URL or a file name,
    // so we need to create a tmp file for the content.
    const REGEX_OPTS_TMPFILE = '/^((header|footer)-html|(xsl|user)-style-sheet)$/i';

    // Prefix for tmp files
    const TMP_PREFIX = 'tmp_wkhtmlto_pdf_';

    // Maximum length of a file path if PHP_MAXPATHLEN is not defined
    const MAX_PATHLEN = 255;

    /**
     * @var string the name of the `wkhtmltopdf` binary. Default is
     *             `wkhtmltopdf`. You can also configure a full path here.
     */
    public $binary = 'wkhtmltopdf';

    /**
     * @var array options to pass to the Command constructor. Default is none.
     */
    public $commandOptions = [];

    /**
     * @var null|string the directory to use for temporary files. If null
     *                  (default) the dir is autodetected.
     */
    public $tmpDir;

    /**
     * @var bool whether to ignore any errors if some PDF file was still
     *           created. Default is false.
     */
    public $ignoreWarnings = false;

    /**
     * @var bool whether the old version 9 of wkhtmltopdf is used (slightly
     *           different syntax). Default is false.
     */
    public $version9 = false;

    /**
     * @var bool whether the PDF was created
     */
    protected $isCreated = false;

    /**
     * @var array global options for `wkhtmltopdf` as `['--opt1', '--opt2' =>
     *            'val', ...]`
     */
    protected $options = [];

    /**
     * @var array list of wkhtmltopdf objects as arrays
     */
    protected $objects = [];

    /**
     * @var \CRunner_WkHtmlToPdf_TmpFile the temporary PDF file
     */
    protected $tmpPdfFile;

    /**
     * @var \CRunner_WkHtmlToPdf_TmpFile[] list of tmp file objects. This is here to
     *                                     keep a reference to `File` and thus avoid too early call of
     *                                     [[File::__destruct]] if the file is not referenced anymore.
     */
    protected $tmpFiles = [];

    /**
     * @var CRunner_WkHtmlToPdf_Command the command instance that executes wkhtmltopdf
     */
    protected $command;

    /**
     * @var string the detailed error message. Empty string if none.
     */
    protected $error = '';

    /**
     * @param array|string $options global options for wkhtmltopdf, a page URL,
     *                              a HTML string or a filename
     */
    public function __construct($options = null) {
        if (is_array($options)) {
            $this->setOptions($options);
        } elseif (is_string($options)) {
            $this->addPage($options);
        }
    }

    /**
     * Add a page object to the output.
     *
     * @param string      $input   either a URL, a HTML string or a filename
     * @param array       $options optional options for this page
     * @param null|string $type    a type hint if the input is a string of known
     *                             type. This can either be `TYPE_HTML` or `TYPE_XML`. If `null` (default)
     *                             the type is auto detected from the string content.
     *
     * @return static the Pdf instance for method chaining
     */
    public function addPage($input, $options = [], $type = null) {
        $options['inputArg'] = $this->ensureUrlOrFile($input, $type);
        $this->objects[] = $this->ensureUrlOrFileOptions($options);

        return $this;
    }

    /**
     * Add a cover page object to the output.
     *
     * @param string      $input   either a URL, a HTML string or a filename
     * @param array       $options optional options for the cover page
     * @param null|string $type    a type hint if the input is a string of known
     *                             type. This can either be `TYPE_HTML` or `TYPE_XML`. If `null` (default)
     *                             the type is auto detected from the string content.
     *
     * @return static the Pdf instance for method chaining
     */
    public function addCover($input, $options = [], $type = null) {
        $options['input'] = ($this->version9 ? '--' : '') . 'cover';
        $options['inputArg'] = $this->ensureUrlOrFile($input, $type);
        $this->objects[] = $this->ensureUrlOrFileOptions($options);

        return $this;
    }

    /**
     * Add a TOC object to the output.
     *
     * @param array $options optional options for the table of contents
     *
     * @return static the Pdf instance for method chaining
     */
    public function addToc($options = []) {
        $options['input'] = ($this->version9 ? '--' : '') . 'toc';
        $this->objects[] = $this->ensureUrlOrFileOptions($options);

        return $this;
    }

    /**
     * Save the PDF to given filename (triggers PDF creation).
     *
     * @param string $filename to save PDF as
     *
     * @return bool whether PDF was created successfully
     */
    public function saveAs($filename) {
        if (!$this->isCreated && !$this->createPdf()) {
            return false;
        }
        if (!$this->tmpPdfFile->saveAs($filename)) {
            $this->error = "Could not save PDF as '$filename'";

            return false;
        }

        return true;
    }

    /**
     * Send PDF to client, either inline or as download (triggers PDF creation).
     *
     * @param null|string $filename the filename to send. If empty, the PDF is
     *                              streamed inline.
     * @param bool        $inline   whether to force inline display of the PDF, even if
     *                              filename is present
     *
     * @return bool whether PDF was created successfully
     */
    public function send($filename = null, $inline = false) {
        if (!$this->isCreated && !$this->createPdf()) {
            return false;
        }
        $this->tmpPdfFile->send($filename, 'application/pdf', $inline);

        return true;
    }

    /**
     * Get the raw PDF contents (triggers PDF creation).
     *
     * @return string|bool the PDF content as a string or `false` if the PDF
     *                     wasn't created successfully
     */
    public function toString() {
        if (!$this->isCreated && !$this->createPdf()) {
            return false;
        }

        return file_get_contents($this->tmpPdfFile->getFileName());
    }

    /**
     * Set global option(s).
     *
     * @param array $options list of global PDF options to set as name/value pairs
     *
     * @return static the Pdf instance for method chaining
     */
    public function setOptions($options = []) {
        // #264 tmpDir must be set before calling ensureUrlOrFileOptions
        if (isset($options['tmpDir'])) {
            $this->tmpDir = $options['tmpDir'];
            unset($options['tmpDir']);
        }
        $options = $this->ensureUrlOrFileOptions($options);
        foreach ($options as $key => $val) {
            if (is_int($key)) {
                $this->options[] = $val;
            } elseif ($key[0] !== '_' && property_exists($this, $key)) {
                $this->$key = $val;
            } else {
                $this->options[$key] = $val;
            }
        }

        return $this;
    }

    /**
     * @return Command the command instance that executes wkhtmltopdf
     */
    public function getCommand() {
        if ($this->command === null) {
            $options = $this->commandOptions;
            if (!isset($options['command'])) {
                $options['command'] = $this->binary;
            }
            $this->command = new Command($options);
        }

        return $this->command;
    }

    /**
     * @return string the detailed error message. Empty string if none.
     */
    public function getError() {
        return $this->error;
    }

    /**
     * @return string the filename of the temporary PDF file
     */
    public function getPdfFilename() {
        if ($this->tmpPdfFile === null) {
            $this->tmpPdfFile = new File('', '.pdf', self::TMP_PREFIX, $this->tmpDir);
        }

        return $this->tmpPdfFile->getFileName();
    }

    /**
     * Run the Command to create the tmp PDF file.
     *
     * @return bool whether creation was successful
     */
    protected function createPdf() {
        if ($this->isCreated) {
            return false;
        }
        $command = $this->getCommand();
        $fileName = $this->getPdfFilename();

        $command->addArgs($this->options);
        foreach ($this->objects as $object) {
            $command->addArgs($object);
        }
        $command->addArg($fileName, null, true);    // Always escape filename
        if (!$command->execute()) {
            $this->error = $command->getError();
            if (!(file_exists($fileName) && filesize($fileName) !== 0 && $this->ignoreWarnings)) {
                return false;
            }
        }
        $this->isCreated = true;

        return true;
    }

    /**
     * This method creates a temporary file if the passed argument is neither a
     * File instance or URL nor contains XML or HTML and is also not a valid
     * file name.
     *
     * @param string|File $input the input argument File to check
     * @param null|string $type  a type hint if the input is a string of known
     *                           type. This can either be `TYPE_HTML` or `TYPE_XML`. If `null` (default)
     *                           the type is auto detected from the string content.
     *
     * @return \mikehaertl\tmp\File|string a File object if the input is a HTML
     *                                     or XML string. The unchanged input otherwhise.
     */
    protected function ensureUrlOrFile($input, $type = null) {
        if ($input instanceof File) {
            $this->tmpFiles[] = $input;

            return $input;
        } elseif (preg_match(self::REGEX_URL, $input)) {
            return $input;
        } elseif ($type === self::TYPE_XML || $type === null && preg_match(self::REGEX_XML, $input)) {
            $ext = '.xml';
        } else {
            // First check for obvious HTML content to avoid is_file() as much
            // as possible as it can trigger open_basedir restriction warnings
            // with long strings.
            $isHtml = $type === self::TYPE_HTML || preg_match(self::REGEX_HTML, $input);
            if (!$isHtml) {
                $maxPathLen = defined('PHP_MAXPATHLEN')
                        ? constant('PHP_MAXPATHLEN') : self::MAX_PATHLEN;
                if (strlen($input) <= $maxPathLen && is_file($input)) {
                    return $input;
                }
            }
            $ext = '.html';
        }
        $file = new File($input, $ext, self::TMP_PREFIX, $this->tmpDir);
        $this->tmpFiles[] = $file;

        return $file;
    }

    /**
     * @param array $options list of options as name/value pairs
     *
     * @return array options with raw HTML/XML/String content converted to tmp
     *               files where neccessary
     */
    protected function ensureUrlOrFileOptions($options = []) {
        foreach ($options as $key => $val) {
            // Some options expect a URL or a file name, so check if we need a temp file
            if (is_string($val) && preg_match(self::REGEX_OPTS_TMPFILE, $key)) {
                $file = $this->ensureUrlOrFile($val);
                if ($file instanceof File) {
                    $options[$key] = $file;
                }
            }
        }

        return $options;
    }
}

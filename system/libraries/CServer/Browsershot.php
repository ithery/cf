<?php

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * @mixin \CImage_Manipulations
 *
 * @see CServer
 */
class CServer_Browsershot {
    const POLLING_REQUEST_ANIMATION_FRAME = 'raf';

    const POLLING_MUTATION = 'mutation';

    protected $nodeBinary = null;

    protected $npmBinary = null;

    protected $nodeModulePath = null;

    protected $includePath = '$PATH:/usr/local/bin:/opt/homebrew/bin';

    protected $binPath = null;

    protected $html = '';

    protected $noSandbox = false;

    protected $proxyServer = '';

    protected $showBackground = false;

    protected $showScreenshotBackground = true;

    protected $scale = null;

    protected $screenshotType = 'png';

    protected $screenshotQuality = null;

    protected $temporaryHtmlDirectory;

    protected $timeout = 60;

    protected $transparentBackground = false;

    protected $url = '';

    protected $postParams = [];

    protected $additionalOptions = [];

    protected $temporaryOptionsDirectory;

    protected $writeOptionsToFile = false;

    protected $chromiumArguments = [];

    /**
     * @var \CImage_Manipulations
     */
    protected $imageManipulations;

    /**
     * @param string $url
     *
     * @return static
     */
    public static function url($url) {
        return (new static())->setUrl($url);
    }

    /**
     * @param string $html
     *
     * @return static
     */
    public static function html($html) {
        return (new static())->setHtml($html);
    }

    /**
     * @param string $url
     * @param bool   $deviceEmulate
     */
    public function __construct($url = '', $deviceEmulate = false) {
        $this->url = $url;

        $this->imageManipulations = new CImage_Manipulations();

        if (!$deviceEmulate) {
            $this->windowSize(800, 600);
        }
    }

    /**
     * @param string $nodeBinary
     *
     * @return $this
     */
    public function setNodeBinary($nodeBinary) {
        $this->nodeBinary = $nodeBinary;

        return $this;
    }

    /**
     * @param string $npmBinary
     *
     * @return $this
     */
    public function setNpmBinary($npmBinary) {
        $this->npmBinary = $npmBinary;

        return $this;
    }

    public function setIncludePath(string $includePath) {
        $this->includePath = $includePath;

        return $this;
    }

    /**
     * @param string $binPath
     *
     * @return $this
     */
    public function setBinPath($binPath) {
        $this->binPath = $binPath;

        return $this;
    }

    /**
     * @param string $nodeModulePath
     *
     * @return $this
     */
    public function setNodeModulePath($nodeModulePath) {
        $this->nodeModulePath = $nodeModulePath;

        return $this;
    }

    /**
     * @param string $executablePath
     *
     * @return $this
     */
    public function setChromePath($executablePath) {
        $this->setOption('executablePath', $executablePath);

        return $this;
    }

    /**
     * @param array $postParams
     *
     * @return $this
     */
    public function post(array $postParams = []) {
        $this->postParams = $postParams;

        return $this;
    }

    /**
     * @param array       $cookies
     * @param null|string $domain
     *
     * @return $this
     */
    public function useCookies(array $cookies, $domain = null) {
        if (!count($cookies)) {
            return $this;
        }

        if (is_null($domain)) {
            $domain = parse_url($this->url)['host'];
        }

        $cookies = array_map(function ($value, $name) use ($domain) {
            return compact('name', 'value', 'domain');
        }, $cookies, array_keys($cookies));

        if (isset($this->additionalOptions['cookies'])) {
            $cookies = array_merge($this->additionalOptions['cookies'], $cookies);
        }

        $this->setOption('cookies', $cookies);

        return $this;
    }

    /**
     * @param array $extraHTTPHeaders
     *
     * @return $this
     */
    public function setExtraHttpHeaders(array $extraHTTPHeaders) {
        $this->setOption('extraHTTPHeaders', $extraHTTPHeaders);

        return $this;
    }

    /**
     * @param array $extraNavigationHTTPHeaders
     *
     * @return $this
     */
    public function setExtraNavigationHttpHeaders(array $extraNavigationHTTPHeaders) {
        $this->setOption('extraNavigationHTTPHeaders', $extraNavigationHTTPHeaders);

        return $this;
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return $this
     */
    public function authenticate($username, $password) {
        $this->setOption('authentication', compact('username', 'password'));

        return $this;
    }

    /**
     * @param string $selector
     * @param string $button
     * @param int    $clickCount
     * @param int    $delay
     *
     * @return $this
     */
    public function click($selector, $button = 'left', $clickCount = 1, $delay = 0) {
        $clicks = $this->additionalOptions['clicks'] ?? [];

        $clicks[] = compact('selector', 'button', 'clickCount', 'delay');

        $this->setOption('clicks', $clicks);

        return $this;
    }

    /**
     * @param string $selector
     * @param string $value
     *
     * @return $this
     */
    public function selectOption($selector, $value = '') {
        $dropdownSelects = $this->additionalOptions['selects'] ?? [];

        $dropdownSelects[] = compact('selector', 'value');

        $this->setOption('selects', $dropdownSelects);

        return $this;
    }

    /**
     * @param string $selector
     * @param string $text
     * @param int    $delay
     *
     * @return $this
     */
    public function type($selector, $text = '', $delay = 0) {
        $types = isset($this->additionalOptions['types']) ? $this->additionalOptions['types'] : [];

        $types[] = compact('selector', 'text', 'delay');

        $this->setOption('types', $types);

        return $this;
    }

    /**
     * @param bool $strict
     *
     * @return $this
     */
    public function waitUntilNetworkIdle($strict = true) {
        $this->setOption('waitUntil', $strict ? 'networkidle0' : 'networkidle2');

        return $this;
    }

    /**
     * @param string $function
     * @param string $polling
     * @param int    $timeout
     *
     * @return $this
     */
    public function waitForFunction($function, $polling = self::POLLING_REQUEST_ANIMATION_FRAME, $timeout = 0) {
        $this->setOption('functionPolling', $polling);
        $this->setOption('functionTimeout', $timeout);

        return $this->setOption('function', $function);
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url) {
        $this->url = $url;
        $this->html = '';

        return $this;
    }

    /**
     * @param string $proxyServer
     *
     * @return $this
     */
    public function setProxyServer($proxyServer) {
        $this->proxyServer = $proxyServer;

        return $this;
    }

    /**
     * @param string $html
     *
     * @return $this
     */
    public function setHtml($html) {
        $this->html = $html;
        $this->url = '';

        $this->hideBrowserHeaderAndFooter();

        return $this;
    }

    /**
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     *
     * @return $this
     */
    public function clip($x, $y, $width, $height) {
        return $this->setOption('clip', compact('x', 'y', 'width', 'height'));
    }

    /**
     * @param bool $preventUnsuccessfulResponse
     *
     * @return $this
     */
    public function preventUnsuccessfulResponse($preventUnsuccessfulResponse = true) {
        return $this->setOption('preventUnsuccessfulResponse', $preventUnsuccessfulResponse);
    }

    /**
     * @param string $selector
     * @param int    $index
     *
     * @return $this
     */
    public function select($selector, $index = 0) {
        $this->selectorIndex($index);

        return $this->setOption('selector', $selector);
    }

    /**
     * @param int $index
     *
     * @return $this
     */
    public function selectorIndex($index) {
        return $this->setOption('selectorIndex', $index);
    }

    public function showBrowserHeaderAndFooter() {
        return $this->setOption('displayHeaderFooter', true);
    }

    public function hideBrowserHeaderAndFooter() {
        return $this->setOption('displayHeaderFooter', false);
    }

    public function hideHeader() {
        return $this->headerHtml('<p></p>');
    }

    public function hideFooter() {
        return $this->footerHtml('<p></p>');
    }

    public function headerHtml(string $html) {
        return $this->setOption('headerTemplate', $html);
    }

    public function footerHtml(string $html) {
        return $this->setOption('footerTemplate', $html);
    }

    public function deviceScaleFactor(int $deviceScaleFactor) {
        // Google Chrome currently supports values of 1, 2, and 3.
        return $this->setOption('viewport.deviceScaleFactor', max(1, min(3, $deviceScaleFactor)));
    }

    public function fullPage() {
        return $this->setOption('fullPage', true);
    }

    public function showBackground() {
        $this->showBackground = true;
        $this->showScreenshotBackground = true;

        return $this;
    }

    public function hideBackground() {
        $this->showBackground = false;
        $this->showScreenshotBackground = false;

        return $this;
    }

    public function transparentBackground() {
        $this->transparentBackground = true;

        return $this;
    }

    public function setScreenshotType(string $type, int $quality = null) {
        $this->screenshotType = $type;

        if (!is_null($quality)) {
            $this->screenshotQuality = $quality;
        }

        return $this;
    }

    public function ignoreHttpsErrors() {
        return $this->setOption('ignoreHttpsErrors', true);
    }

    public function mobile(bool $mobile = true) {
        return $this->setOption('viewport.isMobile', $mobile);
    }

    public function touch(bool $touch = true) {
        return $this->setOption('viewport.hasTouch', $touch);
    }

    public function landscape(bool $landscape = true) {
        return $this->setOption('landscape', $landscape);
    }

    public function margins(float $top, float $right, float $bottom, float $left, string $unit = 'mm') {
        return $this->setOption('margin', [
            'top' => $top . $unit,
            'right' => $right . $unit,
            'bottom' => $bottom . $unit,
            'left' => $left . $unit,
        ]);
    }

    public function noSandbox() {
        $this->noSandbox = true;

        return $this;
    }

    public function dismissDialogs() {
        return $this->setOption('dismissDialogs', true);
    }

    public function disableJavascript() {
        return $this->setOption('disableJavascript', true);
    }

    public function disableImages() {
        return $this->setOption('disableImages', true);
    }

    public function blockUrls($array) {
        return $this->setOption('blockUrls', $array);
    }

    public function blockDomains($array) {
        return $this->setOption('blockDomains', $array);
    }

    /**
     * @param string $pages
     *
     * @return $this
     */
    public function pages($pages) {
        return $this->setOption('pageRanges', $pages);
    }

    /**
     * @param float  $width
     * @param float  $height
     * @param string $unit
     *
     * @return $this
     */
    public function paperSize($width, $height, $unit = 'mm') {
        return $this
            ->setOption('width', $width . $unit)
            ->setOption('height', $height . $unit);
    }

    /**
     * Paper Format.
     *
     * @param string $format
     *
     * @return $this
     */
    public function format($format) {
        return $this->setOption('format', $format);
    }

    /**
     * @param float $scale
     *
     * @return $this
     */
    public function scale($scale) {
        $this->scale = $scale;

        return $this;
    }

    /**
     * @param int $timeout
     *
     * @return $this
     */
    public function timeout($timeout) {
        $this->timeout = $timeout;
        $this->setOption('timeout', $timeout * 1000);

        return $this;
    }

    /**
     * @param string $userAgent
     *
     * @return $this
     */
    public function userAgent($userAgent) {
        $this->setOption('userAgent', $userAgent);

        return $this;
    }

    /**
     * @param string $device
     *
     * @return $this
     */
    public function device($device) {
        $this->setOption('device', $device);

        return $this;
    }

    /**
     * @param null|string $media
     *
     * @return $this
     */
    public function emulateMedia($media = null) {
        $this->setOption('emulateMedia', $media);

        return $this;
    }

    /**
     * @param int $width
     * @param int $height
     *
     * @return $this
     */
    public function windowSize($width, $height) {
        return $this
            ->setOption('viewport.width', $width)
            ->setOption('viewport.height', $height);
    }

    /**
     * @param int $delayInMilliseconds
     *
     * @return $this
     */
    public function setDelay($delayInMilliseconds) {
        return $this->setOption('delay', $delayInMilliseconds);
    }

    /**
     * @param int $delayInMilliseconds
     *
     * @return $this
     */
    public function delay($delayInMilliseconds) {
        return $this->setDelay($delayInMilliseconds);
    }

    /**
     * @param string $absolutePath
     *
     * @return $this
     */
    public function setUserDataDir($absolutePath) {
        return $this->addChromiumArguments(['user-data-dir' => $absolutePath]);
    }

    /**
     * @param string $absolutePath
     *
     * @return $this
     */
    public function userDataDir($absolutePath) {
        return $this->setUserDataDir($absolutePath);
    }

    /**
     * @return $this
     */
    public function writeOptionsToFile() {
        $this->writeOptionsToFile = true;

        return $this;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @return $this
     */
    public function setOption($key, $value) {
        $this->arraySet($this->additionalOptions, $key, $value);

        return $this;
    }

    public function addChromiumArguments(array $arguments) {
        foreach ($arguments as $argument => $value) {
            if (is_numeric($argument)) {
                $this->chromiumArguments[] = "--${value}";
            } else {
                $this->chromiumArguments[] = "--${argument}=${value}";
            }
        }

        return $this;
    }

    public function __call($name, $arguments) {
        $this->imageManipulations->$name(...$arguments);

        return $this;
    }

    /**
     * @param string $targetPath
     *
     * @return $this
     */
    public function save($targetPath) {
        $extension = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));

        if ($extension === '') {
            throw CServer_Browsershot_Exception_CouldNotTakeBrowsershotException::outputFileDidNotHaveAnExtension($targetPath);
        }

        if ($extension === 'pdf') {
            return $this->savePdf($targetPath);
        }

        $command = $this->createScreenshotCommand($targetPath);

        $output = $this->callBrowser($command);

        $this->cleanupTemporaryHtmlFile();

        if (!file_exists($targetPath)) {
            throw CServer_Browsershot_Exception_CouldNotTakeBrowsershotException::chromeOutputEmpty($targetPath, $output, $command);
        }

        if (!$this->imageManipulations->isEmpty()) {
            $this->applyManipulations($targetPath);
        }
    }

    /**
     * @return string
     */
    public function bodyHtml() {
        $command = $this->createBodyHtmlCommand();

        return $this->callBrowser($command);
    }

    /**
     * @return string
     */
    public function base64Screenshot() {
        $command = $this->createScreenshotCommand();

        return $this->callBrowser($command);
    }

    /**
     * @return string
     */
    public function screenshot() {
        if ($this->imageManipulations->isEmpty()) {
            $command = $this->createScreenshotCommand();

            $encodedImage = $this->callBrowser($command);

            return base64_decode($encodedImage);
        }

        $temporaryDirectory = CTemporary::customDirectory()->create();

        $this->save($temporaryDirectory->path('screenshot.png'));

        $screenshot = file_get_contents($temporaryDirectory->path('screenshot.png'));

        $temporaryDirectory->delete();

        return $screenshot;
    }

    /**
     * @return string
     */
    public function pdf() {
        $command = $this->createPdfCommand();

        $encoded_pdf = $this->callBrowser($command);

        $this->cleanupTemporaryHtmlFile();

        return base64_decode($encoded_pdf);
    }

    /**
     * @param null|string $targetPath
     *
     * @return string
     */
    public function savePdf($targetPath = null) {
        if ($targetPath == null) {
            $targetPath = CTemporary::getLocalPath('browsershot') . '.pdf';
            $targetDir = CFile::dirname($targetPath);
            if (!CFile::isDirectory($targetDir)) {
                CFile::makeDirectory($targetDir, 0755, true);
            }
        }
        $command = $this->createPdfCommand($targetPath);

        $output = $this->callBrowser($command);

        $this->cleanupTemporaryHtmlFile();

        if (!file_exists($targetPath)) {
            throw CServer_Browsershot_Exception_CouldNotTakeBrowsershotException::chromeOutputEmpty($targetPath, $output);
        }

        return $targetPath;
    }

    /**
     * @return string
     */
    public function base64pdf() {
        $command = $this->createPdfCommand();

        return $this->callBrowser($command);
    }

    /**
     * @param string $pageFunction
     *
     * @return string
     */
    public function evaluate($pageFunction) {
        $command = $this->createEvaluateCommand($pageFunction);

        return $this->callBrowser($command);
    }

    public function triggeredRequests(): array {
        $command = $this->createTriggeredRequestsListCommand();

        return json_decode($this->callBrowser($command), true);
    }

    public function applyManipulations(string $imagePath) {
        CImage_Image::load($imagePath)
            ->manipulate($this->imageManipulations)
            ->save();
    }

    public function createBodyHtmlCommand(): array {
        $url = $this->html ? $this->createTemporaryHtmlFile() : $this->url;

        return $this->createCommand($url, 'content');
    }

    public function createScreenshotCommand($targetPath = null): array {
        $url = $this->html ? $this->createTemporaryHtmlFile() : $this->url;

        $options = [
            'type' => $this->screenshotType,
        ];
        if ($targetPath) {
            $options['path'] = $targetPath;
        }

        if ($this->screenshotQuality) {
            $options['quality'] = $this->screenshotQuality;
        }

        $command = $this->createCommand($url, 'screenshot', $options);

        if (!$this->showScreenshotBackground) {
            $command['options']['omitBackground'] = true;
        }

        return $command;
    }

    /**
     * @param null|string $targetPath
     *
     * @return array
     */
    public function createPdfCommand($targetPath = null) {
        $url = $this->html ? $this->createTemporaryHtmlFile() : $this->url;

        $options = [];

        if ($targetPath) {
            $options['path'] = $targetPath;
        }

        $command = $this->createCommand($url, 'pdf', $options);

        if ($this->showBackground) {
            $command['options']['printBackground'] = true;
        }

        if ($this->transparentBackground) {
            $command['options']['omitBackground'] = true;
        }

        if ($this->scale) {
            $command['options']['scale'] = $this->scale;
        }

        return $command;
    }

    /**
     * @param string $pageFunction
     *
     * @return array
     */
    public function createEvaluateCommand($pageFunction) {
        $url = $this->html ? $this->createTemporaryHtmlFile() : $this->url;

        $options = [
            'pageFunction' => $pageFunction,
        ];

        return $this->createCommand($url, 'evaluate', $options);
    }

    public function createTriggeredRequestsListCommand(): array {
        $url = $this->html ? $this->createTemporaryHtmlFile() : $this->url;

        return $this->createCommand($url, 'requestsList');
    }

    public function setRemoteInstance(string $ip = '127.0.0.1', int $port = 9222): self {
        // assuring that ip and port does actually contains a value
        if ($ip && $port) {
            $this->setOption('remoteInstanceUrl', 'http://' . $ip . ':' . $port);
        }

        return $this;
    }

    public function setWSEndpoint(string $endpoint): self {
        if (!is_null($endpoint)) {
            $this->setOption('browserWSEndpoint', $endpoint);
        }

        return $this;
    }

    public function usePipe(): self {
        $this->setOption('pipe', true);

        return $this;
    }

    public function setEnvironmentOptions(array $options = []): self {
        return $this->setOption('env', $options);
    }

    public function setContentUrl(string $contentUrl): self {
        return $this->html ? $this->setOption('contentUrl', $contentUrl) : $this;
    }

    protected function getOptionArgs(): array {
        $args = $this->chromiumArguments;

        if ($this->noSandbox) {
            $args[] = '--no-sandbox';
        }

        if ($this->proxyServer) {
            $args[] = '--proxy-server=' . $this->proxyServer;
        }

        return $args;
    }

    protected function createCommand(string $url, string $action, array $options = []): array {
        $command = compact('url', 'action', 'options');

        $command['options']['args'] = $this->getOptionArgs();

        if (!empty($this->postParams)) {
            $command['postParams'] = $this->postParams;
        }

        if (!empty($this->additionalOptions)) {
            $command['options'] = array_merge_recursive($command['options'], $this->additionalOptions);
        }

        return $command;
    }

    /**
     * @return string
     */
    protected function createTemporaryHtmlFile() {
        $this->temporaryHtmlDirectory = CTemporary::customDirectory()->create();

        file_put_contents($temporaryHtmlFile = $this->temporaryHtmlDirectory->path('index.html'), $this->html);

        return "file://{$temporaryHtmlFile}";
    }

    protected function cleanupTemporaryHtmlFile() {
        if ($this->temporaryHtmlDirectory) {
            $this->temporaryHtmlDirectory->delete();
        }
    }

    /**
     * @param string $command
     *
     * @return string
     */
    protected function createTemporaryOptionsFile($command) {
        $this->temporaryOptionsDirectory = CTemporary::customDirectory()->create();

        file_put_contents($temporaryOptionsFile = $this->temporaryOptionsDirectory->path('command.js'), $command);

        return "file://{$temporaryOptionsFile}";
    }

    protected function cleanupTemporaryOptionsFile() {
        if ($this->temporaryOptionsDirectory) {
            $this->temporaryOptionsDirectory->delete();
        }
    }

    /**
     * @param array $command
     *
     * @return string
     */
    protected function callBrowser($command) {
        $fullCommand = $this->getFullCommand($command);
        // $fullCommand = trim(<<<END
        // PATH=\$PATH:/usr/local/bin:/opt/homebrew/bin NODE_PATH=`npm root -g` node '/home/appittro/public_html/.bin/browsershot/browser.js' '{"url":"file:\/\/\/tmp\/2030169288-0256412001653404063\/index.html","action":"pdf","options":{"path":"\/home\/appittro\/public_html\/temp\/browsershot\/20220524\/e\/3\/5\/4\/f\/20220524e354fd90b2d5c777bfec87a352a18976.pdf","args":[],"viewport":{"width":800,"height":600},"displayHeaderFooter":false}}'
        // END);
        // $fullCommand = trim(<<<END
        // PATH=\$PATH:/usr/local/bin:/opt/homebrew/bin NODE_PATH=`npm root -g` node '/home/appittro/public_html/.bin/browsershot/browser.js' '{"url":"file:\/\/\/tmp\/2030169288-0256412001653404063\/index.html","action":"pdf","options":{"path":"example.pdf","args":[],"viewport":{"width":800,"height":600},"displayHeaderFooter":false}}'
        // END);
        $process = Process::fromShellCommandline($fullCommand)->setTimeout($this->timeout);

        $process->run();

        if ($process->isSuccessful()) {
            return rtrim($process->getOutput());
        }

        $this->cleanupTemporaryOptionsFile();
        $process->clearOutput();
        $exitCode = $process->getExitCode();

        if ($exitCode === 3) {
            throw new CServer_Browsershot_Exception_UnsuccessfulResponse($this->url, $process->getErrorOutput());
        }

        if ($exitCode === 2) {
            throw new CServer_Browsershot_Exception_ElementNotFound($this->additionalOptions['selector']);
        }

        throw new ProcessFailedException($process);
    }

    protected function getFullCommand(array $command) {
        $nodeBinary = $this->nodeBinary ?: 'node';

        $binPath = $this->binPath ?: DOCROOT . '.bin/browsershot/browser.js';

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $fullCommand
                = $nodeBinary . ' '
                . escapeshellarg($binPath) . ' '
                . '"' . str_replace('"', '\"', (json_encode($command))) . '"';

            return escapeshellcmd($fullCommand);
        }

        $setIncludePathCommand = "PATH={$this->includePath}";

        $setNodePathCommand = $this->getNodePathCommand($nodeBinary);

        $optionsCommand = $this->getOptionsCommand(json_encode($command));

        return
            $setIncludePathCommand . ' '
            . $setNodePathCommand . ' '
            . $nodeBinary . ' '
            . escapeshellarg($binPath) . ' '
            . $optionsCommand;
    }

    /**
     * @param string $nodeBinary
     *
     * @return string
     */
    protected function getNodePathCommand($nodeBinary) {
        if ($this->nodeModulePath) {
            return "NODE_PATH='{$this->nodeModulePath}'";
        }
        if ($this->npmBinary) {
            return "NODE_PATH=`{$nodeBinary} {$this->npmBinary} root -g`";
        }

        return 'NODE_PATH=`npm root -g`';
    }

    protected function getOptionsCommand(string $command): string {
        if ($this->writeOptionsToFile) {
            $temporaryOptionsFile = $this->createTemporaryOptionsFile($command);

            return escapeshellarg("-f {$temporaryOptionsFile}");
        }

        return escapeshellarg($command);
    }

    protected function arraySet(array &$array, string $key, $value): array {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    public function initialPageNumber(int $initialPage = 1) {
        return $this
            ->setOption('initialPageNumber', ($initialPage - 1))
            ->pages($initialPage . '-');
    }
}

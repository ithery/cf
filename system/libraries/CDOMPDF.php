<?php

defined('SYSPATH') or die('No direct access allowed.');

use Dompdf\Dompdf;

use Dompdf\Adapter\CPDF;
use Symfony\Component\HttpFoundation\HeaderUtils;

/**
 * A wrapper for Dompdf.
 *
 * @method CDOMPDF                 setBaseHost(string $baseHost)
 * @method CDOMPDF                 setBasePath(string $basePath)
 * @method CDOMPDF                 setCanvas(\Dompdf\Canvas $canvas)
 * @method CDOMPDF                 setCallbacks(array<string, mixed> $callbacks)
 * @method CDOMPDF                 setCss(\Dompdf\Css\Stylesheet $css)
 * @method CDOMPDF                 setDefaultView(string $defaultView, array<string, mixed> $options)
 * @method CDOMPDF                 setDom(\DOMDocument $dom)
 * @method CDOMPDF                 setFontMetrics(\Dompdf\FontMetrics $fontMetrics)
 * @method CDOMPDF                 setHttpContext(resource|array<string, mixed> $httpContext)
 * @method CDOMPDF                 setPaper(string|float[] $paper, string $orientation = 'portrait')
 * @method CDOMPDF                 setProtocol(string $protocol)
 * @method CDOMPDF                 setTree(\Dompdf\Frame\FrameTree $tree)
 * @method string                  getBaseHost()
 * @method string                  getBasePath()
 * @method \Dompdf\Canvas          getCanvas()
 * @method array<string,           mixed> getCallbacks()
 * @method \Dompdf\Css\Stylesheet  getCss()
 * @method \DOMDocument            getDom()
 * @method \Dompdf\FontMetrics     getFontMetrics()
 * @method resource                getHttpContext()
 * @method Options                 getOptions()
 * @method \Dompdf\Frame\FrameTree getTree()
 * @method string                  getPaperOrientation()
 * @method float[]                 getPaperSize()
 * @method string                  getProtocol()
 */
class CDOMPDF extends Dompdf {
    public function __construct() {
        parent::__construct();
    }

    public static function factory() {
        return new CDOMPDF();
    }

    /**
     * Add metadata info.
     *
     * @param array<string, string> $info
     */
    public function addInfo(array $info): self {
        foreach ($info as $name => $value) {
            $this->add_info($name, $value);
        }

        return $this;
    }

    /**
     * Make the PDF downloadable by the user.
     */
    public function download(string $filename = 'document.pdf'): CHTTP_Response {
        $output = $this->output();
        $fallback = $this->fallbackName($filename);

        return new CHTTP_Response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => HeaderUtils::makeDisposition('attachment', $filename, $fallback),
            'Content-Length' => strlen($output),
        ]);
    }

    /**
     * @param array<string> $pc
     */
    public function setEncryption(string $password, string $ownerpassword = '', array $pc = []): void {
        $this->render();
        $canvas = $this->getCanvas();
        if (!$canvas instanceof CPDF) {
            throw new \RuntimeException('Encryption is only supported when using CPDF');
        }
        /** @var CPDF $canvas */
        $canvas->get_cpdf()->setEncryption($password, $ownerpassword, $pc);
    }

    protected function convertEntities(string $subject): string {
        if (false === $this->config->get('dompdf.convert_entities', true)) {
            return $subject;
        }

        $entities = [
            '€' => '&euro;',
            '£' => '&pound;',
        ];

        foreach ($entities as $search => $replace) {
            $subject = str_replace($search, $replace, $subject);
        }

        return $subject;
    }

    /**
     * Make a safe fallback filename.
     */
    protected function fallbackName(string $filename): string {
        return str_replace('%', '', cstr::ascii($filename));
    }
}

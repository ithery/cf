<?php

use Knp\Snappy\Image as SnappyImage;

use Symfony\Component\HttpFoundation\StreamedResponse;

class CExporter_Snappy_ImageWrapper {
    /**
     * @var \Knp\Snappy\Image
     */
    protected $snappy;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string
     */
    protected $html;

    /**
     * @var string
     */
    protected $file;

    /**
     * @param \Knp\Snappy\Image $snappy
     */
    public function __construct(SnappyImage $snappy) {
        $this->snappy = $snappy;
    }

    /**
     * Get the Snappy instance.
     *
     * @return \Knp\Snappy\Image
     */
    public function snappy() {
        return $this->snappy;
    }

    public function setOption($name, $value) {
        $this->snappy->setOption($name, $value);

        return $this;
    }

    public function setOptions($options) {
        $this->snappy->setOptions($options);

        return $this;
    }

    /**
     * Load a HTML string.
     *
     * @param string $string
     *
     * @return static
     */
    public function loadHTML($string) {
        $this->html = (string) $string;
        $this->file = null;

        return $this;
    }

    /**
     * Load a HTML file.
     *
     * @param string $file
     *
     * @return static
     */
    public function loadFile($file) {
        $this->html = null;
        $this->file = $file;

        return $this;
    }

    public function loadView($view, $data = [], $mergeData = []) {
        $this->html = CView::factory()->make($view, $data, $mergeData)->render();
        $this->file = null;

        return $this;
    }

    /**
     * Output the PDF as a string.
     *
     * @throws \InvalidArgumentException
     *
     * @return string The rendered PDF as string
     */
    public function output() {
        if ($this->html) {
            return $this->snappy->getOutputFromHtml($this->html, $this->options);
        }

        if ($this->file) {
            return $this->snappy->getOutput($this->file, $this->options);
        }

        throw new \InvalidArgumentException('Image Generator requires a html or file in order to produce output.');
    }

    /**
     * Save the image to a file.
     *
     * @param $filename
     * @param mixed $overwrite
     *
     * @return static
     */
    public function save($filename, $overwrite = false) {
        if ($this->html) {
            $this->snappy->generateFromHtml($this->html, $filename, $this->options, $overwrite);
        } elseif ($this->file) {
            $this->snappy->generate($this->file, $filename, $this->options, $overwrite);
        }

        return $this;
    }

    /**
     * Make the image downloadable by the user.
     *
     * @param string $filename
     *
     * @return \CHTTP_Response
     */
    public function download($filename = 'image.jpg') {
        return new CHTTP_Response($this->output(), 200, [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Return a response with the image to show in the browser.
     *
     * @param string $filename
     *
     * @return \CHTTP_Response
     */
    public function inline($filename = 'image.jpg') {
        return new CHTTP_Response($this->output(), 200, [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Return a response with the image to show in the browser.
     *
     * @deprecated Use inline() instead
     *
     * @param string $filename
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function stream($filename = 'image.jpg') {
        return new StreamedResponse(function () {
            echo $this->output();
        }, 200, [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Call Snappy instance.
     *
     * Also shortcut's
     * ->html => loadHtml
     * ->view => loadView
     * ->file => loadFile
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, array $arguments) {
        $method = 'load' . ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $arguments);
        }

        return call_user_func_array([$this->snappy, $name], $arguments);
    }
}

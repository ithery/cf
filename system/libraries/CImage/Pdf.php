<?php
/**
 * @see https://github.com/spatie/pdf-to-image
 */
class CImage_Pdf {
    public $imagick;

    protected string $filename;

    protected int $resolution = 144;

    protected ?string $backgroundColor = null;

    protected string $outputFormat = CImage_Pdf_Enums_OutputFormat::JPG;

    protected array $pages = [1];

    protected int $layerMethod = CImage_Pdf_Enums_LayerMethod::FLATTEN;

    protected $colorspace;

    protected ?int $compressionQuality = null;

    protected ?int $thumbnailWidth = null;

    protected ?int $thumbnailHeight = null;

    protected ?int $resizeWidth = null;

    protected ?int $resizeHeight = null;

    protected ?int $numberOfPages = null;

    public function __construct(string $filename) {
        if (!file_exists($filename)) {
            throw CImage_Pdf_Exception_PdfDoesNotExistException::for($filename);
        }

        $this->filename = $filename;
    }

    /**
     * Sets the resolution of the generated image in DPI.
     * Default is 144 DPI.
     */
    public function resolution(int $dpiResolution) {
        $this->resolution = $dpiResolution;

        return $this;
    }

    /**
     * Set the background color e.g. 'white', '#fff', 'rgb(255, 255, 255)'.
     */
    public function backgroundColor(string $backgroundColorCode) {
        $this->backgroundColor = $backgroundColorCode;

        return $this;
    }

    /**
     * Sets the output format of the generated image.
     * Default is CImage_Pdf_Enums_OutputFormat::JPG.
     */
    public function format(string $outputFormat) {
        $this->outputFormat = $outputFormat;

        return $this;
    }

    public function getFormat(): string {
        return $this->outputFormat;
    }

    /**
     * Sets the layer method for Imagick::mergeImageLayers()
     * If int, should correspond to a predefined Imagick LAYERMETHOD constant.
     * If LayerMethod, should be a valid LayerMethod enum.
     * To disable merging image layers, set to LayerMethod::None.
     *
     * @param \CImage_Pdf_Enums_LayerMethod|int
     *
     * @throws \CImage_Pdf_Exception_InvalidLayerMethodException
     *
     * @return $this
     *
     * @see https://secure.php.net/manual/en/imagick.constants.php
     * @see Pdf::getImageData()
     */
    public function layerMethod(int $method) {
        if (is_int($method) && !CImage_Pdf_Enums_LayerMethod::isValid($method)) {
            throw CImage_Pdf_Exception_InvalidLayerMethodException::for($method);
        }

        $this->layerMethod = $method;

        return $this;
    }

    /**
     * @param null|string|OutputFormat $outputFormat
     *                                               Expects a string or OutputFormat enum. If a string, expects the file extension of the format,
     *                                               without a leading period.
     */
    public function isValidOutputFormat($outputFormat): bool {
        if ($outputFormat === null) {
            return false;
        }

        return is_string($outputFormat) && CImage_Pdf_Enums_OutputFormat::isValid($outputFormat);
    }

    public function selectPage(int $page) {
        return $this->selectPages($page);
    }

    public function selectPages(int ...$pages) {
        $this->validatePageNumbers(...$pages);

        $this->pages = $pages;

        return $this;
    }

    /**
     * Returns the number of pages in the PDF.
     */
    public function pageCount(): int {
        if ($this->imagick === null) {
            $this->imagick = new Imagick();
            $this->imagick->pingImage($this->filename);
        }

        if ($this->numberOfPages === null) {
            $this->numberOfPages = $this->imagick->getNumberImages();
        }

        return $this->numberOfPages;
    }

    /**
     * Returns a DTO representing the size of the PDF, which
     * contains the width and height in pixels.
     */
    public function getSize(): CImage_Pdf_DTOs_PageSize {
        if ($this->imagick === null) {
            $this->imagick = new Imagick();
            $this->imagick->pingImage($this->filename);
        }

        $geometry = $this->imagick->getImageGeometry();

        return CImage_Pdf_DTOs_PageSize::make($geometry['width'], $geometry['height']);
    }

    /**
     * Saves the PDF as an image. Expects a path to save the image to, which should be
     * a directory if multiple pages have been selected (otherwise the image will be overwritten).
     * Returns an array of paths to the saved images.
     *
     * @return array<string>
     */
    public function save(string $pathToImage, string $prefix = ''): array {
        $pages = [CImage_Pdf_DTOs_PdfPage::make($this->pages[0], $this->outputFormat, $prefix, $pathToImage)];

        if (is_dir($pathToImage)) {
            $pages = array_map(fn ($page) => CImage_Pdf_DTOs_PdfPage::make($page, $this->outputFormat, $prefix, rtrim($pathToImage, '\/') . DIRECTORY_SEPARATOR . $page . '.' . $this->outputFormat), $this->pages);
        }

        $result = [];

        foreach ($pages as $page) {
            $path = $page->filename();
            $imageData = $this->getImageData($path, $page->number);

            if (file_put_contents($path, $imageData) !== false) {
                $result[] = $path;
            }
        }

        return $result;
    }

    /**
     * Saves all pages of the PDF as images. Expects a directory to save the images to,
     * and an optional prefix for the image filenames. Returns an array of paths to the saved images.
     *
     * @return array<string>
     */
    public function saveAllPages(string $directory, string $prefix = ''): array {
        $numberOfPages = $this->pageCount();

        if ($numberOfPages === 0) {
            return [];
        }

        $this->selectPages(...range(1, $numberOfPages));

        return $this->save($directory, $prefix);
    }

    public function getImageData(string $pathToImage, int $pageNumber): Imagick {
        /*
         * Reinitialize imagick because the target resolution must be set
         * before reading the actual image.
         */
        $this->imagick = new Imagick();

        $this->imagick->setResolution($this->resolution, $this->resolution);

        if ($this->colorspace !== null) {
            $this->imagick->setColorspace($this->colorspace);
        }

        if ($this->compressionQuality !== null) {
            $this->imagick->setCompressionQuality($this->compressionQuality);
        }

        $this->imagick->readImage(sprintf('%s[%s]', $this->filename, $pageNumber - 1));

        if (!empty($this->backgroundColor)) {
            $this->imagick->setImageBackgroundColor(new ImagickPixel($this->backgroundColor));
            $this->imagick->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
        }

        if ($this->resizeWidth !== null) {
            $this->imagick->resizeImage($this->resizeWidth, $this->resizeHeight ?? 0, Imagick::FILTER_POINT, 0);
        }

        if ($this->layerMethod !== CImage_Pdf_Enums_LayerMethod::NONE) {
            $this->imagick = $this->imagick->mergeImageLayers($this->layerMethod);
        }

        if ($this->thumbnailWidth !== null) {
            $this->imagick->thumbnailImage($this->thumbnailWidth, $this->thumbnailHeight ?? 0);
        }

        $this->imagick->setFormat($this->determineOutputFormat($pathToImage));

        return $this->imagick;
    }

    public function colorspace(int $colorspace) {
        $this->colorspace = $colorspace;

        return $this;
    }

    /**
     * Set the compression quality for the image. The value should be between 1 and 100, where
     * 1 is the lowest quality and 100 is the highest.
     *
     * @throws \CImage_Pdf_Exception_InvalidQualityException
     */
    public function quality(int $compressionQuality) {
        if ($compressionQuality < 1 || $compressionQuality > 100) {
            throw CImage_Pdf_Exception_InvalidQualityException::for($compressionQuality);
        }

        $this->compressionQuality = $compressionQuality;

        return $this;
    }

    /**
     * Set the thumbnail size for the image. If no height is provided, the thumbnail height will
     * be scaled according to the width.
     *
     * @throws \CImage_Pdf_Exception_InvalidSizeException
     */
    public function thumbnailSize(int $width, ?int $height = null) {
        if ($width < 0) {
            throw CImage_Pdf_Exception_InvalidSizeException::forThumbnail($width, 'width');
        }

        if ($height !== null && $height < 0) {
            throw CImage_Pdf_Exception_InvalidSizeException::forThumbnail($height, 'height');
        }

        $this->thumbnailWidth = $width;
        $this->thumbnailHeight = $height ?? 0;

        return $this;
    }

    /**
     * Set the size of the image. If no height is provided, the height will be scaled according to the width.
     *
     * @throws \CImage_Pdf_Exception_InvalidSizeException
     */
    public function size(int $width, ?int $height = null) {
        if ($width < 0) {
            throw CImage_Pdf_Exception_InvalidSizeException::forImage($width, 'width');
        }

        if ($height !== null && $height < 0) {
            throw CImage_Pdf_Exception_InvalidSizeException::forImage($height, 'height');
        }

        $this->resizeWidth = $width;
        $this->resizeHeight = $height ?? 0;

        return $this;
    }

    protected function determineOutputFormat(string $pathToImage): string {
        $outputFormat = pathinfo($pathToImage, PATHINFO_EXTENSION);

        if (!empty($this->outputFormat)) {
            $outputFormat = $this->outputFormat;
        }

        if (!$this->isValidOutputFormat($outputFormat)) {
            $outputFormat = CImage_Pdf_Enums_OutputFormat::JPG;
        }

        return $outputFormat;
    }

    /**
     * Validate that the page numbers are within the range of the PDF, which is 1 to the number of pages.
     * Throws a PageDoesNotExist exception if a page number is out of range.
     *
     * @throws \CImage_Pdf_Exception_PageDoesNotExistException
     */
    protected function validatePageNumbers(int ...$pageNumbers): void {
        $count = $this->pageCount();

        foreach ($pageNumbers as $page) {
            if ($page > $count || $page < 1) {
                throw CImage_Pdf_Exception_PageDoesNotExistException::for($page);
            }
        }
    }
}

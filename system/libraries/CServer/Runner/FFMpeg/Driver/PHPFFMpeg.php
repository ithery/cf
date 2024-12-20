<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 *
 * @since Aug 26, 2020
 *
 * @license Ittron Global Teknologi
 */
use FFMpeg\FFMpeg;
use FFMpeg\Media\Frame;
use FFMpeg\Media\Video;
use FFMpeg\Media\Concat;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Media\AdvancedMedia;
use FFMpeg\Filters\FilterInterface;
use FFMpeg\Media\AbstractMediaType;
use FFMpeg\FFProbe\DataMapping\Stream;
use FFMpeg\Filters\Audio\SimpleFilter;

/**
 * @mixin \FFMpeg\Media\AbstractMediaType
 */
class CServer_Runner_FFMpeg_Driver_PHPFFMpeg {
    use CTrait_ForwardsCalls;

    /**
     * @var \FFMpeg\FFMpeg
     */
    private $ffmpeg;

    /**
     * @var CRunner_FFMpeg_MediaCollection
     */
    private $mediaCollection;

    /**
     * @var bool
     */
    private $forceAdvanced = false;

    /**
     * @var CCollection
     */
    private $pendingComplexFilters;

    /**
     * @var \FFMpeg\Media\AbstractMediaType
     */
    private $media;

    public function __construct(FFMpeg $ffmpeg = null) {
        if ($ffmpeg == null) {
            $ffmpeg = FFMpeg::create();
        }
        $this->ffmpeg = $ffmpeg;
        $this->pendingComplexFilters = new CCollection();
    }

    /**
     * Returns a fresh instance of itself with only the underlying FFMpeg instance.
     */
    public function fresh() {
        return new static($this->ffmpeg);
    }

    public function get() {
        return $this->media;
    }

    private function isAdvancedMedia() {
        return $this->get() instanceof AdvancedMedia;
    }

    public function isFrame() {
        return $this->get() instanceof Frame;
    }

    public function isConcat() {
        return $this->get() instanceof Concat;
    }

    public function isVideo() {
        return $this->get() instanceof Video;
    }

    public function getMediaCollection() {
        return $this->mediaCollection;
    }

    /**
     * Opens the MediaCollection if it's not been instanciated yet.
     */
    public function open(CRunner_FFMpeg_MediaCollection $mediaCollection) {
        if ($this->media) {
            return $this;
        }

        $this->mediaCollection = $mediaCollection;

        $localPaths = $mediaCollection->getLocalPaths();

        if (count($localPaths) === 1 && !$this->forceAdvanced) {
            $this->media = $this->ffmpeg->open(carr::first($localPaths));
        } else {
            $this->media = $this->ffmpeg->openAdvanced($localPaths);
        }

        return $this;
    }

    public function frame(TimeCode $timecode) {
        if (!$this->isVideo()) {
            throw new Exception('Opened media is not a video file.');
        }

        $this->media = $this->media->frame($timecode);

        return $this;
    }

    public function concatWithoutTranscoding() {
        $localPaths = $this->mediaCollection->getLocalPaths();

        $this->media = $this->ffmpeg->open(carr::first($localPaths))
            ->concat($localPaths);

        return $this;
    }

    /**
     * Force 'openAdvanced' when opening the MediaCollection.
     */
    public function openAdvanced(CRunner_FFMpeg_MediaCollection $mediaCollection) {
        $this->forceAdvanced = true;

        return $this->open($mediaCollection);
    }

    public function getStreams() {
        if (!$this->isAdvancedMedia()) {
            return iterator_to_array($this->media->getStreams());
        }

        return $this->mediaCollection->map(function (CRunner_FFMpeg_Media $media) {
            return $this->fresh()->open(CRunner_FFMpeg_MediaCollection::make([$media]))->getStreams();
        })->collapse()->all();
    }

    public function getFilters() {
        return iterator_to_array($this->media->getFiltersCollection());
    }

    public function getDurationInSeconds() {
        return round($this->getDurationInMiliseconds() / 1000);
    }

    /**
     * Gets the duration of the media from the first stream or from the format.
     */
    public function getDurationInMiliseconds() {
        $stream = carr::first($this->getStreams());

        if ($stream->has('duration')) {
            return $stream->get('duration') * 1000;
        }

        $format = $this->getFormat();

        if ($format->has('duration')) {
            return $format->get('duration') * 1000;
        }
    }

    /**
     * Gets the first audio streams of the media.
     */
    public function getAudioStream() {
        return carr::first($this->getStreams(), function (Stream $stream) {
            return $stream->isAudio();
        });
    }

    /**
     * Gets the first video streams of the media.
     */
    public function getVideoStream() {
        return carr::first($this->getStreams(), function (Stream $stream) {
            return $stream->isVideo();
        });
    }

    /**
     * Helper method to provide multiple ways to add a filter to the underlying
     * media object.
     *
     * @return self
     */
    public function addFilter() {
        $arguments = func_get_args();

        // to support '[in]filter[out]' complex filters
        if ($this->isAdvancedMedia() && count($arguments) === 3) {
            $this->media->filters()->custom(...$arguments);

            return $this;
        }

        // use a callback to add a filter
        if ($arguments[0] instanceof Closure) {
            call_user_func_array($arguments[0], [$this->media->filters()]);

            return $this;
        }

        // use an object to add a filter
        if ($arguments[0] instanceof FilterInterface) {
            call_user_func_array([$this->media, 'addFilter'], $arguments);

            return $this;
        }

        // use a single array with parameters to define a filter
        if (is_array($arguments[0])) {
            $this->media->addFilter(new SimpleFilter($arguments[0]));

            return $this;
        }

        // use all function arguments as a filter
        $this->media->addFilter(new SimpleFilter($arguments));

        return $this;
    }

    /**
     * Maps the arguments into a 'LegacyFilterMapping' instance and
     * pushed it to the 'pendingComplexFilters' collection. These
     * filters will be applied later on by the MediaExporter.
     *
     * @param mixed $in
     * @param mixed $out
     */
    public function addFilterAsComplexFilter($in, $out, ...$arguments) {
        $this->pendingComplexFilters->push(new LegacyFilterMapping(
            $in,
            $out,
            ...$arguments
        ));

        return $this;
    }

    public function getPendingComplexFilters() {
        return $this->pendingComplexFilters;
    }

    /**
     * Returns the underlying media object itself.
     */
    public function __invoke() {
        return $this->get();
    }

    /**
     * Forwards the call to the underling media object and returns the result
     * if it's something different than the media object itself.
     *
     * @param mixed $method
     * @param mixed $arguments
     */
    public function __call($method, $arguments) {
        $result = $this->forwardCallTo($media = $this->get(), $method, $arguments);

        return ($result === $media) ? $this : $result;
    }
}

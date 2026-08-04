<?php

use Webmozart\Assert\Assert;

final class CVendor_Kataai_Message_Header {
    public const TYPE_DOCUMENT = 'DOCUMENT';

    public const TYPE_VIDEO = 'VIDEO';

    public const TYPE_IMAGE = 'IMAGE';

    /**
     * @var string
     */
    private $format;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $filename;

    public function __construct(string $format, string $url, string $filename) {
        Assert::inArray($format, ['TEXT', 'IMAGE', 'DOCUMENT', 'VIDEO']);
        $this->format = $format;

        $this->url = $url;

        $this->filename = $filename;
    }

    public function getFormat(): string {
        return $this->format;
    }

    public function getUrl(): string {
        return $this->url;
    }

    public function getFilename(): string {
        return $this->filename;
    }

    public function toArray(): array {
        $return = [
            'type' => cstr::lower($this->getFormat()),
        ] + $this->headerItem();

        return $return;
    }

    public function headerItem(): array {
        switch ($this->getFormat()) {
            case 'TEXT':
                $return = [
                    'text' => $this->getUrl(),
                ];
                break;
            case 'IMAGE':
                $return = [
                    'image' => [
                        'link' => $this->getUrl(),
                    ]
                ];
                break;
            case 'VIDEO':
                $return = [
                    'video' => [
                        'link' => $this->getUrl(),
                    ]
                ];
                break;
            case 'DOCUMENT':
                $return = [
                    'document' => [
                        'link' => $this->getUrl(),
                        'filename' => $this->getFilename(),
                    ]
                ];
                break;
            default:
                throw new \InvalidArgumentException('Invalid header format: ' . $this->getFormat());
        }
        return $return;
    }
}

<?php

class CManager_EditorJs_ImageUploadHandler {
    protected $path;

    protected $disk;

    protected $options;

    public function __construct($options = []) {
        $this->options = array_merge(CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.image'), $options);
        $this->path = $this->getOption('path', 'editorjs/image');
        $this->disk = $this->getOption('disk', 'local-temp');
    }

    /**
     * Upload file.
     *
     * @param CHTTP_Request $request
     *
     * @return array
     */
    public function byFile(CHTTP_Request $request) {
        $validator = CValidation::createValidator($request->all(), [
            'image' => 'required|image',
        ]);

        if ($validator->fails()) {
            return [
                'success' => 0
            ];
        }

        $path = $request->file('image')->store($this->path, $this->disk);

        if ($this->disk !== 'local-temp') {
            $tempPath = $request->file('image')->store(
                $this->path,
                'local-temp'
            );

            $this->applyAlterations(c::disk('local-temp')->path($tempPath));
            $thumbnails = $this->applyThumbnails($tempPath);

            $this->deleteThumbnails(c::disk('local-temp')->path($tempPath));
            c::disk('local-temp')->delete($tempPath);
        } else {
            $this->applyAlterations(c::disk($this->disk)->path($path));
            $thumbnails = $this->applyThumbnails($path);
        }

        return [
            'success' => 1,
            'file' => [
                'url' => c::disk($this->disk)->url($path),
                'thumbnails' => $thumbnails
            ]
        ];
    }

    /**
     * @param NovaRequest $request
     *
     * @return array
     */
    public function byUrl(CHTTP_Request $request) {
        $validator = CValidation::createValidator($request->all(), [
            'url' => [
                'required',
                'active_url',
                function ($attribute, $value, $fail) {
                    $imageDetails = getimagesize($value);

                    if (!in_array($imageDetails['mime'] ?? '', [
                        'image/jpeg',
                        'image/webp',
                        'image/gif',
                        'image/png',
                        'image/svg+xml',
                    ])
                    ) {
                        $fail($attribute . ' is invalid.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return [
                'success' => 0
            ];
        }

        $url = $request->input('url');
        $imageContents = file_get_contents($url);
        $name = parse_url(substr($url, strrpos($url, '/') + 1))['path'];
        $nameWithPath = $this->path . '/' . uniqid() . $name;

        c::disk($this->disk)->put($nameWithPath, $imageContents);

        return [
            'success' => 1,
            'file' => [
                'url' => c::disk($this->disk)->url($nameWithPath)
            ]
        ];
    }

    protected function getOption($key, $default = null) {
        return carr::get($this->options, $key, $default);
    }

    /**
     * @param $path
     * @param array $alterations
     */
    private function applyAlterations($path, $alterations = []) {
        try {
            $image = CImage_Image::load($path);

            $imageSettings = $this->getOption('alterations');

            if (!empty($alterations)) {
                $imageSettings = $alterations;
            }

            if (empty($imageSettings)) {
                return;
            }

            if (!empty($imageSettings['resize']['width'])) {
                $image->width($imageSettings['resize']['width']);
            }

            if (!empty($imageSettings['resize']['height'])) {
                $image->height($imageSettings['resize']['height']);
            }

            if (!empty($imageSettings['optimize'])) {
                $image->optimize();
            }

            if (!empty($imageSettings['adjustments']['brightness'])) {
                $image->brightness($imageSettings['adjustments']['brightness']);
            }

            if (!empty($imageSettings['adjustments']['contrast'])) {
                $image->contrast($imageSettings['adjustments']['contrast']);
            }

            if (!empty($imageSettings['adjustments']['gamma'])) {
                $image->gamma($imageSettings['adjustments']['gamma']);
            }

            if (!empty($imageSettings['effects']['blur'])) {
                $image->blur($imageSettings['effects']['blur']);
            }

            if (!empty($imageSettings['effects']['pixelate'])) {
                $image->pixelate($imageSettings['effects']['pixelate']);
            }

            if (!empty($imageSettings['effects']['greyscale'])) {
                $image->greyscale();
            }
            if (!empty($imageSettings['effects']['sepia'])) {
                $image->sepia();
            }

            if (!empty($imageSettings['effects']['sharpen'])) {
                $image->sharpen($imageSettings['effects']['sharpen']);
            }

            $image->save();
        } catch (CImage_Exception_InvalidManipulationException $exception) {
            c::report($exception);
        }
    }

    /**
     * @param $path
     *
     * @return array
     */
    private function applyThumbnails($path) {
        $thumbnailSettings = $this->getOption('thumbnails');

        $generatedThumbnails = [];

        if (!empty($thumbnailSettings)) {
            foreach ($thumbnailSettings as $thumbnailName => $setting) {
                $filename = pathinfo($path, PATHINFO_FILENAME);
                $extension = pathinfo($path, PATHINFO_EXTENSION);

                $newThumbnailName = $filename . $thumbnailName . '.' . $extension;
                $newThumbnailPath = $this->path . '/' . $newThumbnailName;

                c::disk($this->disk)->copy($path, $newThumbnailPath);

                if ($this->disk !== 'local-temp') {
                    c::disk('local-temp')->copy($path, $newThumbnailPath);
                    $newPath = c::disk('local-temp')->path($newThumbnailPath);
                } else {
                    $newPath = c::disk($this->disk)->path($newThumbnailPath);
                }

                $this->applyAlterations($newPath, $setting);

                $generatedThumbnails[] = CStorage::instance()->disk($this->disk)->url($newThumbnailPath);
            }
        }

        return $generatedThumbnails;
    }

    /**
     * @param $path
     */
    private function deleteThumbnails($path) {
        $thumbnailSettings = $this->getOption('thumbnails');

        if (!empty($thumbnailSettings)) {
            foreach ($thumbnailSettings as $thumbnailName => $setting) {
                $filename = pathinfo($path, PATHINFO_FILENAME);
                $extension = pathinfo($path, PATHINFO_EXTENSION);

                $newThumbnailName = $filename . $thumbnailName . '.' . $extension;
                $newThumbnailPath = $this->path . '/' . $newThumbnailName;

                c::disk('local-temp')->delete($path, $newThumbnailPath);
            }
        }
    }
}

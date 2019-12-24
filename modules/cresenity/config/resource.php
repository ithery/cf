<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 2, 2019, 12:31:21 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
return array(
    /*
     * The disk on which to store added files and derived images by default. Choose
     * one or more of the disks you've configured in config/filesystems.php.
     */
    'disk' => 'local',
    /*
     * The maximum file size of an item in bytes.
     * Adding a larger file will result in an exception.
     */
    'maxFileSize' => 1024 * 1024 * 10,
    /*
     * This queue will be used to generate derived and responsive images.
     * Leave empty to use the default queue.
     */
    'queueName' => '',
    /*
     * The fully qualified class name of the media model.
     */
    'resourceModel' => 'CApp_Model_Resource',
    'resource_model' => 'CApp_Model_Resource',
    's3' => [
    /*
     * The domain that should be prepended when generating urls.
     */
    //'domain' => 'https://' . env('AWS_BUCKET') . '.s3.amazonaws.com',
    ],
    'remote' => [
        /*
         * Any extra headers that should be included when uploading media to
         * a remote disk. Even though supported headers may vary between
         * different drivers, a sensible default has been provided.
         *
         * Supported by S3: CacheControl, Expires, StorageClass,
         * ServerSideEncryption, Metadata, ACL, ContentEncoding
         */
        'extra_headers' => [
            'CacheControl' => 'max-age=604800',
        ],
    ],
    'responsiveImages' => [
        /*
         * This class is responsible for calculating the target widths of the responsive
         * images. By default we optimize for filesize and create variations that each are 20%
         * smaller than the previous one. More info in the documentation.
         *
         * https://docs.spatie.be/laravel-medialibrary/v7/advanced-usage/generating-responsive-images
         */
        //'width_calculator' => Spatie\MediaLibrary\ResponsiveImages\WidthCalculator\FileSizeOptimizedWidthCalculator::class,
        /*
         * By default rendering media to a responsive image will add some javascript and a tiny placeholder.
         * This ensures that the browser can already determine the correct layout.
         */
        'useTinyPlaceholders' => true,
    /*
     * This class will generate the tiny placeholder used for progressive image loading. By default
     * the medialibrary will use a tiny blurred jpg image.
     */
    //'tiny_placeholder_generator' => Spatie\MediaLibrary\ResponsiveImages\TinyPlaceholderGenerator\Blurred::class,
    ],
    /*
     * When urls to files get generated, this class will be called. Leave empty
     * if your files are stored locally above the site root or on s3.
     */
    'urlGenerator' => null,
    /*
     * The class that contains the strategy for determining a media file's path.
     */
    'pathGenerator' => null,
    /*
     * Medialibrary will try to optimize all converted images by removing
     * metadata and applying a little bit of compression. These are
     * the optimizers that will be used by default.
     */
    'imageOptimizers' => [
        CImage_Optimizer_Jpegoptim::class => [
            '--strip-all', // this strips out all text information such as comments and EXIF data
            '--all-progressive', // this will make sure the resulting image is a progressive one
        ],
//        CImage_Optimizer_Pngquant::class => [
//            '--force', // required parameter for this package
//        ],
//        CImage_Optimizer_Optipng::class => [
//            '-i0', // this will result in a non-interlaced, progressive scanned image
//            '-o2', // this set the optimization level to two (multiple IDAT compression trials)
//            '-quiet', // required parameter for this package
//        ],
//        CImage_Optimizer_Svgo::class => [
//            '--disable=cleanupIDs', // disabling because it is known to cause troubles
//        ],
//        CImage_Optimizer_Gifsicle::class => [
//            '-b', // required parameter for this package
//            '-O3', // this produces the slowest but best results
//        ],
    ],
    /*
     * These generators will be used to create an image of media files.
     */
    'imageGenerators' => [
        CResources_ImageGenerator_FileType_ImageType::class,
//        CResources_ImageGenerator_FileType_WebpType::class,
//        CResources_ImageGenerator_FileType_PdfType::class,
//        CResources_ImageGenerator_FileType_SvgType::class,
//        CResources_ImageGenerator_FileType_VideoType::class,
    ],
    /*
     * The engine that should perform the image conversions.
     * Should be either `gd` or `imagick`.
     */
    'imageDriver' => 'gd',
    /*
     * FFMPEG & FFProbe binaries paths, only used if you try to generate video
     * thumbnails and have installed the php-ffmpeg/php-ffmpeg composer
     * dependency.
     */
    'ffmpegPath' => '/usr/bin/ffmpeg',
    'ffprobePath' => '/usr/bin/ffprobe',
    /*
     * The path where to store temporary files while performing image conversions.
     * If set to null, storage_path('medialibrary/temp') will be used.
     */
    'temporaryDirectoryPath' => null,
    /*
     * Here you can override the class names of the jobs used by this package. Make sure
     * your custom jobs extend the ones provided by the package.
     */
    'taskQueue' => [
//        'performConversions' => CResources_TaskQueue_PerformConversions::class,
//        'generate_responsive_images' => CResources_TaskQueue_PerformConversions_GenerateResponsiveImages::class,
    ],
);

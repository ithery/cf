<?php
use Aws\S3\S3Client;
use TusPhp\Tus\Server;
use League\Flysystem\AwsS3v3\AwsS3V3Adapter;

class CStorage_Tus {
    public static function createServer($basePath = null) {
        $server = new Server('file');
        if ($basePath == null) {
            $basePath = DOCROOT . 'temp/tus/' . CF::appCode() . '';
        }
        if (!CFile::isDirectory($basePath)) {
            CFile::makeDirectory($basePath, 0755, true);
        }
        $server->setUploadDir($basePath);

        return $server;
    }

    // public static function createServer() {
    //     // redis connection
    //     $predis = new Predis\Client('tcp://127.0.0.1:6379');

    //     // AWS S3 Client
    //     $S3client = new S3Client([
    //         'credentials' => [
    //             'key' => 'your-key',
    //             'secret' => 'your-secret',
    //         ],
    //         'region' => 'your-region',
    //         'version' => 'latest|version',
    //     ]);

    //     $server = new TusPhpS3\Server(
    //         new TusPhpS3\Cache\PredisCache($predis),
    //         new AwsS3V3Adapter($S3client, 'your-bucket-name', 'optional/path/prefix'),
    //         new TusPhpS3\Http\Request(HttpRequest::createFromGlobals()),
    //         ['id'],
    //         true
    //     );

    //     return $server;
    // }
}

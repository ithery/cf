<?php


class CHTTP_FileServeDriver {
    public static function existsInApplication($uri) {
        return CFile::exists(static::applicationPathFromUri($uri));
    }

    public static function existsInPublic($uri) {
        return CFile::exists(static::publicPathFromUri($uri));
    }

    public static function applicationPathFromUri($uri) {
        return DOCROOT . $uri;
    }

    public static function publicPathFromUri($uri) {
        return DOCROOT . 'public' . DS . $uri;
    }

    public static function responseStaticFile($uri) {
        if (static::existsInApplication($uri)) {
            $appPath = static::applicationPathFromUri($uri);
            $publicPath = static::publicPathFromUri($uri);

            if (!static::existsInPublic($uri)) {
                $dirname = CFile::dirname($publicPath);
                if (!CFile::isDirectory($dirname)) {
                    CFile::makeDirectory($dirname, 0755, true);
                }
                CFile::copy($appPath, $publicPath);
            }
            $file = $publicPath;

            $contentType = mime_content_type($file);
            $ext = pathinfo($file, PATHINFO_EXTENSION);

            if ($ext == 'css') {
                $contentType = 'text/css';
            }
            if ($ext == 'js') {
                $contentType = 'application/javascript';
            }
            return c::response()->file($file, [
                'Content-Type' => $contentType,
            ]);
        }
    }

    public static function clearPublic() {
        $path = [];
        $path[] = DOCROOT . 'public' . DS . 'media';
        $path[] = DOCROOT . 'public' . DS . 'application' . DS . CF::appCode();
        $path[] = DOCROOT . 'public' . DS . 'compiled';
        foreach ($path as $p) {
            CFile::deleteDirectory($p);
        }
    }
}

<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 1, 2019, 11:30:32 PM
 */
class CModel_HasResource_FileAdder_FileAdderFactory {
    /**
     * @param CModel                                                     $subject
     * @param string|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return CModel_HasResource_FileAdder_FileAdder
     */
    public static function create(CModel $subject, $file) {
        $fileAdder = new CModel_HasResource_FileAdder_FileAdder(new CResources_Filesystem());
        return $fileAdder->setSubject($subject)->setFile($file);
    }

    public static function createFromDisk(CModel $subject, $key, $disk) {
        /** @var CModel_HasResource_FileAdder_FileAdder $fileAdder */
        $fileAdder = new CModel_HasResource_FileAdder_FileAdder(new CResources_Filesystem());

        return $fileAdder
            ->setSubject($subject)
            ->setFile(new CResources_Support_RemoteFile($key, $disk));
    }

    public static function createFromRequest(CModel $subject, $key) {
        return static::createMultipleFromRequest($subject, [$key])->first();
    }

    public static function createMultipleFromRequest(CModel $subject, array $keys = []) {
        return c::collect($keys)->map(function ($key) use ($subject) {
            if (!CHTTP::request()->hasFile($key)) {
                throw RequestDoesNotHaveFile::create($key);
            }
            $files = CHTTP::request()->file($key);
            if (!is_array($files)) {
                return static::create($subject, $files);
            }
            return array_map(function ($file) use ($subject) {
                return static::create($subject, $file);
            }, $files);
        })
                        ->flatten();
    }

    public static function createAllFromRequest(CModel $subject) {
        $fileKeys = array_keys(CHTTP::request()->allFiles());
        return static::createMultipleFromRequest($subject, $fileKeys);
    }
}

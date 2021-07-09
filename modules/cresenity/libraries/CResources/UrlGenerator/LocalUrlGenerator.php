<?php

class CResources_UrlGenerator_LocalUrlGenerator extends CResources_UrlGeneratorAbstract {
    /**
     * Get the url for a resource item.
     *
     * @return string
     *
     * @throws CResources_Exception_UrlCannotBeDetermined
     */
    public function getUrl() {
        $url = rtrim($this->getBaseResourceDirectoryUrl(), '/') . '/' . $this->getPathRelativeToRoot();
        $url = $this->makeCompatibleForNonUnixHosts($url);
        $url = $this->rawUrlEncodeFilename($url);
        return $url;
    }

    /**
     * @param \DateTimeInterface $expiration
     * @param array              $options
     *
     * @return string
     *
     * @throws \Spatie\MediaLibrary\Exceptions\UrlCannotBeDetermined
     */
    public function getTemporaryUrl(DateTimeInterface $expiration, array $options = []) {
        throw CResources_Exception_UrlCannotBeDetermined::filesystemDoesNotSupportTemporaryUrls();
    }

    /**
     * Get the path for the profile of a resource item.
     */
    public function getPath() {
        return rtrim($this->getStoragePath(), '/') . '/' . $this->getPathRelativeToRoot();
    }

    protected function getBaseResourceDirectoryUrl() {
        if ($diskUrl = CF::config("storage.disks.{$this->resource->disk}.url")) {
            return str_replace(c::url('/'), '', $diskUrl);
        }
        if (!cstr::startsWith(c::fixPath($this->getStoragePath()), c::fixPath(DOCROOT))) {
            throw CResources_Exception_UrlCannotBeDetermined::resourceNotPubliclyAvailable($this->getStoragePath(), DOCROOT);
        }
        return rtrim($this->getBaseResourceDirectory(), '/');
    }

    /**
     * Get the directory where all files of the resource item are stored.
     */
    protected function getBaseResourceDirectory() {
        return str_replace(DOCROOT, '', $this->getStoragePath());
    }

    /**
     * Get the path where the whole resource library is stored.
     */
    protected function getStoragePath() {
        $diskRootPath = CF::config("storage.disks.{$this->resource->disk}.root");
        if ($diskRootPath == null) {
            return rtrim(DOCROOT);
        }
        return rtrim(realpath($diskRootPath), '/') . '/';
    }

    protected function makeCompatibleForNonUnixHosts($url) {
        if (DIRECTORY_SEPARATOR != '/') {
            $url = str_replace(DIRECTORY_SEPARATOR, '/', $url);
        }
        return $url;
    }

    /**
     * Get the url to the directory containing responsive images.
     *
     * @return string
     */
    public function getResponsiveImagesDirectoryUrl() {
        return c::url($this->getBaseResourceDirectoryUrl() . '/' . $this->pathGenerator->getPathForResponsiveImages($this->resource)) . '/';
    }
}

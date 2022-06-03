<?php

class CResources_UrlGenerator_DefaultUrlGenerator extends CResources_UrlGeneratorAbstract {
    public function getUrl() {
        $url = $this->getDisk()->url($this->getPathRelativeToRoot());

        $url = $this->versionUrl($url);

        return $url;
    }

    public function getTemporaryUrl(DateTimeInterface $expiration, array $options = []) {
        return $this->getDisk()->temporaryUrl($this->getPathRelativeToRoot(), $expiration, $options);
    }

    public function getBaseMediaDirectoryUrl() {
        return $this->getDisk()->url('/');
    }

    public function getPath() {
        $adapter = $this->getDisk()->getAdapter();

        if ($adapter instanceof \League\Flysystem\Cached\CachedAdapter) {
            $adapter = $adapter->getAdapter();
        }

        $pathPrefix = $adapter->getPathPrefix();

        return $pathPrefix . $this->getPathRelativeToRoot();
    }

    public function getResponsiveImagesDirectoryUrl() {
        $base = cstr::finish($this->getBaseMediaDirectoryUrl(), '/');

        $path = $this->pathGenerator->getPathForResponsiveImages($this->media);

        return cstr::finish(c::url($base . $path), '/');
    }
}

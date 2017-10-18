<?php

// namespace League\Flysystem;

interface League_Flysystem_PluginInterface
{
    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod();

    /**
     * Set the Filesystem object.
     *
     * @param FilesystemInterface $filesystem
     */
    public function setFilesystem(League_Flysystem_FilesystemInterface $filesystem);
}

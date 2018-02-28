<?php

namespace buzzingpixel\ansel\services;

use League\Flysystem\Filesystem;
use Ramsey\Uuid\UuidFactoryInterface;

/**
 * Class FileCacheService
 */
class FileCacheService
{
    const ANSEL_CACHE = 'anselCache';
    const ANSEL_CACHE_PERSISTENT = 'anselPersistent';

    /** @var UuidFactoryInterface $uuid */
    private $uuid;

    /** @var Filesystem $fileSystem */
    private $fileSystem;

    /**
     * FileCacheService constructor
     * @param UuidFactoryInterface $uuid
     * @param Filesystem $fileSystem
     * @throws \Exception
     */
    public function __construct(UuidFactoryInterface $uuid, Filesystem $fileSystem)
    {
        $this->uuid = $uuid;
        $this->fileSystem = $fileSystem;
        $this->ensurePathsExist();
        $this->cleanUp();
    }

    /**
     * Cleans things up
     */
    private function cleanUp()
    {
        // Clean up
        $this->cleanUpDir($this->getCachePath());
    }

    /**
     * Cleans up directory
     * @param string $dirPath
     */
    private function cleanUpDir(string $dirPath)
    {
        $dirPath = rtrim($dirPath, '/');

        // Some environments can't distinguish between empty match and an error
        $glob = glob("{$dirPath}/*") ?: [];

        foreach ($glob as $item) {
            if (is_dir($item)) {
                $this->cleanUpDir($item);

                $items = glob("{$item}/*");

                if (! $items) {
                    rmdir($item);
                }

                continue;
            }

            if (strtotime('+ 1 day', filemtime($item)) < time()) {
                // Delete the file
                unlink($item);
            }
        }
    }

    /**
     * Ensure paths exist
     * @throws \Exception
     */
    private function ensurePathsExist()
    {
        $this->ensurePathExists(self::ANSEL_CACHE);
        $this->ensurePathExists(self::ANSEL_CACHE_PERSISTENT);
    }

    /**
     * Ensures a path exists
     * @param string $path
     * @throws \Exception
     */
    private function ensurePathExists(string $path)
    {
        if ($this->fileSystem->has("{$path}/.gitignore")) {
            return;
        }

        $this->fileSystem->write("{$path}/.gitignore", "*\n");
    }

    /**
     * Gets the Ansel Cache Path
     * @return string
     */
    public function getCachePath() : string
    {
        return $this->fileSystem->getAdapter()->getPathPrefix() .
            self::ANSEL_CACHE;
    }

    /**
     * Gets the Ansel Cache Path
     * @return string
     */
    public function getPersistentCachePath() : string
    {
        return $this->fileSystem->getAdapter()->getPathPrefix() .
            self::ANSEL_CACHE_PERSISTENT;
    }

    /**
     * Checks if a cache file exists
     * @param $fileName
     * @param bool $persistent
     * @return bool
     */
    public function cacheFileExists($fileName, $persistent = false) : bool
    {
        $path = $persistent ? self::ANSEL_CACHE_PERSISTENT : self::ANSEL_CACHE;
        $pathName = "{$path}/{$fileName}";
        $fullPath = $persistent ?
            $this->getPersistentCachePath() :
            $this->getCachePath();
        $fullPath .= "/{$fileName}";
        return $this->fileSystem->has($pathName) && ! is_dir($fullPath);
    }

    /**
     * Creates an empty cache file
     * @param string $ext
     * @param bool $persistent
     * @return string
     * @throws \Exception
     */
    public function createEmptyFile(string $ext = '', $persistent = false) : string
    {
        $ext = $ext ? ".{$ext}" : '';
        $cachePath = $persistent ? self::ANSEL_CACHE_PERSISTENT : self::ANSEL_CACHE;
        $cacheFile = "{$this->uuid->uuid4()->toString()}{$ext}";
        $this->fileSystem->write("{$cachePath}/{$cacheFile}", '');
        return $cacheFile;
    }

    /**
     * Sets a cache file
     * @param string $fileName
     * @param $contents
     * @param bool $persistent
     * @throws \Exception
     */
    public function setCacheFile(
        string $fileName,
        $contents,
        $persistent = false
    ) {
        $cachePath = $persistent ? self::ANSEL_CACHE_PERSISTENT : self::ANSEL_CACHE;
        $fullPath = "{$cachePath}/{$fileName}";
        $this->fileSystem->write($fullPath, $contents);
    }

    /**
     * Gets cache file contents
     * @param string $fileName
     * @param bool $persistent
     * @return string|bool
     * @throws \Exception
     */
    public function getCacheFileContents(
        string $fileName,
        $persistent = false
    ) {
        $cachePath = $persistent ? self::ANSEL_CACHE_PERSISTENT : self::ANSEL_CACHE;
        $fullPath = "{$cachePath}/{$fileName}";
        return $this->fileSystem->read($fullPath);
    }
}

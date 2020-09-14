<?php

namespace Midun\Storage;

class Storage
{
    /**
     * Disk working
     * 
     * @var string
     */
    protected string $disk;

    /**
     * Set disk setting
     * 
     * @param string $disk
     * 
     * @return self
     */
    public function disk(string $disk): Storage
    {
        $this->disk = $disk;
        return $this;
    }

    /**
     * Check exists file storage
     * 
     * @param string $fileName
     * 
     * @return boolean
     */
    public function exists(string $fileName): bool
    {
        return file_exists($this->getFullDirectoryWithDisk($fileName));
    }

    /**
     * Get current disk
     * 
     * @return string
     */
    public function getDisk(): string
    {
        return $this->disk;
    }

    /**
     * Get full directory with disk
     * 
     * @param string $fileName
     * 
     * @return string
     */
    public function getFullDirectoryWithDisk(string $fileName = ""): string
    {
        return ($this->getWorkingDirectory() . DIRECTORY_SEPARATOR . $this->getDisk()) . ($fileName ? DIRECTORY_SEPARATOR . $fileName : "");
    }

    /**
     * Get url for internet
     * 
     * @param string $fileName
     * 
     * @return string
     */
    public function url(string $fileName): string
    {
        return config('app.url') . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . $fileName;
    }

    /**
     * Get info of file storage
     * 
     * @param string $fileName
     * 
     * @return array|null
     */
    public function info(string $fileName): ?array
    {
        if ($this->exists($fileName)) {
            $filePath = $this->getWorkingDirectory() . $fileName;
            $fileStorage = new FileStorage($filePath);

            return $fileStorage->getFileStorageInfo();
        }

        return null;
    }

    /**
     * Get full path of fileName
     * 
     * @param string $fileName
     * 
     * @return string|null
     */
    public function realPath(string $fileName): ?string
    {
        if ($this->exists($fileName)) {
            $filePath = $this->getWorkingDirectory() . $fileName;
            $fileStorage = new FileStorage($filePath);

            return $fileStorage->getRealPath();
        }

        return null;
    }

    /**
     * Copy a file to a new location
     * 
     * @param string $fileName
     * @param string $target
     * 
     * @throws StorageException
     * 
     * @return bool
     */
    public function copy(string $fileName, string $target): bool
    {
        try {
            if ($this->exists($fileName)) {
                $filePath = $this->getWorkingDirectory() . $fileName;
                $fileStorage = new FileStorage($filePath);

                $realPath = $fileStorage->getRealPath();

                $targetPath = $this->getWorkingDirectory() . $target;

                return copy($realPath, $targetPath);
            }
        } catch (\Exception $e) {
            throw new StorageException($e->getMessage());
        }
    }

    /**
     * Copy a file to a new location
     * 
     * @param string $fileName
     * @param string $target
     * 
     * @throws StorageException
     * 
     * @return bool
     */
    public function move(string $fileName, string $target): bool
    {
        try {
            if ($this->exists($fileName)) {
                $filePath = $this->getWorkingDirectory() . $fileName;
                $fileStorage = new FileStorage($filePath);

                $realPath = $fileStorage->getRealPath();

                $targetPath = $this->getWorkingDirectory() . $target;

                return rename($realPath, $targetPath);
            }
        } catch (\Exception $e) {
            throw new StorageException($e->getMessage());
        }
    }

    /**
     * Upload file to storage
     * 
     * @param \Midun\Services\File $file
     * @param string $fileName
     * @param string $directory
     * 
     * @return string|null
     * 
     * @throws StorageException
     */
    public function put(\Midun\Services\File $file, ?string $fileName = null, ?string $directory = null): ?string
    {
        try {
            $tmpName = $file->getTmpName();

            $fileName = !is_null($fileName) ? $fileName : generateRandomString(40);

            $fileName = $fileName . '.' . $file->getFileExtension();

            $uploadTo = !is_null($directory) ? $this->getWorkingDirectory() . $directory . DIRECTORY_SEPARATOR . $fileName : $this->getWorkingDirectory() . $fileName;

            if (true === move_uploaded_file($tmpName, $uploadTo)) {

                return $fileName;
            }

            return null;
        } catch (\Exception $e) {
            throw new StorageException($e->getMessage());
        }
    }

    /**
     * Upload file to storage as custom information
     * 
     * @param \Midun\Services\File $file
     * @param string $fileName
     * @param string $directory
     * 
     * @return string|null
     */
    public function putAs(\Midun\Services\File $file, string $directory, string $fileName): ?string
    {
        $fullDir = $this->getWorkingDirectory();

        foreach (explode('/', $directory) as $dir) {
            $fullDir .= $dir . DIRECTORY_SEPARATOR;

            if (false === check_dir($fullDir)) {
                mkdir($fullDir, 0755, true);
            }
        }

        return $this->put($file, $fileName, $directory);
    }

    /**
     * Delete file storage
     * 
     * @param string $fileName
     * 
     * @return bool
     * 
     * @throws StorageException
     */
    public function delete(string $fileName): bool
    {
        try {
            if ($this->exists($fileName)) {
                $filePath = $this->getWorkingDirectory() . $fileName;
                $fileStorage = new FileStorage($filePath);

                $realPath = $fileStorage->getRealPath();

                return unlink($realPath);
            }
        } catch (\Exception $e) {
            throw new StorageException($e->getMessage());
        }
    }

    /**
     * Get file storage instance
     * 
     * @param string $fileName
     * 
     * @throws StorageException
     * 
     * @return FileStorage
     */
    public function get(string $fileName): FileStorage
    {
        if ($this->exists($fileName)) {
            $filePath = $this->getFullDirectoryWithDisk($fileName);
            return new FileStorage($filePath);
        }

        throw new StorageException("file {$fileName} doesn't exists");
    }

    /**
     * Get current disk
     * 
     * @return string
     */
    public function getCurrentDisk(): string
    {
        return $this->disk;
    }

    /**
     * Get working directory
     * 
     * @return string
     */
    public function getWorkingDirectory(): string
    {
        return config("storage.{$this->disk}.root");
    }
}

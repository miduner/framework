<?php

namespace Midun\Logger;

use Midun\FileSystem\FileSystem;

class Logger
{
    /**
     * File system
     * 
     * @var FileSystem
     */
    protected FileSystem $fileSystem;

    /**
     * Log directory
     * 
     * @var string
     */
    protected string $directory;

    /**
     * Create log file by date
     * 
     * @var boolean
     */
    protected bool $byDate = false;

    /**
     * Constructor of Logger
     * 
     * @param string $directory
     */
    public function __construct(?string $directory = null)
    {
        if (!is_null($directory)) $this->setDirectory($directory);

        $this->setFileSystem(
            \Midun\Container::getInstance()->make('fileSystem')
        );
    }

    /**
     * Set directory for logger
     * 
     * @param string $directory
     * 
     * @return self
     */
    public function setDirectory(string $directory): Logger
    {
        $this->directory = $directory;
        return $this;
    }

    /**
     * Get directory of logger
     * 
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * Set log by date
     * 
     * @param int $byDate
     * 
     * @return self
     */
    public function setWriteLogByDate(int $byDate): Logger
    {
        $this->byDate = $byDate;
        return $this;
    }

    /**
     * Check is write log by date
     * 
     * @return boolean
     */
    public function isLogByDate(): bool
    {
        return $this->byDate;
    }

    /**
     * Set file system
     * 
     * @param FileSystem $fileSystem
     * 
     * @return void
     */
    protected function setFileSystem(FileSystem $fileSystem): void
    {
        $this->fileSystem = $fileSystem;
    }

    /**
     * Get file system
     * 
     * @return FileSystem
     */
    protected function getFileSystem(): FileSystem
    {
        return $this->fileSystem;
    }

    /**
     * Write down log message
     * 
     * @param string $level
     * @param mixed $message
     * @param string $directory = null
     * @param string $fileName
     * @param bool $byDate
     * 
     * @return int
     */
    public function writeLog(string $level, $message, ?string $directory = null, ?string $fileName = null, ?bool $byDate = null): int
    {
        $directory = !is_null($directory) ? $directory : $this->getDirectory();
        $byDate = !is_null($byDate) ? $byDate : $this->isLogByDate();
        $time = date('Y-m-d H:i:s');
        $fileName = !is_null($fileName) ? $fileName : 'application';
        $file = $byDate ? $fileName . '-' . date('Y-m-d') . '.log' : $fileName . '.log';

        $endPoint = $directory . DIRECTORY_SEPARATOR . $file;
        $message = "[{$level}] [{$time}] {$message}" . PHP_EOL;

        return $this->getFileSystem()->append(
            $endPoint,
            $message
        );
    }

    /**
     * Handle call function
     * 
     * @param string $method
     * @param array $args
     * 
     * @return mixed
     * 
     * @throws LoggerException
     */
    public function __call(string $method, array $args)
    {
        switch ($method) {
            case 'info':
            case 'error':
            case 'debug':
            case 'warning':
                return $this->writeLog(strtoupper($method), ...$args);
            default:
                throw new LoggerException("Logger method {$method} is not supported.");
        }
    }
}

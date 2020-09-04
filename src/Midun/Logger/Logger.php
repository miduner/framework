<?php

namespace Midun\Logger;

class Logger
{
    /**
     * Log directory
     * 
     * @var string
     */
    protected $directory;

    /**
     * Create log file by date
     * 
     * @var boolean
     */
    protected $byDate = false;

    /**
     * Constructor of Logger
     */
    public function __construct(string $directory = null)
    {
        $this->directory = $directory;
    }

    /**
     * Set directory for logger
     * 
     * @param string $directory
     * 
     * @return self
     */
    public function setDirectory(string $directory)
    {
        $this->directory = $directory;
        return $this;
    }

    /**
     * Get directory of logger
     * 
     * @return string
     */
    public function getDirectory()
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
    public function setWriteLogByDate(int $byDate)
    {
        $this->byDate = $byDate;
        return $this;
    }

    /**
     * Check is write log by date
     * 
     * @return boolean
     */
    public function isLogByDate()
    {
        return $this->byDate;
    }

    /**
     * Write down log message
     * 
     * @param string $level
     * @param mixed $message
     * @param string $directory = null
     * @param bool $byDate
     * 
     * @return int/false
     */
    public function writeLog($level, $message, $directory = null, $fileName = null, $byDate = null)
    {
        $directory = !is_null($directory) ? $directory : $this->getDirectory();

        $byDate = !is_null($byDate) ? $byDate : $this->isLogByDate();

        $time = date('Y-m-d H:i:s');

        $fileName = !is_null($fileName) ? $fileName : 'application';

        $file = $byDate ? $fileName . '-' . date('Y-m-d') . '.log' : $fileName . '.log';

        return file_put_contents(
            $directory . DIRECTORY_SEPARATOR . $file,
            "[{$level}] [{$time}] {$message}" . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }

    /**
     * Handle call function
     * 
     * @param string $method
     * @param array $args
     * 
     * @return mixed
     */
    public function __call($method, $args)
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

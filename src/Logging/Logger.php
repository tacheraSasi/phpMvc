<?php

namespace Viper\Logging;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Simple Logger implementation
 */
class Logger
{
    protected string $logPath;
    protected string $dateFormat = 'Y-m-d H:i:s';
    protected array $levels = [
        'emergency' => 0,
        'alert' => 1,
        'critical' => 2,
        'error' => 3,
        'warning' => 4,
        'notice' => 5,
        'info' => 6,
        'debug' => 7
    ];
    protected int $minLevel;
    
    public function __construct(string $logPath = null, string $minLevel = 'debug')
    {
        $this->logPath = $logPath ?? $this->getDefaultLogPath();
        $this->minLevel = $this->levels[$minLevel] ?? 7;
        
        // Ensure log directory exists
        $this->ensureLogDirectory();
    }
    
    /**
     * Log an emergency message
     */
    public function emergency(string $message, array $context = []): void
    {
        $this->log('emergency', $message, $context);
    }
    
    /**
     * Log an alert message
     */
    public function alert(string $message, array $context = []): void
    {
        $this->log('alert', $message, $context);
    }
    
    /**
     * Log a critical message
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log('critical', $message, $context);
    }
    
    /**
     * Log an error message
     */
    public function error(string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }
    
    /**
     * Log a warning message
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }
    
    /**
     * Log a notice message
     */
    public function notice(string $message, array $context = []): void
    {
        $this->log('notice', $message, $context);
    }
    
    /**
     * Log an info message
     */
    public function info(string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }
    
    /**
     * Log a debug message
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }
    
    /**
     * Log a message with given level
     */
    public function log(string $level, string $message, array $context = []): void
    {
        if (!isset($this->levels[$level]) || $this->levels[$level] > $this->minLevel) {
            return;
        }
        
        $timestamp = date($this->dateFormat);
        $levelUpper = strtoupper($level);
        
        // Interpolate context into message
        $message = $this->interpolate($message, $context);
        
        // Format log entry
        $logEntry = "[$timestamp] $levelUpper: $message";
        
        // Add context if present
        if (!empty($context)) {
            $contextString = $this->formatContext($context);
            $logEntry .= " | Context: $contextString";
        }
        
        $logEntry .= PHP_EOL;
        
        // Write to log file
        file_put_contents($this->logPath, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Interpolate context values into message placeholders
     */
    protected function interpolate(string $message, array $context): string
    {
        $replace = [];
        
        foreach ($context as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $replace['{' . $key . '}'] = $value;
            }
        }
        
        return strtr($message, $replace);
    }
    
    /**
     * Format context array for logging
     */
    protected function formatContext(array $context): string
    {
        $formatted = [];
        
        foreach ($context as $key => $value) {
            if ($value instanceof \Throwable) {
                $formatted[$key] = get_class($value) . ': ' . $value->getMessage();
            } elseif (is_object($value)) {
                $formatted[$key] = get_class($value);
            } elseif (is_array($value)) {
                $formatted[$key] = json_encode($value);
            } else {
                $formatted[$key] = (string) $value;
            }
        }
        
        return json_encode($formatted);
    }
    
    /**
     * Get default log path
     */
    protected function getDefaultLogPath(): string
    {
        $logDir = dirname(ROOTPATH) . '/storage/logs';
        return $logDir . '/app-' . date('Y-m-d') . '.log';
    }
    
    /**
     * Ensure log directory exists
     */
    protected function ensureLogDirectory(): void
    {
        $directory = dirname($this->logPath);
        
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }
    
    /**
     * Get log file path
     */
    public function getLogPath(): string
    {
        return $this->logPath;
    }
    
    /**
     * Set minimum log level
     */
    public function setMinLevel(string $level): void
    {
        if (isset($this->levels[$level])) {
            $this->minLevel = $this->levels[$level];
        }
    }
}
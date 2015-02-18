<?php
namespace Fruty\Environment;

use stdClass;
use InvalidArgumentException;

/**
 * Class Env
 * @package Fruty\Environment
 */
class Env 
{
    /**
     * @var stdClass
     */
    private $storage;

    /**
     * @var stdClass
     */
    private $cache;

    /**
     * @var EnvReaderInterface
     */
    private $reader;

    /**
     * @var string
     */
    private $envFile;

    /**
     * @var bool
     */
    private $fileNotFoundException = false;

    /**
     * @var array
     */
    private $required = [];

    /**
     * Get singleton instance of Env class
     *
     * @return static
     */
    public static function instance()
    {
        static $instance;
        if (! $instance) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * Load environment variables
     *
     * @param string $path
     * @param string $file
     * @param string $reader
     */
    public function load($path, $file = null, $reader = 'json')
    {
        $this->initialize();
        $file = is_null($file) ? getenv('APP_ENV') : $file;
        $envFile = $this->combineEnvFileName($path, $file, $reader);
        $this->setReader($reader);
        if (is_file($envFile)) {
            $this->envFile = $envFile;
            $this->loadData();
        } elseif ($this->fileNotFoundException) {
            throw new InvalidArgumentException("Environment file '{$file}' not found", 500);
        }
        $this->merge();
        $this->checkRequired();
    }

    /**
     * Check required variables
     *
     * @throws \RuntimeException
     */
    private function checkRequired()
    {
        if ($this->required) {
            foreach ($this->required as $el) {
                if (! isset($this->storage->$el)) {
                    throw new \RuntimeException("Environment variable '{$el}' must be defined", 500);
                }
            }
        }
    }

    /**
     * Merge $_ENV with parsed values
     */
    private function merge()
    {
        $this->storage = (object) array_merge($_ENV, (array)$this->storage);
        foreach ($this->storage as $k => $v) {
            $_ENV[$k] = $v;
            putenv("{$k}=" . json_encode($v));
        }
    }

    /**
     * Throw Exception when env file is not found
     *
     * @param $value
     */
    public function fileNotFoundException($value)
    {
        $this->fileNotFoundException = (bool) $value;
    }

    /**
     * Get env variable value
     *
     * @access public
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        if ($key === null) {
            return $this->storage;
        }
        if (! isset($this->cache->$key)) {
            $this->cache->$key = $this->getFromStorage($this->storage, $key, $default);
        }
        return $this->cache->$key;
    }

    /**
     * Set required env variables
     *
     * @param array $required
     */
    public function required(array $required)
    {
        $this->required = $required;
    }

    /**
     * Get env filename
     *
     * @return string
     */
    public function getEnvFile()
    {
        return $this->getEnvFile;
    }

    /**
     * Get value from storage
     *
     * @access private
     * @param stdClass $storage
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    private function getFromStorage(stdClass $storage, $key, $default = null)
    {
        if (is_null($key)) return $storage;
        if (isset($storage->$key)) return $storage->$key;
        foreach (explode('.', $key) as $segment) {
            if ((! is_object($storage)) || ! isset($storage->$segment)) {
                return $default;
            }
            $storage = $storage->$segment;
        }
        return $storage;
    }

    /**
     * Initialize
     */
    private function initialize()
    {
        $this->storage = new stdClass();
        $this->cache = new stdClass();
    }

    /**
     * Collect env file name from parts
     * @param string $path
     * @param string $file
     * @param string $reader
     * @return string
     */
    private function combineEnvFileName($path, $file, $reader)
    {
        return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file . "." . $reader;
    }

    /**
     * Set reader
     *
     * @param string $reader
     * @return EnvReaderInterface
     */
    private function setReader($reader)
    {
        switch ($reader) {
            case 'json':
                return $this->reader = new Readers\JsonReader();
            case 'php':
                return $this->reader = new Readers\PhpArrayReader();
            case 'ini':
                return $this->reader = new Readers\IniReader();
            case 'xml':
                return $this->reader = new Readers\XmlReader();
            case 'yml':
            case 'yaml':
                return $this->reader = new Readers\YmlReader();
            case 'serialize':
                return $this->reader = new Readers\SerializeReader();
            default:
                throw new InvalidArgumentException("Unknown reader {$reader}", 500);
        }
    }

    /**
     * Load data by reader
     */
    private function loadData()
    {
        $this->storage = $this->reader->run($this->envFile);
    }
}
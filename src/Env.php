<?php
namespace Fruty\Environment;

use stdClass;
use InvalidArgumentException;


class Env 
{
    private $storage;

    private $cache;

    private $reader;

    private $envFile;

    private $share = true;

    private $fileNotFoundException = false;

    /**
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

    public function load($path, $file = null, $reader = 'json')
    {
        $this->initialize();
        $file = is_null($file) ? getenv('APP_ENV') : $file;
        $envFile = $this->combineEnvFileName($path, $file, $reader);
        $this->setReader($reader);
        if (is_file($envFile)) {
            $this->envFile = $envFile;
            $this->loadData();
            if ($this->share) {
                $this->shareLoaded();
            }
        } elseif ($this->fileNotFoundException) {
            throw new InvalidArgumentException("Environment file '{$file}' not found", 500);
        }
    }

    public function share($value)
    {
        $this->share = (bool) $value;
    }

    public function fileNotFoundException($value)
    {
        $this->fileNotFoundException = (bool) $value;
    }

    public function get($key, $default = null)
    {
        if (! isset($this->cache->$key)) {
            $this->cache->$key = $this->getFromStorage($this->storage, $key, $default);
        }
        return $this->cache->$key;
    }

    private function getFromStorage(stdClass $storage, $key, $default = null)
    {
        if (is_null($key)) return $storage;
        if (isset($storage->$key)) return $storage->$key;
        foreach (explode('.', $key) as $segment) {
            if ( (! is_object($storage)) || ! isset($storage->$segment)) {
                return $default;
            }
            $storage = $storage->$segment;
        }
        return $storage;
    }

    private function initialize()
    {
        $this->storage = new stdClass();
        $this->cache = new stdClass();
    }

    private function combineEnvFileName($path, $file, $reader)
    {
        return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file . "." . $reader;
    }

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
                return $this->reader = new Readers\YmlReader();
            default:
                throw new InvalidArgumentException("Unknown reader {$reader}", 500);
        }
    }

    private function loadData()
    {
        $this->storage = $this->reader->run($this->envFile);
    }

    private function shareLoaded()
    {
        foreach ($this->storage as $k => $v) {
            $_ENV[$k] = $v;
            putenv("{$k}={$v}");
        }
    }
}
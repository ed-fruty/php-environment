<?php
namespace Fruty\Environment;

/**
 * Class Runtime
 * @package Fruty\Environment
 */
class Runtime 
{
    /**
     * @return int
     */
    public function getProcessPid()
    {
        return getmypid();
    }

    /**
     * @return int
     */
    public function getProcessGid()
    {
        return getmygid();
    }

    /**
     * @return int
     */
    public function getProcessUid()
    {
        return getmygid();
    }

    /**
     * @return int
     */
    public function getProcessInode()
    {
        return getmyinode();
    }

    /**
     * @return string
     */
    public function getProcessOwner()
    {
        return get_current_user();
    }

    /**
     * @return string
     */
    public function getFullOS()
    {
        return $this->getUName();
    }

    /**
     * @return string
     */
    public function getOsName()
    {
        return $this->getUName('s');
    }

    /**
     * @param null $mode
     * @return string
     */
    public function getUName($mode = null)
    {
        return php_uname($mode);
    }

    /**
     * @return bool
     */
    public function isWindows()
    {
        return (strtoupper(substr($this->getOsName(), 0, 3)) === 'WIN');
    }

    /**
     * @return bool
     */
    public function isLinux()
    {
        return (strtoupper(substr($this->getOsName(), 0, 3)) === 'LIN');
    }

    /**
     * @return bool
     */
    public function isUnix()
    {
        $names = array(
            'CYG',
            'DAR',
            'FRE',
            'HP-',
            'IRI',
            'LIN',
            'NET',
            'OPE',
            'SUN',
            'UNI'
        );
        return in_array($this->getOsName(), $names) !== false;
    }

    /**
     * @return bool
     */
    public function isHHVM()
    {
        return defined('HHVM_VERSION');
    }

    /**
     * @return bool
     */
    public function isPHP()
    {
        return ! $this->isHHVM();
    }

    /**
     * @return bool
     * @param string $extension
     */
    public function hasExtension($extension)
    {
        return extension_loaded($extension);
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        if ($this->isHHVM()) {
            return HHVM_VERSION;
        } else {
            return PHP_VERSION;
        }
    }

    /**
     * @return bool
     */
    public function isCli()
    {
        return substr(strtolower(php_sapi_name()), 0, 3) == 'cli';
    }

    /**
     * @return bool
     */
    public function isWeb()
    {
        return !$this->isCli();
    }

    /**
     * @return string
     */
    public function getPwd()
    {
        return getcwd();
    }

}
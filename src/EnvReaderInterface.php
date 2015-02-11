<?php
namespace Fruty\Environment;

/**
 * Interface EnvReaderInterface
 * @package Fruty\Environment
 */
interface EnvReaderInterface 
{
    /**
     * @param string $file
     * @return \stdClass
     * @throws \RuntimeException
     */
    public function run($file);
}
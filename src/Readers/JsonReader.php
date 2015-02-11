<?php
namespace Fruty\Environment\Readers;

use Fruty\Environment\EnvReaderInterface;

/**
 * Class JsonReader
 * @package Fruty\Environment\Readers
 */
class JsonReader implements EnvReaderInterface
{

    /**
     * @param string $file
     * @return \stdClass
     * @throws \RuntimeException
     */
    public function run($file)
    {
        return json_decode(file_get_contents($file));
    }
}
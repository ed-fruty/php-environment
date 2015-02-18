<?php
namespace Fruty\Environment\Readers;

use Fruty\Environment\EnvReaderInterface;

/**
 * Class SerializeReader
 * @package Fruty\Environment\Readers
 */
class SerializeReader implements EnvReaderInterface
{

    /**
     * @param string $file
     * @return \stdClass
     * @throws \RuntimeException
     */
    public function run($file)
    {
        return json_decode(json_encode(unserialize(file_get_contents($file))));
    }
}
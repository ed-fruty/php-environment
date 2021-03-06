<?php
namespace Fruty\Environment\Readers;

use Fruty\Environment\EnvReaderInterface;

/**
 * Class PhpArrayReader
 * @package Fruty\Environment\Readers
 */
class PhpArrayReader implements EnvReaderInterface
{

    /**
     * @param string $file
     * @return \stdClass
     * @throws \RuntimeException
     */
    public function run($file)
    {
        return json_decode(json_encode(require $file));
    }
}
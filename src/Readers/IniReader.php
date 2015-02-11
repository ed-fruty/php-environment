<?php
namespace Fruty\Environment\Readers;

use Fruty\Environment\EnvReaderInterface;

/**
 * Class IniReader
 * @package Fruty\Environment\Readers
 */
class IniReader implements EnvReaderInterface
{

    /**
     * @param string $file
     * @return \stdClass
     * @throws \RuntimeException
     */
    public function run($file)
    {
        return json_decode(json_encode(parse_ini_file($file, true)));
    }
}
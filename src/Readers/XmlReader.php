<?php
namespace Fruty\Environment\Readers;

use Fruty\Environment\EnvReaderInterface;
use SimpleXMLElement;

class XmlReader implements EnvReaderInterface
{

    /**
     * @param string $file
     * @return \stdClass
     * @throws \RuntimeException
     */
    public function run($file)
    {
        return json_decode(json_encode((new SimpleXMLElement($file, 0, true))));
    }
}
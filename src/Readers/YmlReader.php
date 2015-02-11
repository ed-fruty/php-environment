<?php
namespace Fruty\Environment\Readers;

use Fruty\Environment\EnvReaderInterface;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

class YmlReader implements EnvReaderInterface
{

    /**
     * @param string $file
     * @return \stdClass
     * @throws \RuntimeException
     */
    public function run($file)
    {
        $symfonyYamlClass = 'Symfony\Component\Yaml\Yaml';
        if (! class_exists($symfonyYamlClass)) {
            throw new RuntimeException("Yaml reader not avaliable, {$symfonyYamlClass} must installed for it. Try to run `composer require symfony/yaml`", 500);
        }
        return json_decode(json_encode(Yaml::parse(file_get_contents($file))));
    }
}
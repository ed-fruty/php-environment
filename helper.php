<?php
if (! function_exists('envGet')) {
    function envGet($key = null, $default = null)
    {
        return \Fruty\Environment\Env::instance()->get($key, $default);
    }
}

if (! function_exists('env_get')) {
    function env_get($key = null, $default = null)
    {
        return \Fruty\Environment\Env::instance()->get($key, $default);
    }
}
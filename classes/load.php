<?php
define("CLASSES", __DIR__.DIRECTORY_SEPARATOR);
define("TEMPLATES", dirname(__DIR__).DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR);
define("CACHE", dirname(__DIR__).DIRECTORY_SEPARATOR."cache".DIRECTORY_SEPARATOR);

spl_autoload_register(function ($className): bool {
    $filename = CLASSES.str_replace("\\", "/", $className).".php";

    if (file_exists($filename)) {
        /** @noinspection PhpIncludeInspection */
        require($filename);

        return true;
    } else {
        return false;
    }
}, true, false);

if (!is_dir(CACHE) || !file_exists(CACHE)) {
    mkdir(CACHE);
}
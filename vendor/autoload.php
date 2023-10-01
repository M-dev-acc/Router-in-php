<?php

spl_autoload_extensions('.php');
spl_autoload_register(static function ($className) {
    $autoloadFileExtension = spl_autoload_extensions();
    $filteredClassName = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    $relativePath = dirname(__DIR__);
    $fileName = $relativePath . DIRECTORY_SEPARATOR . $filteredClassName . $autoloadFileExtension;

    if (!is_readable($fileName)) {
        throw new Exception("Unable to load $filteredClassName");
    }
    // var_dump($fileName);
    require_once($fileName);
});


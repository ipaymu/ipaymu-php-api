<?php
/**
 * Copyright (c) 2019.
 */
spl_autoload_register(function ($class) {
    $file = __DIR__.'/../src/'.strtr($class, '\\', '/').'.php';
    if (file_exists($file)) {
        require $file;

        return true;
    }

    return true;
});

<?php
/**
 * @package aduh95/HTMLGenerator
 * @license MIT
 */
if(!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50601)
    exit('This librairy (aduh95/html-generator) is not compatible with your current version of PHP, it requires PHP 5.6.1 or higher.'."\n");


spl_autoload_register(function ($class) {
    if (substr($class, 0, 21)==='aduh95\\HTMLGenerator\\') {
        $class = substr($class, 21);
        $file = __DIR__.DIRECTORY_SEPARATOR.$class.'.php';

        if (is_readable($file))
            include $file;
    }
}, true, true);

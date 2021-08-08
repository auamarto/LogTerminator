<?php

require dirname(__DIR__).'/vendor/autoload.php';
require dirname(__DIR__).'/src/LogCleaner.php';
require dirname(__DIR__).'/src/LogManager.php';
require dirname(__DIR__).'/src/FileLogManager.php';

if (!array_key_exists('APP_ENV', $_SERVER)) {
    $_SERVER['APP_ENV'] = $_ENV['APP_ENV'] ?? null;
}
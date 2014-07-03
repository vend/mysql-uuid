<?php

if (!is_dir('build')) {
    mkdir('build');
}

$loader = require __DIR__ . '/../vendor/autoload.php';

// Load the tests directory into the main namespace
$loader->add('MysqlUuid\\', __DIR__);

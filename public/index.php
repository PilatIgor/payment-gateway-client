<?php

$appDir = dirname(__DIR__);

require $appDir . '/vendor/autoload.php';

$env = new Symfony\Component\Dotenv\Dotenv;
$env->load($appDir . '/.env');





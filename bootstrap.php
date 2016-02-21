<?php

date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . '/vendor/autoload.php';

use Silex\Application;
use DOLucas\Concurso\Provider\ResourceProvider;

$app = new Application();

$app->register(new ResourceProvider());

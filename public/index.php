<?php

require_once __DIR__ . '/../bootstrap.php';

use DOLucas\Concurso\Service\Teste;
use DOLucas\Concurso\Service\ConcursoService;
use DOLucas\Concurso\Service\ReceiverService;
use DOLucas\Concurso\Mapper\ReceiverMapper;
use Symfony\Component\HttpFoundation\JsonResponse;

$app['debug'] = false;

$app['service.concurso'] = function() use ($app) {
	return new ConcursoService($app['urls']());
};

$app['service.receiver'] = function() use ($app) {
    $receiverMapper = new ReceiverMapper(__DIR__ . '/../storage/data.json');
    return new ReceiverService($app['service.concurso'], $receiverMapper);
};

/**
 * Notificação de concursos abertos por e-mail
 */
$app->get('/notify', function () use ($app) {
    $emails = $app['service.receiver']->notify();

    return new JsonResponse([
        'success' => true,
        'message' => 'Alertas enviados com sucesso',
    	'sent_at' => new DateTime(),
        'emails' => $emails
    ]);
});

/**
 * API JSON de concursos
 */
$app->get('/api/concursos/{status}', function ($status) use ($app) {
    $concursos = $app['service.concurso']->getConcursos($status);

    return new JsonResponse($concursos);
});

$app->run();

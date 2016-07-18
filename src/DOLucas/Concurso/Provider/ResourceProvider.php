<?php

namespace DOLucas\Concurso\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class ResourceProvider implements ServiceProviderInterface
{

    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['urls'] = $app->protect(function () {
            $data = file_get_contents(__DIR__ . '/../Resource/urls.json');
            return json_decode($data, true);
        });
    }

    /**
     * @param Application $app
     */
    public function boot(Application $app)
    {

    }
}
